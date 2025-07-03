<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-600">
                    <i class="fas fa-receipt mr-3"></i>
                    Invoice Generator
                </h1>
                <p class="text-gray-600 mt-2">Create professional invoices quickly and easily</p>
            </div>

            <form id="invoiceForm" method="POST" action="/generate-pdf" class="max-w-6xl mx-auto">
                @csrf
                
                <!-- Basic Invoice Info -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number</label>
                        <input type="text" name="invoice_number" value="INV-{{ date('Y') }}-001" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                        <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" onchange="updateCurrencySymbols()">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}" {{ $code === 'USD' ? 'selected' : '' }}>
                                    {{ $code }} ({{ $currency['symbol'] }}) - {{ $currency['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Due Date</label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>

                <!-- From and To sections -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- From -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">From (Your Company)</h3>
                        <div class="space-y-3">
                            <input type="text" name="from_name" placeholder="Company Name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                            <input type="email" name="from_email" placeholder="company@email.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <textarea name="from_address" rows="3" placeholder="Company Address"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>

                    <!-- To -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3 text-gray-800">Bill To (Client)</h3>
                        <div class="space-y-3">
                            <input type="text" name="to_name" placeholder="Client Name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                            <input type="email" name="to_email" placeholder="client@email.com"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <textarea name="to_address" rows="3" placeholder="Client Address"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <div class="bg-gray-800 text-white px-4 py-3 rounded-t-lg">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm font-medium">
                            <div class="md:col-span-1">Item</div>
                            <div class="text-center">Quantity</div>
                            <div class="text-center">Rate</div>
                            <div class="text-center">Amount</div>
                        </div>
                    </div>
                    
                    <div id="items-container" class="border-l border-r border-gray-200">
                        <!-- Items will be added here -->
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 border-l border-r border-b border-gray-200">
                        <button type="button" onclick="addItem()" class="text-blue-600 hover:text-blue-800 font-medium">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>
                </div>

                <!-- Calculations and Details -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Notes and Terms -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="4" placeholder="Notes - any relevant information not already covered"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Terms</label>
                            <textarea name="terms" rows="4" placeholder="Terms and conditions - late fees, payment methods, delivery schedule"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>

                    <!-- Calculations -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="space-y-3">
                            <!-- Subtotal -->
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Subtotal</span>
                                <span class="font-medium" id="subtotal">$0.00</span>
                            </div>

                            <!-- Tax -->
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Tax</span>
                                <div class="flex items-center space-x-2">
                                    <input type="number" name="tax_rate" value="0" min="0" max="100" step="0.1" 
                                           class="w-16 px-2 py-1 border border-gray-300 rounded text-sm text-center" 
                                           onchange="calculateTotals()">
                                    <span class="text-gray-500">%</span>
                                    <button type="button" onclick="calculateTotals()" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Discount -->
                            <div class="flex justify-between items-center">
                                <button type="button" onclick="toggleDiscount()" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-plus mr-1"></i>Discount
                                </button>
                                <div id="discount-section" class="hidden">
                                    <input type="number" name="discount" value="0" min="0" step="0.01" 
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center" 
                                           onchange="calculateTotals()">
                                </div>
                            </div>

                            <!-- Shipping -->
                            <div class="flex justify-between items-center">
                                <button type="button" onclick="toggleShipping()" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-plus mr-1"></i>Shipping
                                </button>
                                <div id="shipping-section" class="hidden">
                                    <input type="number" name="shipping" value="0" min="0" step="0.01" 
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center" 
                                           onchange="calculateTotals()">
                                </div>
                            </div>

                            <!-- Total -->
                            <div class="flex justify-between items-center border-t pt-3 text-lg font-semibold">
                                <span>Total</span>
                                <span id="total">$0.00</span>
                            </div>

                            <!-- Amount Paid -->
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Amount Paid</span>
                                <div class="flex items-center">
                                    <span class="text-gray-500 mr-1 currency-symbol">$</span>
                                    <input type="number" name="amount_paid" value="0" min="0" step="0.01" 
                                           class="w-20 px-2 py-1 border border-gray-300 rounded text-sm text-center" 
                                           onchange="calculateTotals()">
                                </div>
                            </div>

                            <!-- Balance Due -->
                            <div class="flex justify-between items-center text-lg font-semibold text-blue-600">
                                <span>Balance Due</span>
                                <span id="balance-due">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-md hover:bg-blue-700 text-lg font-semibold">
                        <i class="fas fa-download mr-2"></i>
                        Generate & Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = 0;
        let currencySymbols = @json(collect($currencies)->mapWithKeys(function($currency, $code) {
            return [$code => $currency['symbol']];
        }));

        // Add first item on page load
        document.addEventListener('DOMContentLoaded', function() {
            addItem();
        });

        function getCurrentCurrency() {
            return document.querySelector('select[name="currency"]').value;
        }

        function getCurrencySymbol() {
            return currencySymbols[getCurrentCurrency()] || '$';
        }

        function updateCurrencySymbols() {
            const symbol = getCurrencySymbol();
            
            // Update all currency symbols in the interface
            document.querySelectorAll('.currency-symbol').forEach(element => {
                element.textContent = symbol;
            });
            
            // Update item totals display
            calculateTotals();
        }

        function addItem() {
            itemCount++;
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            const symbol = getCurrencySymbol();
            
            itemDiv.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 p-4 border-b border-gray-200';
            itemDiv.innerHTML = `
                <div class="md:col-span-1">
                    <input type="text" name="items[${itemCount}][description]" placeholder="Description of item/service..." 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm" required>
                </div>
                <div class="text-center">
                    <input type="number" name="items[${itemCount}][quantity]" value="1" min="1" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm text-center" 
                           onchange="updateItemTotal(${itemCount})" required>
                </div>
                <div class="text-center flex items-center">
                    <span class="text-gray-500 mr-1 currency-symbol">${symbol}</span>
                    <input type="number" name="items[${itemCount}][rate]" value="0" min="0" step="0.01" 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md text-sm text-center" 
                           onchange="updateItemTotal(${itemCount})" required>
                </div>
                <div class="text-center flex items-center justify-between">
                    <span class="item-total font-medium">${symbol}0.00</span>
                    <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800 ml-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(itemDiv);
            calculateTotals();
        }

        function removeItem(button) {
            const items = document.querySelectorAll('#items-container > div');
            if (items.length > 1) {
                button.closest('div.grid').remove();
                calculateTotals();
            }
        }

        function updateItemTotal(itemIndex) {
            const container = document.getElementById('items-container');
            const itemRow = container.children[itemIndex - 1];
            const quantity = parseFloat(itemRow.querySelector(`input[name="items[${itemIndex}][quantity]"]`).value) || 0;
            const rate = parseFloat(itemRow.querySelector(`input[name="items[${itemIndex}][rate]"]`).value) || 0;
            const total = quantity * rate;
            const symbol = getCurrencySymbol();
            
            itemRow.querySelector('.item-total').textContent = `${symbol}${total.toFixed(2)}`;
            calculateTotals();
        }

        function calculateTotals() {
            let subtotal = 0;
            const symbol = getCurrencySymbol();
            
            // Calculate subtotal from all items
            document.querySelectorAll('.item-total').forEach(totalSpan => {
                const value = parseFloat(totalSpan.textContent.replace(/[^\d.-]/g, '')) || 0;
                subtotal += value;
            });

            // Get tax rate
            const taxRate = parseFloat(document.querySelector('input[name="tax_rate"]').value) || 0;
            const taxAmount = subtotal * (taxRate / 100);

            // Get discount
            const discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;

            // Get shipping
            const shipping = parseFloat(document.querySelector('input[name="shipping"]').value) || 0;

            // Calculate total
            const total = subtotal + taxAmount - discount + shipping;

            // Get amount paid
            const amountPaid = parseFloat(document.querySelector('input[name="amount_paid"]').value) || 0;

            // Calculate balance due
            const balanceDue = total - amountPaid;

            // Update display
            document.getElementById('subtotal').textContent = `${symbol}${subtotal.toFixed(2)}`;
            document.getElementById('total').textContent = `${symbol}${total.toFixed(2)}`;
            document.getElementById('balance-due').textContent = `${symbol}${balanceDue.toFixed(2)}`;
        }

        function toggleDiscount() {
            const section = document.getElementById('discount-section');
            section.classList.toggle('hidden');
        }

        function toggleShipping() {
            const section = document.getElementById('shipping-section');
            section.classList.toggle('hidden');
        }

        // Auto-calculate when page loads
        setTimeout(calculateTotals, 100);
    </script>
</body>
</html> 