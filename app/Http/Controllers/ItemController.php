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
}
