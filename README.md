# Invoice Template Generator

A professional, modern invoice template generator built with Laravel and Tailwind CSS. Create beautiful invoices with real-time preview and export functionality.

## Features

✨ **Modern Interface** - Clean, professional design with blue color scheme
📝 **Real-time Preview** - See your invoice as you type
🧮 **Automatic Calculations** - Tax, discount, and total calculations
📄 **Multiple Export Options** - Print and PDF download (PDF feature in development)
📱 **Responsive Design** - Works perfectly on desktop and mobile
⚡ **Fast & Intuitive** - Add/remove line items dynamically
💼 **Professional Layout** - Similar to popular invoice generators

## Technologies Used

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Tailwind CSS, Vanilla JavaScript
- **Icons**: Font Awesome 6
- **Database**: SQLite (default)

## Quick Start

1. **Clone and Install**
   ```bash
   git clone <your-repo-url>
   cd invoice-template
   composer install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Run the Application**
   ```bash
   php artisan serve
   ```

4. **Open in Browser**
   Visit `http://localhost:8000` to start creating invoices!

## How to Use

1. **Fill Company Details** - Add your company information in the "From" section
2. **Add Client Info** - Fill in your client's details in the "Bill To" section  
3. **Add Line Items** - Click "Add Item" to add products/services
4. **Set Tax & Discount** - Configure tax rate and discount percentage
5. **Preview & Export** - Use the real-time preview and export options

## Features in Detail

### Invoice Information
- Invoice number (auto-generated)
- Issue date and due date
- Company and client details
- Contact information

### Line Items
- Dynamic item addition/removal
- Description, quantity, rate fields
- Automatic total calculation per item
- Real-time subtotal updates

### Calculations
- Subtotal calculation
- Percentage-based discounts
- Tax calculations
- Grand total with all adjustments

### Export Options
- **Print**: Direct browser printing
- **PDF Download**: Server-side PDF generation (in development)
- **Preview**: Real-time invoice preview

## Development

### Project Structure
```
app/Http/Controllers/
├── InvoiceController.php    # Main invoice logic

resources/views/
├── invoice/
    └── generator.blade.php  # Main invoice template

routes/
├── web.php                 # Application routes
```

### Adding PDF Generation

To add PDF generation capability:

1. Install a PDF library like DomPDF:
   ```bash
   composer require barryvdh/laravel-dompdf
   ```

2. Update the `InvoiceController::generatePdf()` method to generate actual PDFs

3. Create a PDF template view for proper formatting

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b new-feature`
3. Commit changes: `git commit -am 'Add new feature'`
4. Push to branch: `git push origin new-feature`
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

If you encounter any issues or have questions, please open an issue on GitHub.

---

**Created with ❤️ using Laravel & Tailwind CSS**
