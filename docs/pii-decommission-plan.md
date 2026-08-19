# PII Decommission Plan

## Overview

This document outlines the strategy and timeline for safely removing plaintext PII columns (`email`, `id_number`) from the `guests` table. Currently, these columns are kept as a "safety net" while encrypted versions (`*_ciphertext`, `*_hash`) handle all read/write operations.

## Current State

As of TASK 4 (PII Encryption), the following encrypted columns were added to the `guests` table:

- `email_ciphertext` - Encrypted email address
- `email_hash` - SHA256 HMAC blind-index for exact email matching
- `id_number_ciphertext` - Encrypted ID number
- `id_number_hash` - SHA256 HMAC blind-index for exact ID matching

All application code has been migrated to use these encrypted columns through:
- Model accessors/mutators in `Guest::class`
- Hash-based unique validation in controllers
- PII-filtered queries in `GuestController::index()`

The plaintext columns remain as a backup/rollback mechanism. The audit command now also scans `database/seeders` and `database/factories` so fixture data and seeding code are covered alongside runtime application code.

## Conditions for Safe Deletion

Before removing the plaintext columns, ALL of the following conditions must be met:

### 1. Extended Stability Period (Minimum 4 weeks)

- The `verify-guest-pii-encryption` command must pass with 100% success for **at least 4 consecutive weeks**
- This ensures all data has been properly encrypted and the decryption path works reliably
- A weekly cron job should run this verification and log results

### 2. Zero Direct Plaintext Access in Production

- Run `audit-guest-pii-plaintext-usage` command in production environment
- All **legitimate** findings (not false positives) must be resolved or documented
- Examples of legitimate issues that would block deletion:
  - Any `.select()` query explicitly including `'email'` or `'id_number'` columns
  - Any `.pluck()` directly accessing plaintext columns
  - Any raw SQL queries referencing plaintext columns

### 3. Database Backup and Verification

- A full backup of the production database must be available **before** any column deletion
- The backup should be tested for restoration to ensure integrity

### 4. Testing Coverage

- All existing tests must pass without modification
- The test suite should cover:
  - Guest creation with PII data
  - Guest updates with PII data
  - PII-based searches (exact hash matching)
  - Export functionality
  - API authentication (User model uses different columns)

### 5. Rollback Plan Documented

- A documented rollback procedure must exist in case issues arise post-deletion
- This includes:
  - How to restore from backup
  - How to re-encrypt data if needed
  - Communication protocol for stakeholders

## Pre-Deletion Checklist

Before proceeding with the actual deletion migration, ensure:

- [ ] 4+ weeks of successful `verify-guest-pii-encryption` runs logged
- [ ] `audit-guest-pii-plaintext-usage` run on production with all findings resolved
- [ ] Database backup created and verified
- [ ] Full test suite (118+ tests) passing
- [ ] Rollback plan documented and tested (at least on staging)
- [ ] Team sign-off from project maintainers
- [ ] Stakeholders notified of maintenance window

## Code Items to Review/Fix Before Deletion

### Audit Command Methodology (IMPORTANT)

The `app:audit-guest-pii-plaintext-usage` command uses **heuristic pattern matching** (regex-based) to scan the codebase. This is a **static analysis tool**, NOT a semantic analyzer:

**What it does:**
- Searches for regex patterns in source code that may indicate direct plaintext column access
- Reports patterns like `$guest->email`, `['email']`, `.select('email')`, etc.
- Provides a comprehensive list of all pattern matches for manual review
- Scans runtime code plus `database/seeders` and `database/factories` for fixture and seeding coverage

**What it does NOT do:**
- Parse AST (Abstract Syntax Tree) to understand code semantics
- Determine whether a `$guest->email` access uses a model accessor/mutator or bypasses it
- Distinguish between safe and unsafe access patterns automatically
- Make security decisions — it reports potential issues only

**False Positive Risk:**
- Many findings are **intentional and safe** because Laravel's model accessors/mutators transparently decrypt data when you access properties
- Example: `$guest->email` in a Blade template triggers the `getEmailAttribute()` accessor, which decrypts the ciphertext — this is SAFE
- Example: `$guest->email` in the verification command is INTENTIONAL — it's comparing plaintext vs. ciphertext during validation

**Classification Note:**
The documentation below categorizes findings as "Safe" or "Review Required", but this is **manual interpretation** based on code knowledge, NOT automatic detection by the command. **A human must review and verify each finding before proceeding with deletion.**

Run the following command to identify potential plaintext column access:

```bash
php artisan app:audit-guest-pii-plaintext-usage
```

### Current Known Findings (as of last audit):

The latest audit, after expanding scan coverage to `database/seeders` and `database/factories`, still detected 28 potential plaintext PII access patterns. No additional real findings were introduced by the new directories. Below is the manual categorization based on code review:

**Complete List of 28 Findings:**

1. **MigrateGuestPiiEncryption.php:48** - `email` - Pattern: Direct property access
   - Line: `'email_ciphertext' => encrypt($guest->email),`
   - **Assessment:** SAFE - This is the migration command that intentionally reads plaintext to create ciphertext during initial encryption setup

