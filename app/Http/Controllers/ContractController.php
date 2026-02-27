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
        return redirect()->route('contracts.index')->with('success', __('ui.contract.created'));
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
        return redirect()->route('contracts.show', $contract)->with('success', __('ui.contract.updated'));
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', __('ui.contract.deleted'));
    }

    public function export(Request $request)
    {
        $contracts = Contract::orderBy('id', 'desc')->get();
        $filename = 'contracts_export_' . date('Y-m-d') . '.xlsx';

        $rows = [];
        $rows[] = ['Contract Number', 'Contractor', 'Description', 'Start Date', 'End Date', 'Amount', 'Status', 'Notes'];

        foreach ($contracts as $contract) {
            $rows[] = [
                $contract->contract_number,
                $contract->contractor_name,
                $contract->description ?? '-',
                optional($contract->start_date)->format('d/m/Y'),
                optional($contract->end_date)->format('d/m/Y'),
                $contract->amount,
                $contract->status,
                $contract->notes ?? '-',
            ];
        }

        return xlsx_download($filename, $rows);
    }

    public function generatePdf($id)
    {
        return redirect()->route('contracts.show', $id)->with('info', __('ui.common.pdf_coming_soon'));
    }

    public function expiring()
    {
        $contracts = Contract::whereDate('end_date', '<=', now()->addDays(30))->orderBy('end_date')->paginate(10);
        return view('contracts.index', compact('contracts'));
    }
}


