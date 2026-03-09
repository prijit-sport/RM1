# TODO - Code Improvements

## ✅ Completed Fixes & Improvements
- [x] Contract `contractor_name` field error - Made field nullable
- [x] Contract `amount` field error - Made field nullable
- [x] Fix inconsistent pricing calculation - Changed BookingModel to use 30.44 (matching BookingService)
- [x] Add search functionality to RoomController - Added search, status, and room_type filters
- [x] Add Form Requests for InvoiceController - Created StoreInvoiceRequest and UpdateInvoiceRequest
- [x] Add TODO comment for remindAll() function
- [x] Add database indexes for performance - Added indexes to all major tables
- [x] Implement caching for dashboard data - Added 5-minute cache with user-specific keys
- [x] Add API routes - Created REST API with Sanctum authentication

## 🔴 High Priority Issues
- [ ] None remaining

## ⚠️ Medium Priority Improvements
- [ ] None remaining

## 📋 Low Priority / Future Enhancements
- [x] Add API routes (Sanctum installed)
- [ ] Set up local assets instead of CDN for Bootstrap
- [ ] Add role hierarchy support in User model
- [ ] Add more unit tests

## Analysis Summary

### Good Practices Found:
- Service Layer pattern (BookingService, ContractService, InvoiceService)
- Form Requests for validation
- Policies for authorization
- Database transactions
- SoftDeletes
- Audit logging
- Good test coverage
- Proper route grouping

### Issues Fixed:
1. **Contract `contractor_name` field** - Migration added to make nullable
2. **Contract `amount` field** - Migration added to make nullable
3. **Inconsistent pricing** - BookingModel now uses 30.44 divisor
4. **RoomController search** - Added search, status, and room_type filters
5. **Invoice Form Requests** - Created StoreInvoiceRequest and UpdateInvoiceRequest
6. **remindAll()** - Added TODO comment for future implementation
7. **Fallback route** - Added route to handle unknown URLs and redirect properly
8. **Migration issue** - Removed duplicate performance index migration (indexes already exist in earlier migrations)

