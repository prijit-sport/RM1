<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // ─────────────────────────────────────────
    //  INDEX - หน้ารายการห้องทั้งหมด พร้อมระบบค้นหา
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Room::class);

        $query = Room::query();

        // 1. กรองตามหมายเลขห้อง
        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }

        // 2. กรองตามสถานะ
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. กรองตามชั้น
        if ($request->filled('floor')) {
            $query->where('floor', $request->floor);
        }

        // 4. กรองตามโซน
        if ($request->filled('zone')) {
            $query->where('zone', $request->zone);
        }

        // ดึงข้อมูลที่กรองแล้วมานับสถิติ
        $allFilteredRooms = (clone $query)->get();

        $availableCount   = $allFilteredRooms->where('status', 'available')->count();
        $occupiedCount    = $allFilteredRooms->where('status', 'occupied')->count();
        $maintenanceCount = $allFilteredRooms->where('status', 'maintenance')->count();

        $availableRoomList = $allFilteredRooms->where('status', 'available')
            ->sortBy('room_number')
            ->pluck('room_number');

        $occupiedRoomList  = $allFilteredRooms->where('status', 'occupied')
            ->sortBy('room_number')
            ->pluck('room_number');

        // ดึงข้อมูลแสดงในตารางพร้อม Pagination
        $rooms = $query->orderBy('floor')->orderBy('zone')->orderBy('room_number')->paginate(30)->withQueryString();

        return view('rooms.index', compact(
            'rooms',
            'availableCount',
            'occupiedCount',
            'maintenanceCount',
            'availableRoomList',
            'occupiedRoomList'
        ));
    }

    // ─────────────────────────────────────────
    //  CREATE - หน้าฟอร์มเพิ่มห้องใหม่
    // ─────────────────────────────────────────
    public function create()
    {
        $this->authorize('create', Room::class); // ✅ แก้ไข: เพิ่ม authorization
        return view('rooms.create');
    }

    // ─────────────────────────────────────────
    //  STORE - บันทึกห้องใหม่
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('create', Room::class); // ✅ แก้ไข: เพิ่ม authorization

        $validated = $request->validate([
            'room_number'     => 'required|unique:rooms,room_number|max:10',
            'room_type'       => 'required|in:fan,air',
            'price_per_month' => 'required|numeric|min:0',
            'capacity'        => 'required|integer|min:1|max:3',
            'status'          => 'required|in:available,occupied,maintenance',
            'description'     => 'nullable|max:500',
            'floor'           => 'nullable|integer|min:1|max:5',
            'zone'            => 'nullable|in:A,B',
        ], [
            'room_number.unique' => 'หมายเลขห้องนี้มีอยู่ในระบบแล้ว กรุณาตรวจสอบอีกครั้ง'
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'เพิ่มห้องใหม่เรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  SHOW - ดูรายละเอียดห้อง
    // ─────────────────────────────────────────
    public function show(Room $room)
    {
        $this->authorize('view', $room); // ✅ แก้ไข: เพิ่ม authorization
        return view('rooms.show', compact('room'));
    }

    // ─────────────────────────────────────────
    //  EDIT - หน้าฟอร์มแก้ไขห้อง
    // ─────────────────────────────────────────
    public function edit(Room $room)
    {
        $this->authorize('update', $room); // ✅ แก้ไข: เพิ่ม authorization
        return view('rooms.edit', compact('room'));
    }

    // ─────────────────────────────────────────
    //  UPDATE - อัปเดตข้อมูลห้อง
    // ─────────────────────────────────────────
    public function update(Request $request, Room $room)
    {
        $this->authorize('update', $room); // ✅ แก้ไข: เพิ่ม authorization

        $validated = $request->validate([
            'room_number'     => 'required|max:10|unique:rooms,room_number,' . $room->id,
            'room_type'       => 'required|in:fan,air',
            'price_per_month' => 'required|numeric|min:0',
            'capacity'        => 'required|integer|min:1|max:3',
            'status'          => 'required|in:available,occupied,maintenance',
            'description'     => 'nullable|max:500',
            'floor'           => 'nullable|integer|min:1|max:5',
            'zone'            => 'nullable|in:A,B',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.show', $room)->with('success', 'อัปเดตข้อมูลห้องเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  DESTROY - ลบห้อง
    // ─────────────────────────────────────────
    public function destroy(Room $room)
    {
        $this->authorize('delete', $room); // ✅ แก้ไข: เพิ่ม authorization ที่ขาดไป
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'ลบห้องเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  BULK CREATE - เพิ่มหลายห้องพร้อมกัน
    // ─────────────────────────────────────────
    public function bulkCreate()
    {
        $this->authorize('create', Room::class); // ✅ แก้ไข: เพิ่ม authorization
        return view('rooms.bulk-create');
    }

    // ─────────────────────────────────────────
    //  BULK STORE - บันทึกหลายห้อง
    // ─────────────────────────────────────────
    public function bulkStore(Request $request)
    {
        $this->authorize('create', Room::class); // ✅ แก้ไข: เพิ่ม authorization

        $validated = $request->validate([
            'prefix'          => 'nullable|string|max:5',
            'start_number'    => 'required|integer|min:1',
            'end_number'      => 'required|integer|gte:start_number',
            'floor'           => 'nullable|integer|min:1|max:5',
            'zone'            => 'nullable|in:A,B',
            'room_type'       => 'required|in:fan,air',
            'price_per_month' => 'required|numeric|min:0',
            'capacity'        => 'required|integer|min:1|max:3',
            'status'          => 'required|in:available,occupied,maintenance',
        ]);

        $created = 0;
        $skipped = 0;

        for ($i = $validated['start_number']; $i <= $validated['end_number']; $i++) {
            $roomNumber = ($validated['prefix'] ?? '') . $i;

            if (Room::where('room_number', $roomNumber)->exists()) {
                $skipped++;
                continue;
            }

            Room::create([
                'room_number'     => $roomNumber,
                'room_type'       => $validated['room_type'],
                'price_per_month' => $validated['price_per_month'],
                'capacity'        => $validated['capacity'],
                'status'          => $validated['status'],
                'floor'           => $validated['floor'] ?? null,
                'zone'            => $validated['zone'] ?? null,
            ]);
            $created++;
        }

        $msg = "เพิ่มห้องสำเร็จ {$created} ห้อง";
        if ($skipped > 0) {
            $msg .= " (ข้ามไป {$skipped} ห้องเนื่องจากเลขซ้ำ)";
        }

        return redirect()->route('rooms.index')->with('success', $msg);
    }

    // ─────────────────────────────────────────
    //  EXPORT - ส่งออก Excel
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $this->authorize('export', Room::class); // ✅ แก้ไข: เพิ่ม authorization

        $rooms = Room::orderBy('floor')->orderBy('zone')->orderBy('room_number')->get();
        $filename = 'rooms_export_' . date('Y-m-d') . '.xlsx';

        $rows = [];
        $rows[] = ['หมายเลขห้อง', 'โซน', 'ชั้น', 'ประเภท', 'ราคา/เดือน', 'ความจุ', 'สถานะ', 'หมายเหตุ'];

        foreach ($rooms as $room) {
            $rows[] = [
                $room->room_number,
                $room->zone  ?? '-',
                $room->floor ?? '-',
                $room->room_type === 'air' ? 'แอร์' : 'พัดลม',
                $room->price_per_month,
                $room->capacity,
                match ($room->status) {
                    'available'   => 'ว่าง',
                    'occupied'    => 'ใช้งานอยู่',
                    'maintenance' => 'ซ่อมบำรุง',
                    default       => $room->status,
                },
                $room->description ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }
}
