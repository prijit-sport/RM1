<?php
 
namespace App\Http\Controllers;
 
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class GuestController extends Controller
{
    // ─────────────────────────────────────────
    //  INDEX - หน้ารายการแขกทั้งหมด (พร้อม search)
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        // แก้ไขตรงนี้: เพิ่ม with() เพื่อดึงข้อมูลห้องพักมาพร้อมกับแขกเลย ป้องกันข้อมูลหายและโหลดเร็วขึ้น
        $query = Guest::with(['bookings.room', 'contracts.room']);
 
        // ค้นหาตามชื่อ / อีเมล / เบอร์โทร / เลขบัตร
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
    //  CREATE - หน้าฟอร์มเพิ่มแขกใหม่ (ทีละคน)
    // ─────────────────────────────────────────
    public function create()
    {
        return view('guests.create');
    }
 
    // ─────────────────────────────────────────
    //  STORE - บันทึกแขกใหม่ (ทีละคน)
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name'  => 'required|max:50',
            'email'      => 'required|email|unique:guests',
            'phone'      => 'required|max:20',
            'address'    => 'nullable|max:255',
            'city'       => 'nullable|max:100',
            'country'    => 'nullable|max:100',
            'id_number'  => 'required|unique:guests|max:50',
        ]);
 
        Guest::create($validated);
 
        return redirect()->route('guests.index')->with('success', __('ui.guest.created'));
    }
 
    // ─────────────────────────────────────────
    //  BULK CREATE - หน้าฟอร์มเพิ่มแขกหลายคน
    // ─────────────────────────────────────────
    public function bulkCreate()
    {
        return view('guests.bulk-create');
    }
 
    // ─────────────────────────────────────────
    //  BULK STORE - บันทึกแขกหลายคนพร้อมกัน
    // ─────────────────────────────────────────
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'guests'                 => ['required', 'array', 'min:1'],
            'guests.*.first_name'    => ['required', 'string', 'max:50'],
            'guests.*.last_name'     => ['required', 'string', 'max:50'],
            'guests.*.id_number'     => ['required', 'string', 'max:50', 'distinct', 'unique:guests,id_number'],
            'guests.*.date_of_birth' => ['nullable', 'date'],
            'guests.*.phone'         => ['required', 'string', 'max:20'],
        ]);
 
        // ใช้ transaction — ถ้าแถวใดแถวหนึ่งพัง จะ rollback ทั้งหมด
        DB::transaction(function () use ($validated): void {
            foreach ($validated['guests'] as $guestData) {
                Guest::create([
                    'first_name'    => $guestData['first_name'],
                    'last_name'     => $guestData['last_name'],
                    'id_number'     => $guestData['id_number'],
                    'date_of_birth' => $guestData['date_of_birth'] ?? null,
                    'phone'         => $guestData['phone'],
                ]);
            }
        });
 
        return redirect()
            ->route('guests.index')
            ->with('success', __('ui.guest.bulk_created', ['count' => count($validated['guests'])]));
    }
 
    // ─────────────────────────────────────────
    //  SHOW - ดูรายละเอียดแขก
    // ─────────────────────────────────────────
    public function show(Guest $guest)
    {
        return view('guests.show', compact('guest'));
    }
 
    // ─────────────────────────────────────────
    //  EDIT - หน้าฟอร์มแก้ไขแขก
    // ─────────────────────────────────────────
    public function edit(Guest $guest)
    {
        return view('guests.edit', compact('guest'));
    }
 
    // ─────────────────────────────────────────
    //  UPDATE - อัปเดตข้อมูลแขก
    // ─────────────────────────────────────────
    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name'  => 'required|max:50',
            'email'      => 'required|email|unique:guests,email,' . $guest->id,
            'phone'      => 'required|max:20',
            'address'    => 'nullable|max:255',
            'city'       => 'nullable|max:100',
            'country'    => 'nullable|max:100',
            'id_number'  => 'required|max:50|unique:guests,id_number,' . $guest->id,
        ]);
 
        $guest->update($validated);
 
        return redirect()->route('guests.show', $guest)->with('success', __('ui.guest.updated'));
    }
 
    // ─────────────────────────────────────────
    //  DESTROY - ลบแขก
    // ─────────────────────────────────────────
    public function destroy(Guest $guest)
    {
        $guest->delete();
 
        return redirect()->route('guests.index')->with('success', __('ui.guest.deleted'));
    }
 
    // ─────────────────────────────────────────
    //  EXPORT - ส่งออกรายชื่อแขกเป็น Excel
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $guests = Guest::orderBy('id')->get();
 
        $rows = [];
        $rows[] = ['First Name', 'Last Name', 'Email', 'Phone', 'Address', 'City', 'Country', 'ID Number'];
 
        foreach ($guests as $guest) {
            $rows[] = [
                $guest->first_name,
                $guest->last_name,
                $guest->email     ?? '-',
                $guest->phone     ?? '-',
                $guest->address   ?? '-',
                $guest->city      ?? '-',
                $guest->country   ?? '-',
                $guest->id_number ?? '-',
            ];
        }
 
        $filename = 'guests_export_' . date('Y-m-d') . '.xlsx';
 
        return xlsx_download($filename, $rows);
    }
}