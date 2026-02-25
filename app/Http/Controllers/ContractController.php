<?php
namespace App\Http\Controllers;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::paginate(10);
        return view('contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('contracts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contractor_name' => 'required|max:100',
            'contract_number' => 'required|unique:contracts|max:50',
            'description' => 'nullable|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,completed,cancelled',
            'notes' => 'nullable|max:500',
        ]);
        Contract::create($validated);
        return redirect()->route('contracts.index')->with('success', 'สัญญาเพิ่มสำเร็จ');
    }

    public function show(Contract $contract)
    {
        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        return view('contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'contractor_name' => 'required|max:100',
            'contract_number' => 'required|unique:contracts,contract_number,' . $contract->id . '|max:50',
            'description' => 'nullable|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,completed,cancelled',
            'notes' => 'nullable|max:500',
        ]);
        $contract->update($validated);
        return redirect()->route('contracts.show', $contract)->with('success', 'สัญญาอัปเดตสำเร็จ');
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'สัญญาลบสำเร็จ');
    }
}
