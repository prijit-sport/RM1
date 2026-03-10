<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller
{
    public function __construct()
    {
        Log::info('[GuestController] Constructor called');
    }
    
    /**
     * Display a listing of the guests.
     */
    public function index(Request $request)
    {
        Log::info('[GuestController] index - User authenticated: ' . (auth()->check() ? 'Yes - ' . auth()->user()->name : 'No'));
        Log::info('[GuestController] index - Session ID: ' . $request->session()->getId());
        
        $guests = Guest::paginate(10);
        return view('guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new guest.
     */
    public function create()
    {
        return view('guests.create');
    }

    /**
     * Store a newly created guest in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:guests',
            'phone' => 'required|max:20',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => 'required|unique:guests|max:50',
        ]);

        Guest::create($validated);
        return redirect()->route('guests.index')->with('success', __('ui.guest.created'));
    }

    /**
     * Display the specified guest.
     */
    public function show(Guest $guest)
    {
        return view('guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified guest.
     */
    public function edit(Guest $guest)
    {
        return view('guests.edit', compact('guest'));
    }

    /**
     * Update the specified guest in storage.
     */
    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|unique:guests,email,' . $guest->id,
            'phone' => 'required|max:20',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:100',
            'country' => 'nullable|max:100',
            'id_number' => 'required|unique:guests,id_number,' . $guest->id . '|max:50',
        ]);

        $guest->update($validated);
        return redirect()->route('guests.show', $guest)->with('success', __('ui.guest.updated'));
    }

    /**
     * Remove the specified guest from storage.
     */
    public function destroy(Guest $guest)
    {
        $guest->delete();
        return redirect()->route('guests.index')->with('success', __('ui.guest.deleted'));
    }

    public function export(Request $request)
    {
        $guests = Guest::all();

        $csvData = [];
        $csvData[] = ['First Name', 'Last Name', 'Email', 'Phone', 'Address', 'City', 'Country', 'ID Number'];

        foreach ($guests as $guest) {
            $csvData[] = [
                $guest->first_name,
                $guest->last_name,
                $guest->email,
                $guest->phone,
                $guest->address,
                $guest->city,
                $guest->country,
                $guest->id_number,
            ];
        }

        $filename = 'guests_export_' . date('Y-m-d') . '.xlsx';

        return xlsx_download($filename, $csvData);
    }
}


