<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contracts</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .btn-group { display: flex; gap: 10px; }
        .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .btn:hover { background: #764ba2; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; color: white; padding: 5px 10px; }
        .btn-danger:hover { background: #c82333; }
        .btn-link { background: transparent; color: #667eea; padding: 5px 10px; text-decoration: underline; cursor: pointer; }
        .btn-link:hover { color: #764ba2; }
        .table-container { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #667eea; color: white; padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid #eee; }
        tr:hover { background: #f9f9f9; }
        .status { padding: 5px 10px; border-radius: 20px; font-size: 0.9em; font-weight: 600; }
        .status.draft { background: #e2e3e5; color: #383d41; }
        .status.active { background: #d4edda; color: #155724; }
        .status.completed { background: #cce5ff; color: #004085; }
        .status.cancelled { background: #f8d7da; color: #721c24; }
        .actions { display: flex; gap: 10px; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 5px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .pagination { margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Contracts</h1>
            <div class="btn-group">
                <a href="{{ route('contracts.create') }}" class="btn">Add Contract</a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Contract No.</th>
                        <th>Contractor</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contract->contract_number }}</td>
                            <td>{{ $contract->contractor_name }}</td>
                            <td>{{ optional($contract->start_date)->format('d/m/Y') }}</td>
                            <td>{{ optional($contract->end_date)->format('d/m/Y') }}</td>
                            <td>{{ number_format($contract->amount, 2) }}</td>
                            <td><span class="status {{ strtolower($contract->status) }}">{{ ucfirst($contract->status) }}</span></td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('contracts.show', $contract->id) }}" class="btn-link">View</a>
                                    <a href="{{ route('contracts.edit', $contract->id) }}" class="btn-link">Edit</a>
                                    <form method="POST" action="{{ route('contracts.destroy', $contract->id) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger" onclick="return confirm('Delete this contract?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">No contract data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($contracts->hasPages())
            <div class="pagination">{{ $contracts->links() }}</div>
        @endif
    </div>
</body>
</html>
