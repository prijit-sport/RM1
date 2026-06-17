<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int|null $actor_id
 * @property string $event
 * @property string|null $auditable_type
 * @property int|null $auditable_id
 * @property array<array-key, mixed>|null $meta
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $actor
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $auditable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * Booking Model
 *
 * @property int $id
 * @property int $guest_id
 * @property int $room_id
 * @property Carbon $check_in_date
 * @property Carbon|null $check_out_date
 * @property float $rent_amount
 * @property float $deposit_amount
 * @property float $total_price
 * @property int|null $electric_meter_start
 * @property int|null $water_meter_start
 * @property string $status (confirmed, cancelled)
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * Relations:
 * @property-read Guest $guest
 * @property-read Room $room
 * @property-read \Illuminate\Database\Eloquent\Collection<Invoice> $invoices
 * @property string|null $actual_check_in
 * @property string|null $actual_check_out
 * @property-read string $status_badge
 * @property-read string $status_label
 * @property-read int|null $invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereActualCheckIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereActualCheckOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCheckInDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCheckOutDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereDepositAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereElectricMeterStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereRentAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereTotalPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking whereWaterMeterStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Booking withoutTrashed()
 */
	class Booking extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $contractor_name
 * @property string $contract_number
 * @property string|null $title
 * @property \Illuminate\Support\Carbon|null $contract_date
 * @property string|null $landlord_name
 * @property string|null $landlord_id_number
 * @property string|null $landlord_phone
 * @property string|null $landlord_address
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property string|null $duration
 * @property numeric|null $monthly_rent
 * @property string|null $monthly_rent_text
 * @property numeric|null $deposit
 * @property numeric|null $advance_payment
 * @property int|null $advance_payment_months
 * @property int|null $due_date_day
 * @property numeric|null $electricity_rate
 * @property numeric|null $water_rate
 * @property numeric|null $late_fee
 * @property string|null $other_fees
 * @property string|null $terms
 * @property string|null $tenant_signature
 * @property string|null $landlord_signature
 * @property string|null $witness_signature
 * @property \Illuminate\Support\Carbon|null $tenant_sign_date
 * @property \Illuminate\Support\Carbon|null $landlord_sign_date
 * @property \Illuminate\Support\Carbon|null $witness_sign_date
 * @property numeric|null $amount
 * @property string $status
 * @property string|null $notes
 * @property int|null $photo_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $room_id
 * @property int|null $guest_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string $status_label
 * @property-read \App\Models\Guest|null $guest
 * @property-read \App\Models\Room|null $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract expired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereAdvancePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereAdvancePaymentMonths($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContractDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContractNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContractorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDeposit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDueDateDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereElectricityRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLandlordAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLandlordIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLandlordName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLandlordPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLandlordSignDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLandlordSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereLateFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereMonthlyRent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereMonthlyRentText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereOtherFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract wherePhotoCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereTenantSignDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereTenantSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereWaterRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereWitnessSignDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereWitnessSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract withoutTrashed()
 */
	class Contract extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $room_id
 * @property string $name
 * @property string $type
 * @property string $location
 * @property string|null $description
 * @property string $status
 * @property string|null $maintenance_schedule
 * @property \Illuminate\Support\Carbon|null $last_maintenance_date
 * @property \Illuminate\Support\Carbon|null $next_maintenance_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Maintenance> $maintenances
 * @property-read int|null $maintenances_count
 * @property-read \App\Models\Room|null $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereLastMaintenanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereMaintenanceSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereNextMaintenanceDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Facility whereUpdatedAt($value)
 */
	class Facility extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string|null $address
 * @property string|null $city
 * @property string|null $country
 * @property string|null $nationality
 * @property string|null $emergency_contact
 * @property string|null $emergency_phone
 * @property string|null $notes
 * @property string $id_number
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Contract|null $activeContract
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Booking> $bookings
 * @property-read int|null $bookings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @property-read \App\Models\Room|null $currentRoom
 * @property-read int|null $age
 * @property-read string $full_name
 * @property-read bool $has_room
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invoice> $invoices
 * @property-read int|null $invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereEmergencyContact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereEmergencyPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereIdNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereNationality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Guest withoutTrashed()
 */
	class Guest extends \Eloquent {}
}

