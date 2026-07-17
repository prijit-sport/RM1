# TODO

- [x] Update InvoiceController constructor to inject NotificationService
- [x] Refactor remindAll() to call NotificationService::sendBulkInvoiceReminders() and use returned sent count
- [x] Ensure success message uses returned count and failures are logged with error
- [ ] Run test suite (phpunit/phpstan) on environment (tools missing in PATH right now)
