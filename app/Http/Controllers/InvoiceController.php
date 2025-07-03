<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(): View
    {
        $currencies = config('currencies');
        return view('invoice.generator', compact('currencies'));
    }

    public function generatePdf(Request $request): Response
    {
        $currencies = config('currencies');
        $currencyCodes = array_keys($currencies);
        
        $request->validate([
            'invoice_number' => 'required|string',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'from_name' => 'required|string',
            'to_name' => 'required|string',
            'currency' => 'required|string|in:' . implode(',', $currencyCodes),
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.rate' => 'required|numeric|min:0',
        ]);

        // Calculate totals
        $subtotal = 0;
        $items = [];
        
        foreach ($request->items as $item) {
            $quantity = (float) $item['quantity'];
            $rate = (float) $item['rate'];
            $amount = $quantity * $rate;
            $subtotal += $amount;
            
            $items[] = [
                'description' => $item['description'],
                'quantity' => $quantity,
                'rate' => $rate,
                'amount' => $amount
            ];
        }

        $taxRate = (float) ($request->tax_rate ?? 0);
        $taxAmount = $subtotal * ($taxRate / 100);
        
        $discount = (float) ($request->discount ?? 0);
        $shipping = (float) ($request->shipping ?? 0);
        
        $total = $subtotal + $taxAmount - $discount + $shipping;
        $amountPaid = (float) ($request->amount_paid ?? 0);
        $balanceDue = $total - $amountPaid;

        $currency = $request->currency;
        $currencySymbol = $currencies[$currency]['symbol'] ?? '$';

        $data = [
            'invoice_number' => $request->invoice_number,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'currency' => $currency,
            'currency_symbol' => $currencySymbol,
            'from' => [
                'name' => $request->from_name,
                'email' => $request->from_email,
                'address' => $request->from_address,
            ],
            'to' => [
                'name' => $request->to_name,
                'email' => $request->to_email,
                'address' => $request->to_address,
            ],
            'items' => $items,
            'subtotal' => $subtotal,
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => $total,
            'amount_paid' => $amountPaid,
            'balance_due' => $balanceDue,
            'notes' => $request->notes,
            'terms' => $request->terms,
        ];

        $pdf = Pdf::loadView('invoice.pdf', $data);
        
        return $pdf->download('invoice-' . $request->invoice_number . '.pdf');
    }
} 