namespace App\Models{
/**
 * Invoice Model
 *
 * @property int $id
 * @property int|null $booking_id
 * @property int|null $guest_id
 * @property int|null $room_id
 * @property string $invoice_number
 * @property float $amount
 * @property float $tax
 * @property float $total
 * @property float|null $late_fee
 * @property float|null $paid_amount
 * @property string|null $payment_method
 * @property Carbon|null $issue_date (casted)
 * @property Carbon|null $due_date (casted)
 * @property Carbon|null $payment_date (casted)
 * @property string $status (draft, sent, paid, overdue, cancelled)
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 * Relations:
 * @property-read Booking|null $booking
 * @property-read Guest|null $guest
 * @property-read Room|null $room
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice byMonth(int $month, int $year)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice draft()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice overdue()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice paid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereBookingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereDueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereGuestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereInvoiceNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereIssueDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereLateFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invoice withoutTrashed()
 */
	class Invoice extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $facility_id  <-- เพิ่ม property นี้
 * @property int $room_id
 * @property string $maintenance_type
 * @property string $description
 * @property string $status
 * @property string $assigned_to
 * @property float $cost
 * @property string $notes
 * @property \Carbon\Carbon $request_date
 * @property \Carbon\Carbon|null $completion_date
 * @property \App\Models\Room $room
 * @property \App\Models\Facility $facility <-- เพิ่มความสัมพันธ์นี้
 * @property string $issue_type
 * @property string $reported_date
 * @property string|null $completed_date
 * @property string $priority
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereIssueType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereReportedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Maintenance whereUpdatedAt($value)
 */
	class Maintenance extends \Eloquent {}
}

namespace App\Models{
/**
 * Meter Model
 *
 * @property int $id
 * @property int $room_id
 * @property string $type
 * @property string $meter_number
 * @property string|null $unit
 * @property Carbon|null $installed_at
 * @property bool $is_active
 * @property string|null $notes
 * @property float|null $rate_per_unit
 * @property float|null $tax_rate
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Room $room
 * @property-read Collection<int, MeterReading> $readings
 * @property-read MeterReading|null $latestReading
 * @property-read string $type_label
 * @property-read string $status_label
 * @property-read int|null $readings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereInstalledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereMeterNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereRatePerUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Meter whereUpdatedAt($value)
 */
	class Meter extends \Eloquent {}
}

namespace App\Models{
/**
 * MeterReading Model
 *
 * @property int $id
 * @property int $meter_id
 * @property int|null $booking_id
 * @property int|null $period_month
 * @property int|null $period_year
 * @property Carbon $reading_date
 * @property float $reading_value
 * @property int|null $recorded_by
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Meter $meter
 * @property-read User|null $recorder
 * @property-read User|null $recordedBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereMeterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereReadingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereReadingValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereRecordedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MeterReading whereUpdatedAt($value)
 */
	class MeterReading extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $label
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * Room Model
 *
 * @property int         $id
 * @property string      $room_number
 * @property string|null $room_type
 * @property string|null $zone
 * @property int|null    $floor
 * @property float|null  $price_per_month
 * @property int|null    $capacity
 * @property string|null $description
 * @property string      $status
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, Booking> $bookings
 * @property-read Collection<int, Meter> $meters
 * @property-read Booking|null $currentBooking
 * @property-read float|null $rent_amount
 * @property string $rental_type
 * @property-read int|null $bookings_count
 * @property-read int|null $meters_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCapacity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereFloor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room wherePricePerMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRentalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRoomNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereRoomType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room whereZone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Room withoutTrashed()
 */
	class Room extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $role_id
 * @property bool $is_active
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

