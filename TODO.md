# TODO List for Master Customer Tenant Owner Fix

## Completed Tasks
- [x] Modified MasterCustomerController index method to pass currentTenantOwner for non-internal users
- [x] Updated MasterCustomerController store method to force tenant_id for non-internal users
- [x] Updated MasterCustomerController update method to force tenant_id for non-internal users
- [x] Modified add modal in master_customer/index.blade.php to disable tenant_id select for non-internal users and pre-fill with current tenant owner
- [x] Modified edit modal in master_customer/index.blade.php to disable tenant_id select for non-internal users and pre-fill with current tenant owner
- [x] Updated JavaScript to skip loading tenant owners for non-internal users in add modal
- [x] Updated JavaScript to skip loading tenant owners for non-internal users in edit modal

## Next Steps
- [ ] Test the changes by logging in as a non-admin tenant owner and verifying the tenant_id field is disabled and pre-filled
- [ ] Test the changes by logging in as an admin and verifying the tenant_id field is editable
- [ ] Ensure that create and edit operations work correctly for both user types
- [ ] Check for any edge cases, such as when no tenant owner exists for the customer
