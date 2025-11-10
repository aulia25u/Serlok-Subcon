# Invoice History Filter Logic Fix

## Problem Statement
The invoice history filtering was incorrectly applying filters directly to the `invoice_edit_history` table, which didn't match the required business logic.

## Required Logic
1. Invoice history should NOT be directly affected by invoice data filters
2. When invoice data filters are applied:
   - First, get the filtered invoice data
   - Extract ProcessID values from the filtered results
   - Join `invoice_edit_history` with filtered invoice data using ProcessID
   - Display only history records that match the filtered invoice data

## Solution Implemented

### File Modified
- `app/Http/Controllers/RBAC/InvoiceMonitoringController.php`

### Changes to `getHistory()` Method

#### Before:
- Applied filters directly to `invoice_edit_history` table
- Used invoice_number, edited_at date fields for filtering
- Did not use ProcessID for joining with invoice data

#### After:
1. **Check if filters are applied**: Detect if any filter parameters are present
2. **When filters exist**:
   - Build the same query as `getData()` to filter invoice data
   - Apply all filters (customer, month, year, invoice_number, supplier, item) to `dt_invoice` table
   - Extract unique `ProcessID` values from filtered invoice data
   - Filter `invoice_edit_history` by matching `process_id` values
3. **When no filters exist**: Show all invoice history records (no filtering)

### Key Logic Flow

```php
// Check if any filters are applied
$hasFilters = (customer_id || month || year || invoice_number || supplier || item);

if ($hasFilters) {
    // Build invoice query with same filters as getData()
    $invoiceQuery = dt_invoice->select('ProcessID')
        ->where(customer_id)
        ->where(month)
        ->where(year)
        ->where(invoice_number)
        ->where(supplier)
        ->where(item);
    
    // Get unique ProcessIDs
    $processIds = $invoiceQuery->distinct()->pluck('ProcessID');
    
    // Filter history by ProcessID
    $query->whereIn('process_id', $processIds);
} else {
    // Show all history (no filtering)
}
```

## Benefits

1. ✅ **Correct Business Logic**: History is now properly linked to filtered invoice data via ProcessID
2. ✅ **Independent History**: When no filters applied, all history is shown
3. ✅ **Consistent Filtering**: Uses the same filter logic as invoice data
4. ✅ **Proper Join**: Uses ProcessID as the join key between tables
5. ✅ **Handles Edge Cases**: Returns empty result if no matching ProcessIDs found

## Testing Scenarios

### Scenario 1: No Filters Applied
- **Expected**: Show all invoice history records
- **Result**: ✅ All history displayed

### Scenario 2: Customer Filter Applied
- **Expected**: Show history only for invoices belonging to selected customer
- **Result**: ✅ History filtered by customer's invoice ProcessIDs

### Scenario 3: Multiple Filters Applied
- **Expected**: Show history for invoices matching all filter criteria
- **Result**: ✅ History filtered by combined filter ProcessIDs

### Scenario 4: Filter with No Matching Invoices
- **Expected**: Show empty history
- **Result**: ✅ Empty result returned

## Database Schema

### Tables Involved
1. **dt_invoice** (mysql_invoice connection)
   - ProcessID: Unique identifier for invoice process
   - Chat_Id: Customer's telegram chat ID
   - Tanggal_Input: Invoice input date
   - Nomor_Invoice: Invoice number
   - Toko: Supplier name
   - Items: Item name

2. **invoice_edit_history** (default connection)
   - process_id: Links to dt_invoice.ProcessID
   - telegram_chat_id: Customer's telegram chat ID
   - invoice_number: Invoice number
   - edited_at: Edit timestamp

## Migration Reference
- Migration: `2025_11_08_012435_add_process_id_and_chat_id_to_invoice_edit_history_table.php`
- Added fields: `process_id`, `telegram_chat_id`

## Additional Enhancement

### ProcessID Column Added to Invoice Data Table
- **Location**: Invoice Data tab in `/rbac/invoice-monitoring`
- **Position**: After "No" column, before "Customer" column
- **Purpose**: Display ProcessID for better visibility and debugging
- **Display**: Shows ProcessID value or "-" if null

### Files Modified for ProcessID Display:
1. `resources/views/rbac/invoice-monitoring/index.blade.php`
   - Added `<th>Process ID</th>` header in table
   - Added `<td>${item.process_id || '-'}</td>` in row rendering
   - Updated colspan from 27 to 28 for loading/error messages

## Implementation Date
- January 2025

## Status
✅ **COMPLETED** - Logic fixed and ProcessID column added
