<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::paginate(10);
        return view('items.index', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:500',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category' => 'required|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        Item::create($validated);
        return redirect()->route('items.index')->with('success', 'Item เพิ่มสำเร็จ');
    }

    public function show(Item $item)
    {
        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'description' => 'nullable|max:500',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'category' => 'required|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $item->update($validated);
        return redirect()->route('items.show', $item)->with('success', 'Item อัปเดตสำเร็จ');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item ลบสำเร็จ');
    }

    public function export(Request $request)
    {
        $items = Item::orderBy('id', 'desc')->get();
        $filename = 'items_export_' . date('Y-m-d') . '.csv';

        return response()->stream(function () use ($items) {
            $rows = [];
            $rows[] = ['Name', 'Description', 'Quantity', 'Price', 'Category', 'Status'];

            foreach ($items as $item) {
                $rows[] = [
                    $item->name,
                    $item->description ?? '-',
                    $item->quantity,
                    $item->price,
                    $item->category,
                    $item->status,
                ];
            }

            $handle = fopen('php://output', 'wb');
            fwrite($handle, chr(0xFF) . chr(0xFE));
            foreach ($rows as $row) {
                $line = '"' . implode('","', array_map(function ($value) {
                    return str_replace('"', '""', (string) $value);
                }, $row)) . '"' . "\r\n";
                fwrite($handle, mb_convert_encoding($line, 'UTF-16LE', 'UTF-8'));
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-16LE',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
