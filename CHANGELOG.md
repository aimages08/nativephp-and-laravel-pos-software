# Changelog

All notable changes to this project will be documented here.

Format based on [Keep a Changelog](https://keepachangelog.com/).

## [1.0.0] - 2026-09-30

### Added
- Initial release
- POS screen with barcode/PLU search
- Products with SKU, barcode, PLU, expiry
- Categories management
- Customers with credit (khata)
- Customer payments + printable statements
- Suppliers + purchases
- Expense tracking
- Stock adjustments (wastage, damage, loss, count)
- Sales history + refund
- Reports (sales, profit, top products, stock)
- Thermal receipt printing
- Barcode label printing
- Dashboard with 7-day sales chart
- Backups (local + Google Drive folder)
- Soft delete + Trash
- Login system
- NativePHP desktop build (Windows .exe)

### Fixed
- Tax calculation (percent vs flat)
- Session persistence in Electron
- Pagination in all list views
- Category delete guard (blocks if has products)
- Supplier delete guard
- Customer delete guard (blocks if has balance)
- Balance calculation for customer khata

## [Unreleased]

### Planned
- Product image upload
- A4 print reports
- Day-end / shift report
- Hold / park sale
- User roles (Admin / Cashier)
- Multi-store support