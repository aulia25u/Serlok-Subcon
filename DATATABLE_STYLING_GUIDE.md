# DataTable Global Styling Guide

## Overview
Styling DataTable yang telah diimplementasikan pada POS Report kini menjadi standard untuk semua table di project Fixo Dashboard.

## Features Implemented

### 1. **Navy Blue Header**
- Background: #001f3f
- Text: White
- Font: Bold
- No text wrapping (white-space: nowrap)

### 2. **Striped Rows**
- Odd rows: Light blue (#e3f2fd)
- Even rows: White
- Hover effect: Lighter blue (#bbdefb)

### 3. **Fixed Header**
- Header stays fixed during vertical scrolling
- Requires: DataTables FixedHeader 3.4.0

### 4. **Fixed Columns** (Optional)
- Left columns can be fixed during horizontal scrolling
- Requires: DataTables FixedColumns 3.3.3
- Striped colors maintained on fixed columns

### 5. **Server-Side Processing** (Optional)
- Lazy loading for large datasets
- Pagination options: 25, 50, 100, 200
- Loading spinner indicator

## Files Created

### 1. Global CSS File
**Location**: `public/css/datatable-custom.css`

Contains all styling rules for:
- Navy blue headers
- Striped rows
- Fixed header styling
- Fixed columns styling
- Hover effects
- Pagination styling
- Processing indicator

### 2. Config Update
**Location**: `config/adminlte.php`

Added plugins:
- `Datatables` (v1.13.7) - with custom CSS
- `DatatablesFixedHeader` (v3.4.0) - for fixed headers
- `DatatablesFixedColumns` (v3.3.3) - for fixed columns

**Version Compatibility:**
- DataTables 1.13.7 is compatible with FixedHeader 3.4.0 and FixedColumns 3.3.3
- Note: FixedColumns 5.0.0+ uses a different API and is NOT compatible with this implementation

## How to Use

### Basic DataTable (Striped + Navy Header)
```javascript
$('#myTable').DataTable({
    // Basic configuration
    // Styling is automatically applied via global CSS
});
```

### With Fixed Header
```javascript
$('#myTable').DataTable({
    fixedHeader: {
        header: true
    },
    scrollY: 500,
    scrollX: true,
    scrollCollapse: true
});
```

### With Fixed Columns
```javascript
$('#myTable').DataTable({
    fixedHeader: {
        header: true
    },
    fixedColumns: {
        leftColumns: 3  // Number of columns to fix from left
    },
    scrollY: 500,
    scrollX: true,
    scrollCollapse: true
});
```

### With Server-Side Processing
```javascript
$('#myTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('your.data.route') }}",
        type: 'GET'
    },
    columns: [
        { data: 0, name: 'column1' },
        { data: 1, name: 'column2' },
        // ... more columns
    ],
    fixedHeader: {
        header: true
    },
    scrollY: 500,
    scrollX: true,
    scrollCollapse: true,
    paging: true,
    pageLength: 25,
    lengthMenu: [[25, 50, 100, 200], [25, 50, 100, 200]],
    searching: false,
    ordering: false
});
```

## Existing Tables to Update

### Tables that need Fixed Columns:
Review these tables and determine which columns should be fixed:

1. **POS Monitoring Index** (`resources/views/rbac/pos-monitoring/index.blade.php`)
   - Consider fixing: Customer Name, Date columns

2. **Invoice Monitoring** (`resources/views/rbac/invoice-monitoring/index.blade.php`)
   - Consider fixing: Invoice Number, Customer columns

3. **Subscription** (`resources/views/rbac/subscription/index.blade.php`)
   - Consider fixing: Customer Name column

4. **Calendar Pitching** (`resources/views/rbac/calendar-pitching/index.blade.php`)
   - Consider fixing: Date, Customer columns

5. **MoM Customer** (`resources/views/rbac/mom-customer/index.blade.php`)
   - Consider fixing: Date, Customer columns

6. **Master Menu** (`resources/views/rbac/master-menu.blade.php`)
   - May not need fixed columns (fewer columns)

7. **User Data** (`resources/views/rbac/user-data.blade.php`)
   - Consider fixing: Name, Email columns

8. **History** (`resources/views/rbac/history.blade.php`)
   - Consider fixing: Date, User columns

### Tables with Many Columns (Priority for Fixed Columns):
- POS Report (Already implemented - 9 fixed columns)
- Invoice Monitoring (if has many columns)
- Subscription (if has many columns)

## Color Scheme

```css
/* Primary Colors */
Navy Blue Header: #001f3f
Border Color: #003366

/* Row Colors */
Odd Rows: #e3f2fd (Light Blue)
Even Rows: #ffffff (White)
Hover: #bbdefb (Lighter Blue)

/* Text */
Header Text: #ffffff (White)
Body Text: #333333 (Dark Gray)
```

## Best Practices

### 1. Fixed Columns Guidelines
- Fix 2-4 columns for tables with 10+ columns
- Fix columns that contain:
  - ID/Number
  - Name/Title
  - Date (if primary sorting field)
- Don't fix too many columns (max 30% of total columns)

### 2. Server-Side Processing
- Use for tables with 1000+ records
- Implement pagination
- Add loading indicators
- Disable client-side searching/ordering

### 3. Scroll Height
- Default: 500px (shows ~10 rows)
- Adjust based on page layout
- Consider: `$(window).height() - offset` for dynamic height

### 4. Performance
- Enable server-side processing for large datasets
- Use fixed columns only when necessary
- Minimize number of fixed columns

## Testing Checklist

When implementing on a new table:
- [ ] Navy blue header displays correctly
- [ ] Striped rows (blue/white) working
- [ ] Hover effect shows lighter blue
- [ ] Fixed header stays on top during scroll
- [ ] Fixed columns (if used) stay left during horizontal scroll
- [ ] Striped colors consistent on fixed columns
- [ ] Pagination working (if enabled)
- [ ] Loading indicator shows (if server-side)
- [ ] No console errors
- [ ] Responsive on different screen sizes

## Troubleshooting

### Header not navy blue
- Check if custom CSS is loaded
- Clear browser cache
- Run: `php artisan config:clear`

### Striped rows not showing
- Ensure table has class `dataTable`
- Check CSS specificity
- Verify no conflicting styles

### Fixed columns not working
- Verify FixedColumns plugin is loaded
- Check `fixedColumns` configuration
- Ensure `scrollX: true` is set

### Table overflow / breaking layout
**Fixed in v1.2** - CSS now includes:
- `.card-body { overflow-x: auto; overflow-y: visible; }`
- `.dataTables_wrapper { overflow-x: auto; }`
- Proper container width management
- If still having issues:
  - Clear browser cache
  - Check for conflicting CSS
  - Ensure `scrollX: true` in DataTable config

### Performance issues
- Implement server-side processing
- Reduce number of fixed columns
- Optimize data queries

## Support

For issues or questions:
1. Check this guide first
2. Review `public/css/datatable-custom.css`
3. Check browser console for errors
4. Verify plugin versions match

## Version History

- **v1.0** - Initial implementation on POS Report
- **v1.1** - Made global standard for all tables
- **v1.2** - Added comprehensive documentation
- **v1.3** - Fixed table overflow issues with proper container styling
- **v1.4** - Updated version compatibility (DataTables 1.13.7, FixedColumns 3.3.3)

---

**Last Updated**: 2025
**Maintained By**: Development Team

## Library Versions

Current versions used in this project:
- **DataTables Core**: 1.13.7
- **FixedHeader Extension**: 3.4.0
- **FixedColumns Extension**: 3.3.3

These versions are configured in `config/adminlte.php` and are compatible with each other.
