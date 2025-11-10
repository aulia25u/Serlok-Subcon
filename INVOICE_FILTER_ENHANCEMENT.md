# Invoice Monitoring Filter Enhancement

## Overview
Enhanced the invoice data filter system to provide more flexible and user-friendly filtering options.

## Changes Made

### 1. Filter Layout Redesign

#### Before:
- Customer (col-md-2)
- Month (col-md-1) - Abbreviated names (Jan, Feb, etc.)
- Year (col-md-1)
- No Invoice (col-md-2) - Text input
- Supplier (col-md-2) - Text input
- Item (col-md-2) - Text input
- Search Button (col-md-2)

#### After:
- Customer (col-md-3) - No change in functionality
- Month (col-md-2) - **Full month names** (January, February, etc.)
- Year (col-md-2) - No change in functionality
- Keyword (col-md-2) - **New Select2 dropdown**
- Value (col-md-2) - **New text input for keyword value**
- Search Button (col-md-1)

### 2. Keyword Filter System

#### Available Keywords:
The keyword dropdown includes all major invoice data fields:

1. **Process ID** - Search by ProcessID
2. **Nomor Invoice** - Search by invoice number
3. **Tanggal Struk** - Search by receipt date
4. **Supplier** - Search by supplier/store name
5. **Items** - Search by item name
6. **Jumlah** - Search by quantity (exact match)
7. **Satuan** - Search by unit
8. **Harga Satuan** - Search by unit price (exact match)
9. **Payment** - Search by payment type (exact match: 0=Cash, 1=Tunda)
10. **Keterangan** - Search by notes/remarks

#### Search Behavior:
- **Text fields**: Uses LIKE search (partial match)
  - Process ID, Nomor Invoice, Tanggal Struk, Supplier, Items, Satuan, Keterangan
- **Numeric fields**: Uses exact match
  - Jumlah, Harga Satuan, Payment

### 3. Files Modified

#### Frontend:
**File**: `resources/views/rbac/invoice-monitoring/index.blade.php`

Changes:
- Updated filter form layout with new column sizes
- Changed month dropdown from abbreviated to full names
- Replaced individual filter inputs (invoice_number, supplier, item) with keyword system
- Added Select2 initialization for keyword dropdown
- Updated JavaScript to use keyword and keyword_value parameters
- Updated info alert message for invoice history

#### Backend:
**File**: `app/Http/Controllers/RBAC/InvoiceMonitoringController.php`

Changes in `getData()` method:
- Removed individual filter parameters (invoice_number, supplier, item)
- Added keyword and keyword_value parameter handling
- Implemented column mapping for keyword to database fields
- Added logic for text vs numeric field filtering

Changes in `getHistory()` method:
- Updated filter detection to use keyword parameters
- Applied same keyword filtering logic to invoice query
- Maintains ProcessID-based join with history table

### 4. Column Mapping

```php
$columnMap = [
    'process_id' => 'ProcessID',
    'nomor_invoice' => 'Nomor_Invoice',
    'tanggal_struk' => 'Tanggal_Struk',
    'supplier' => 'Toko',
    'items' => 'Items',
    'jumlah' => 'Jumlah',
    'satuan' => 'Satuan',
    'harga_satuan' => 'Harga_Satuan',
    'payment' => 'Payment',
    'keterangan' => 'Keterangan',
];
```

## Benefits

1. ✅ **More Flexible**: Users can search any field dynamically
2. ✅ **Cleaner UI**: Reduced number of input fields, more organized layout
3. ✅ **Better UX**: Full month names are more readable
4. ✅ **Consistent**: Same filtering logic applied to both invoice data and history
5. ✅ **Extensible**: Easy to add new searchable fields in the future

## Usage Examples

### Example 1: Search by Supplier
1. Select "Supplier" from Keyword dropdown
2. Enter supplier name in Value field (e.g., "Tokopedia")
3. Click Search
4. Results show all invoices from that supplier

### Example 2: Search by Payment Type
1. Select "Payment" from Keyword dropdown
2. Enter "0" for Cash or "1" for Tunda in Value field
3. Click Search
4. Results show invoices with that payment type

### Example 3: Combined Filters
1. Select Customer: "PT ABC"
2. Select Month: "January"
3. Select Year: "2025"
4. Select Keyword: "Items"
5. Enter Value: "Laptop"
6. Click Search
7. Results show all laptop items from PT ABC in January 2025

## Technical Notes

### Select2 Integration
- Customer dropdown: Uses existing Select2 with `.select2` class
- Keyword dropdown: Uses new Select2 with `.select2-keyword` class
- Both have placeholder and allowClear options enabled

### Filter Parameters
- **Old**: `invoice_number`, `supplier`, `item`
- **New**: `keyword`, `keyword_value`

### Backward Compatibility
- Old filter parameters are completely replaced
- No backward compatibility needed as this is an enhancement

## Implementation Date
- January 2025

## Status
✅ **COMPLETED** - Filter system enhanced with keyword-based search