2. **MigrateGuestPiiEncryption.php:49** - `email` - Pattern: Direct property access
   - Line: `'email_hash' => hash_hmac('sha256', $guest->email,`
   - **Assessment:** SAFE - Same migration command, intentionally creating hash for blind-index

3. **MigrateGuestPiiEncryption.php:50** - `id_number` - Pattern: Direct property access
   - Line: `'id_number_ciphertext' => encrypt($guest->id_number),`
   - **Assessment:** SAFE - Same migration command

4. **MigrateGuestPiiEncryption.php:51** - `id_number` - Pattern: Direct property access
   - Line: `'id_number_hash' => hash_hmac('sha256', $guest->id_numbe...`
   - **Assessment:** SAFE - Same migration command

5. **VerifyGuestPiiEncryption.php:47** - `id_number` - Pattern: Direct property access
   - Line: `$emailDecrypted = ! empty($guest->email_ciphertext) ? decrypt($g...`
   - **Assessment:** SAFE - Verification command that intentionally compares plaintext vs. ciphertext to ensure encryption consistency

6. **VerifyGuestPiiEncryption.php:48** - `email` - Pattern: Direct property access
   - Line: `$emailHashComputed = hash_hmac('sha256', (string) $guest->email,...`
   - **Assessment:** SAFE - Same verification command

7. **VerifyGuestPiiEncryption.php:49** - `email` - Pattern: Direct property access
   - Line: `$emailMatch = ($emailDecrypted === $guest->email)`
   - **Assessment:** SAFE - Same verification command

8. **VerifyGuestPiiEncryption.php:50** - `id_number` - Pattern: Direct property access
   - Line: `&& hash_equals($emailHashComputed, (string) $guest->email_ha...`
   - **Assessment:** SAFE - Same verification command

9. **VerifyGuestPiiEncryption.php:57** - `id_number` - Pattern: Direct property access
   - Line: `$idNumberDecrypted = ! empty($guest->id_number_ciphertext) ? dec...`
   - **Assessment:** SAFE - Same verification command

10. **VerifyGuestPiiEncryption.php:58** - `id_number` - Pattern: Direct property access
    - Line: `$idNumberHashComputed = hash_hmac('sha256', (string) $guest->id_...`
    - **Assessment:** SAFE - Same verification command

11. **VerifyGuestPiiEncryption.php:59** - `id_number` - Pattern: Direct property access
    - Line: `$idNumberMatch = ($idNumberDecrypted === $guest->id_number)`
    - **Assessment:** SAFE - Same verification command

12. **VerifyGuestPiiEncryption.php:60** - `id_number` - Pattern: Direct property access
    - Line: `&& hash_equals($idNumberHashComputed, (string) $guest->id_nu...`
    - **Assessment:** SAFE - Same verification command

13. **AuthController.php:27** - `email` - Pattern: Array access to plaintext column
    - Line: `$user = User::where('email', $credentials['email'])->first();`
    - **Assessment:** SAFE - This is `User` model, not `Guest`. User model doesn't have PII encryption (separate concern). This is out of scope.

14. **AuthController.php:47** - `email` - Pattern: Array access to plaintext column
    - Line: `'email' => $credentials['email'],`
    - **Assessment:** SAFE - Same as above, User model context

15. **GuestController.php:179** - `id_number` - Pattern: Array access to plaintext column
    - Line: `'id_number' => $guestData['id_number'],`
    - **Assessment:** SAFE - This is request validation data ($guestData from form input), not accessing database column. Form input to model assignment is safe because model mutator will handle encryption.

16. **GuestController.php:282** - `email` - Pattern: Direct property access
    - Line: `$guest->email ?? '-',`
    - **Assessment:** SAFE - Export method accesses `$guest->email` which triggers the model accessor that decrypts from ciphertext

17. **GuestController.php:284** - `id_number` - Pattern: Direct property access
    - Line: `$guest->id_number ?? '-',`
    - **Assessment:** SAFE - Same export method, triggers model accessor

18. **guests/bulk-create.blade.php:74** - `id_number` - Pattern: Array access to plaintext column
    - Line: `value="{{ $row['id_number...`
    - **Assessment:** SAFE - Form input row data from request, not database column access

19. **guests/edit.blade.php:35** - `id_number` - Pattern: Direct property access
    - Line: `<input type="text" name="id_number" class="form-...`
    - **Assessment:** SAFE - Blade form that displays `$guest->id_number` in input field, triggers model accessor for decryption

20. **guests/edit.blade.php:48** - `email` - Pattern: Direct property access
    - Line: `<input type="email" name="email" class="form-con...`
    - **Assessment:** SAFE - Same form, triggers model accessor

21. **guests/index.blade.php:76** - `id_number` - Pattern: Direct property access
    - Line: `{{ $guest->id_number ?? '-' }}`
    - **Assessment:** SAFE - Blade template displaying guest data, triggers model accessor

22. **guests/show.blade.php:115** - `email` - Pattern: Direct property access
    - Line: `<div class="info-value">{{ $guest->email }}</div>`
    - **Assessment:** SAFE - Guest detail page, triggers model accessor

