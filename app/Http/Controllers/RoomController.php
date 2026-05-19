<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // ─────────────────────────────────────────
    //  INDEX - หน้ารายการห้องทั้งหมด พร้อมระบบค้นหา (Dynamic Stats)
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $this->authorize('viewAny', Room::class);

        $query = Room::query();


        // 1. กรองข้อมูลตามการค้นหา (Search)
        if ($request->filled('search')) {
            $query->where('room_number', 'like', '%' . $request->search . '%');
        }

        // 2. กรองตามสถานะ (Status Filter)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /** * แก้ไขจุดนี้: ดึงข้อมูลที่ "ถูกกรองแล้ว" มานับสถิติ 
         * เพื่อให้ตัวเลขบน Card สอดคล้องกับตารางด้านล่าง
         */
        $allFilteredRooms = (clone $query)->get();

        $availableCount   = $allFilteredRooms->where('status', 'available')->count();
        $occupiedCount    = $allFilteredRooms->where('status', 'occupied')->count();
        $maintenanceCount = $allFilteredRooms->where('status', 'maintenance')->count();

        // ดึงเลขห้องแยกตามสถานะ (เฉพาะที่ผ่านการกรอง)
        $availableRoomList = $allFilteredRooms->where('status', 'available')
                            ->sortBy('room_number')
                            ->pluck('room_number');
                            
        $occupiedRoomList  = $allFilteredRooms->where('status', 'occupied')
                            ->sortBy('room_number')
                            ->pluck('room_number');

        // 3. ดึงข้อมูลแสดงในตารางพร้อม Pagination
        $rooms = $query->orderBy('room_number', 'asc')->paginate(15)->withQueryString();

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
        return view('rooms.create');
    }

    // ─────────────────────────────────────────
    //  STORE - บันทึกห้องใหม่ พร้อมเช็คเลขห้องซ้ำ
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number'     => 'required|unique:rooms,room_number|max:10', 
            'room_type'       => 'required|max:50',
            'price_per_month' => 'required|numeric|min:0',
            'capacity'        => 'required|integer|min:1',
            'status'          => 'required|in:available,occupied,maintenance',
            'description'     => 'nullable|max:500',
            'floor'           => 'nullable|integer',
            'building'        => 'nullable|string|max:100',
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
        return view('rooms.show', compact('room'));
    }

    // ─────────────────────────────────────────
    //  EDIT - หน้าฟอร์มแก้ไขห้อง
    // ─────────────────────────────────────────
    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    // ─────────────────────────────────────────
    //  UPDATE - อัปเดตข้อมูลห้อง
    // ─────────────────────────────────────────
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number'     => 'required|max:10|unique:rooms,room_number,' . $room->id,
            'room_type'       => 'required|max:50',
            'price_per_month' => 'required|numeric|min:0',
            'capacity'        => 'required|integer|min:1',
            'status'          => 'required|in:available,occupied,maintenance',
            'description'     => 'nullable|max:500',
            'floor'           => 'nullable|integer',
            'building'        => 'nullable|string|max:100',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.show', $room)->with('success', 'อัปเดตข้อมูลห้องเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  DESTROY - ลบห้อง
    // ─────────────────────────────────────────
    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'ลบห้องเรียบร้อยแล้ว');
    }

    // ─────────────────────────────────────────
    //  BULK CREATE - หน้าเพิ่มหลายห้องพร้อมกัน
    // ─────────────────────────────────────────
    public function bulkCreate()
    {
        return view('rooms.bulk-create');
    }

    // ─────────────────────────────────────────
    //  BULK STORE - บันทึกหลายห้องและตรวจสอบเลขซ้ำ
    // ─────────────────────────────────────────
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'prefix'          => 'nullable|string|max:5',
            'start_number'    => 'required|integer|min:1',
            'end_number'      => 'required|integer|gte:start_number',
            'floor'           => 'nullable|integer',
            'building'        => 'nullable|string|max:100',
            'room_type'       => 'required|max:50',
            'price_per_month' => 'required|numeric|min:0',
            'capacity'        => 'required|integer|min:1',
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
                'building'        => $validated['building'] ?? null,
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
    //  EXPORT - ส่งออกไฟล์ Excel
    // ─────────────────────────────────────────
    public function export(Request $request)
    {
        $rooms = Room::orderBy('room_number')->get();
        $filename = 'rooms_export_' . date('Y-m-d') . '.xlsx';

        $rows = [];
        $rows[] = ['Room Number', 'Type', 'Price/Month', 'Capacity', 'Status', 'Floor', 'Building', 'Description'];

        foreach ($rooms as $room) {
            $rows[] = [
                $room->room_number,
                $room->room_type,
                $room->price_per_month,
                $room->capacity,
                $room->status,
                $room->floor ?? '-',
                $room->building ?? '-',
                $room->description ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }
}