<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
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
            $query->where(function ($q) use ($s) {
                $q->where('first_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('id_number', 'like', "%{$s}%");
            });
        }

        $guests = $query->orderByDesc('id')->paginate(10)->withQueryString();

        return view('guests.index', compact('guests'));
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
            'email' => 'required|email|unique:guests',
            'phone' => 'required|max:20',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => 'required|unique:guests|max:50',
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
            'guests.*.id_number' => ['required', 'string', 'max:50', 'distinct', 'unique:guests,id_number'],
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
            'email' => 'required|email|unique:guests,email,'.$guest->id,
            'phone' => 'required|max:20',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => 'required|max:50|unique:guests,id_number,'.$guest->id,
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
