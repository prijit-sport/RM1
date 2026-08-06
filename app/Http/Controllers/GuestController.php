<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    // ─────────────────────────────────────────
    //  INDEX - รายการผู้เช่าทั้งหมด
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Guest::class);

        $query = Guest::with(['contracts.room']);

        if ($request->filled('search')) {
            $s = $request->input('search');

            // PII fields (email, id_number) are encrypted at rest and cannot be
            // searched via SQL LIKE. We filter them in PHP after fetching the
            // SQL-compatible subset (first_name, last_name, phone).
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%");
            })
                ->orWhere(function ($q) use ($s) {
                    // Exact lookup on the blind-index hash for PII fields.
                    $q->where('email_hash', $this->piiHash($s))
                        ->orWhere('id_number_hash', $this->piiHash($s));
                });
        }

        $guests = $query
            ->orderByDesc('id')
            ->get()
            ->filter(function (Guest $guest) use ($request) {
                if (! $request->filled('search')) {
                    return true;
                }

                $s = strtolower($request->input('search'));

                // Keep rows already matched via SQL (name/phone) or hash lookup.
                return str_contains(strtolower((string) $guest->email), $s)
                    || str_contains(strtolower((string) $guest->id_number), $s);
            })
            ->values();

        $perPage = 10;
        $page = max(1, (int) $request->get('page', 1));
        $paginator = new LengthAwarePaginator(
            $guests->forPage($page, $perPage),
            $guests->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('guests.index', ['guests' => $paginator]);
    }

    /** คำนวณ blind-index hash สำหรับ lookup PII field */
    private function piiHash(string $value): string
    {
        return hash_hmac('sha256', $value, (string) config('app.key'));
    }

    // ─────────────────────────────────────────
    //  CREATE - ฟอร์มเพิ่มผู้เช่าใหม่
    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Guest::class); // ✅ แก้ไข: เพิ่ม authorization

        return view('guests.create');
    }

    // ─────────────────────────────────────────
    //  STORE - บันทึกผู้เช่าใหม่
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('create', Guest::class); // ✅ แก้ไข: เพิ่ม authorization

        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => ['required', 'email', function ($attribute, $value, $fail) {
                if (Guest::where('email_hash', $this->piiHash($value))->exists()) {
                    $fail('The email has already been taken.');
                }
            }],
            'phone' => 'required|max:20',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => ['required', 'max:50', function ($attribute, $value, $fail) {
                if (Guest::where('id_number_hash', $this->piiHash($value))->exists()) {
                    $fail('The id number has already been taken.');
                }
            }],
            'nationality' => 'nullable|max:100',
            'date_of_birth' => 'nullable|date',
            'emergency_contact' => 'nullable|max:100',
            'emergency_phone' => 'nullable|max:20',
            'notes' => 'nullable|max:1000',
        ]);

        Guest::create($validated);

        return redirect()->route('guests.index')->with('success', 'เพิ่มผู้เช่าเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  BULK CREATE - เพิ่มหลายคนพร้อมกัน
    // ─────────────────────────────────────────
    public function bulkCreate()
    {
        $this->authorize('create', Guest::class); // ✅ แก้ไข: เพิ่ม authorization

        return view('guests.bulk-create');
    }

    // ─────────────────────────────────────────
    //  BULK STORE - บันทึกหลายคน
    // ─────────────────────────────────────────
    public function bulkStore(Request $request)
    {
        $this->authorize('create', Guest::class); // ✅ แก้ไข: เพิ่ม authorization

        $validated = $request->validate([
            'guests' => ['required', 'array', 'min:1'],
            'guests.*.first_name' => ['required', 'string', 'max:50'],
            'guests.*.last_name' => ['required', 'string', 'max:50'],
            'guests.*.id_number' => ['required', 'string', 'max:50', 'distinct', function ($attribute, $value, $fail) {
                if (Guest::where('id_number_hash', $this->piiHash($value))->exists()) {
                    $fail('The id number has already been taken.');
                }
            }],
            'guests.*.date_of_birth' => ['nullable', 'date'],
            'guests.*.phone' => ['required', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['guests'] as $guestData) {
                Guest::create([
                    'first_name' => $guestData['first_name'],
                    'last_name' => $guestData['last_name'],
                    'id_number' => $guestData['id_number'],
                    'date_of_birth' => $guestData['date_of_birth'] ?? null,
                    'phone' => $guestData['phone'],
                ]);
            }
        });

        return redirect()
            ->route('guests.index')
            ->with('success', 'เพิ่มผู้เช่า '.count($validated['guests']).' คนเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  SHOW - ดูรายละเอียดผู้เช่า
    // ─────────────────────────────────────────
    public function show(Guest $guest)
    {
        $this->authorize('view', $guest); // ✅ แก้ไข: เพิ่ม authorization
        $guest->load(['contracts.room']);

        return view('guests.show', compact('guest'));
    }

    // ─────────────────────────────────────────
    //  EDIT - ฟอร์มแก้ไข
    // ─────────────────────────────────────────
    public function edit(Guest $guest)
    {
        $this->authorize('update', $guest); // ✅ แก้ไข: เพิ่ม authorization

        return view('guests.edit', compact('guest'));
    }

    // ─────────────────────────────────────────
    //  UPDATE - อัปเดตข้อมูล
    // ─────────────────────────────────────────
    public function update(Request $request, Guest $guest)
    {
        $this->authorize('update', $guest); // ✅ แก้ไข: เพิ่ม authorization

        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => ['required', 'email', function ($attribute, $value, $fail) use ($guest) {
                if (Guest::where('email_hash', $this->piiHash($value))
                    ->where('id', '!=', $guest->id)
                    ->exists()) {
                    $fail('The email has already been taken.');
                }
            }],
            'phone' => 'required|max:20',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => ['required', 'max:50', function ($attribute, $value, $fail) use ($guest) {
                if (Guest::where('id_number_hash', $this->piiHash($value))
                    ->where('id', '!=', $guest->id)
                    ->exists()) {
                    $fail('The id number has already been taken.');
                }
            }],
            'nationality' => 'nullable|max:100',
            'date_of_birth' => 'nullable|date',
            'emergency_contact' => 'nullable|max:100',
            'emergency_phone' => 'nullable|max:20',
            'notes' => 'nullable|max:1000',
        ]);

        $guest->update($validated);

        return redirect()->route('guests.show', $guest)->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  DESTROY - ลบผู้เช่า
    // ─────────────────────────────────────────
    public function destroy(Guest $guest)
    {
        $this->authorize('delete', $guest); // ✅ แก้ไข: เพิ่ม authorization ที่ขาดไป
        $guest->delete();

        return redirect()->route('guests.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  EXPORT - ส่งออก Excel
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('export', Guest::class); // ✅ แก้ไข: เพิ่ม authorization

        $guests = Guest::with('contracts.room')->orderBy('id')->get();

        $rows = [];
        $rows[] = ['ชื่อ', 'นามสกุล', 'อีเมล', 'เบอร์โทร', 'เลขบัตร', 'ห้องพัก', 'สถานะ'];

        foreach ($guests as $guest) {
            $contract = $guest->contracts->where('status', 'active')->first();
            $room = $contract?->room;

            $rows[] = [
                $guest->first_name,
                $guest->last_name,
                $guest->email ?? '-',
                $guest->phone ?? '-',
                $guest->id_number ?? '-',
                $room?->room_number ?? 'ไม่มีห้อง',
                $contract ? 'มีผู้เช่า' : 'ว่าง',
            ];
        }

        $filename = 'guests_export_'.date('Y-m-d').'.xlsx';

        return xlsx_download($filename, $rows);
    }
}
