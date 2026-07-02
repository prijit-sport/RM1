# TODO - Maintenance Refactor

- [ ] Update app/Http/Requests/StoreMaintenanceRequest.php
  - [ ] maintenance_type -> issue_type
  - [ ] request_date -> reported_date

- [ ] Update app/Http/Requests/UpdateMaintenanceRequest.php
  - [ ] maintenance_type -> issue_type
  - [ ] request_date -> reported_date

- [ ] Update app/Http/Controllers/MaintenanceController.php
  - [ ] filter/query maintenance_type -> issue_type
  - [ ] remove unneeded mapping and update request_date -> reported_date everywhere
  - [ ] ensure create/update store works with schema (issue_type, reported_date, completed_date)

- [ ] Update resources/views/maintenances/create.blade.php
  - [ ] name="maintenance_type" -> name="issue_type"
  - [ ] name="request_date" -> name="reported_date"

- [ ] Update resources/views/maintenances/edit.blade.php
  - [ ] name="maintenance_type" -> name="issue_type"
  - [ ] name="request_date" -> name="reported_date"

- [ ] Update resources/views/maintenances/index.blade.php
  - [ ] maintenance_type -> issue_type
  - [ ] request_date -> reported_date
  - [ ] completion_date -> completed_date

- [ ] Update resources/views/maintenances/show.blade.php
  - [ ] maintenance_type -> issue_type
  - [ ] request_date -> reported_date

- [ ] Update tests/Feature/MaintenanceControllerTest.php
  - [ ] payload maintenance_type -> issue_type
  - [ ] payload request_date -> reported_date
  - [ ] rename/re-align the test for valid data and assert redirect + record count

- [ ] Run php artisan test (stop immediately if any fail)