23. **guests/show.blade.php:125** - `id_number` - Pattern: Direct property access
    - Line: `<div class="info-value">{{ $guest->id_number }}</div>`
    - **Assessment:** SAFE - Guest detail page, triggers model accessor

24. **invoices/show.blade.php:888** - `email` - Pattern: Direct property access
    - Line: `@if (!empty($guest->email))`
    - **Assessment:** SAFE - Invoice display, triggers model accessor

25. **invoices/show.blade.php:891** - `email` - Pattern: Direct property access
    - Line: `<a href="mailto:{{ $guest->email }}"`
    - **Assessment:** SAFE - Invoice display, triggers model accessor

26. **invoices/show.blade.php:892** - `email` - Pattern: Direct property access
    - Line: `style="color:inherit;text-decoration:non...`
    - **Assessment:** SAFE - Invoice display, triggers model accessor (part of same email link)

27. **invoices/show.blade.php:895** - `id_number` - Pattern: Direct property access
    - Line: `@if (!empty($guest->id_number))`
    - **Assessment:** SAFE - Invoice display, triggers model accessor

28. **invoices/show.blade.php:898** - `id_number` - Pattern: Direct property access
    - Line: `<span class="doc-num" style="font-siz...`
    - **Assessment:** SAFE - Invoice display, triggers model accessor

### Audit Command Properties

**Read-Only Guarantee:**
The `app:audit-guest-pii-plaintext-usage` command is **100% read-only**:
- It only reads files using `Illuminate\Support\Facades\File::get()` 
- It performs regex pattern matching on file contents
- It builds an in-memory array of findings and displays results via `$this->table()`
- **Zero write operations** to disk, database, or application state
- Safe to run on production environment without risk of data modification

**Audit Command Source Code Location:**
[app/Console/Commands/AuditGuestPiiPlaintextUsageCommand.php](../app/Console/Commands/AuditGuestPiiPlaintextUsageCommand.php)

---

1. **Safe (False Positives)** - Do NOT block deletion:
   - `MigrateGuestPiiEncryption.php` - Migration command that intentionally accesses both plaintext and ciphertext
   - `VerifyGuestPiiEncryption.php` - Verification command that intentionally accesses both columns
   - View files (`.blade.php`) using `$guest->email` or `$guest->id_number` - These use model accessors which are safe
   - Controllers using hash-based lookups - These are intentionally using hash columns, not plaintext

2. **Review Required** - Before deletion:
   - Any new code added after the audit that directly selects plaintext columns
   - Any reports or exports that might need plaintext data
   - Any database views or triggers referencing plaintext columns

## Deletion Steps (Future)

When all conditions are met, the actual decommissioning will involve:

### Step 1: Create Migration (NEW)
```php
// database/migrations/2026_XX_XX_XXXXXX_drop_guest_pii_plaintext_columns.php
Schema::table('guests', function (Blueprint $table) {
    $table->dropColumn(['email', 'id_number']);
});
```

### Step 2: Update Guest Model
- Remove comments about safety net
- Update `$fillable` array (already excludes plaintext in favor of ciphertext)
- Update documentation

### Step 3: Verification Steps
- Run full test suite: `php artisan test`
- Run audit command: `php artisan app:audit-guest-pii-plaintext-usage` (should have zero findings)
- Manual smoke test in production environment
- Monitor error logs for decryption failures

### Step 4: Deployment
- Deploy to staging first
- Run tests and audits
- If successful, deploy to production during maintenance window
- Keep backup available for 30 days post-deletion

### Step 5: Post-Deletion Cleanup
- Remove migration backup files
- Update this plan document
- Add entry to changelog/release notes

## Rollback Procedure

If issues arise after deletion:

1. **Immediate Action**: Restore from database backup
   ```bash
   # Depends on your backup system (mysqldump, AWS RDS, etc.)
   ```

2. **Application Rollback**: Redeploy previous version
   - All existing code continues to work with both plaintext and ciphertext

3. **Investigation**: Check error logs for:
   - Decryption failures (malformed ciphertext)
   - Hash computation mismatches
   - Data inconsistencies

4. **Re-validation**: Run verification commands
   ```bash
   php artisan app:verify-guest-pii-encryption
   php artisan app:audit-guest-pii-plaintext-usage
   ```

## Timeline Estimate

- **Week 1-2**: Stability monitoring (run verification daily)
- **Week 3-4**: Final reviews and testing on staging
- **Week 4+**: Obtain team approval
- **Week 5**: Perform deletion in production (during maintenance window)

**Earliest Safe Deletion Date**: 4 weeks after encryption deployment

## References

- TASK 4 (PII Encryption Implementation) - Detailed technical implementation
- [Guest Model Accessor/Mutator Code](../app/Models/Guest.php)
- [GuestController PII Search Implementation](../app/Http/Controllers/GuestController.php)
- [PII Verification Command](../app/Console/Commands/VerifyGuestPiiEncryption.php)
- [PII Audit Command](../app/Console/Commands/AuditGuestPiiPlaintextUsageCommand.php)
