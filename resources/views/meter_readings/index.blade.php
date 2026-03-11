@extends('layouts.app')

@section('title', 'เธเธฑเธเธ—เธถเธเน€เธฅเธเธกเธดเน€เธ•เธญเธฃเน')

@section('page-title', 'เธเธฑเธเธ—เธถเธเน€เธฅเธเธกเธดเน€เธ•เธญเธฃเน')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">เธเธฑเธเธ—เธถเธเน€เธฅเธเธกเธดเน€เธ•เธญเธฃเน</h4>
        <p class="text-muted mb-0">
            เธซเนเธญเธ <strong>{{ $meter->room->room_number ?? '-' }}</strong>
            @if($meter->type === 'water')
                <span class="badge bg-info">เธเนเธณ</span>
            @else
                <span class="badge bg-warning">เนเธเธเนเธฒ</span>
            @endif
            | เน€เธฅเธเธกเธดเน€เธ•เธญเธฃเน: <strong>{{ $meter->meter_number }}</strong>
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('meters.show', $meter) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>เธเธฅเธฑเธ
        </a>
        <a href="{{ route('meters.readings.export', $meter) . '?' . http_build_query(request()->only(['date', 'search'])) }}" class="btn btn-success">
            <i class="bi bi-download me-1"></i>{{ __("ui.export") }}
        </a>
        <a href="{{ route('meters.readings.create', $meter) }}" class="btn btn-primary-custom">
            <i class="bi bi-plus-lg me-1"></i>เน€เธเธดเนเธกเน€เธฅเธ
        </a>
    </div>
</div>

@if(isset($billing))
    <div class="card mb-4">
        <div class="card-body">
            <div class="row gy-2">
                @if($billing['has_reading'])
                    <div class="col-md-3">
                        <div class="text-muted small">เน€เธฅเธเธเนเธญเธเธซเธเนเธฒ</div>
                        <div>{{ number_format($billing['previous'], 2) }} ({{ $billing['previous_date'] ?? '-' }})</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">เน€เธฅเธเธเธฑเธเธเธธเธเธฑเธ</div>
                        <div>{{ number_format($billing['current'], 2) }} ({{ $billing['current_date'] ?? '-' }})</div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-muted small">เธซเธเนเธงเธขเธ—เธตเนเนเธเน</div>
                        <div>{{ number_format($billing['usage'], 2) }} {{ $meter->unit ?? '' }}</div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-muted small">เธญเธฑเธ•เธฃเธฒเธ•เนเธญเธซเธเนเธงเธข</div>
                        <div>{{ number_format($billing['rate'], 2) }} เธเธฒเธ—</div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-muted small">เธขเธญเธ”เธฃเธงเธก</div>
                        <div class="fw-bold">{{ number_format($billing['total'], 2) }} เธเธฒเธ—</div>
                    </div>
                @else
                    <div class="col-12 text-muted">เธขเธฑเธเนเธกเนเธกเธตเน€เธฅเธเธญเนเธฒเธเธฅเนเธฒเธชเธธเธ” เนเธเธฃเธ”เธเธฑเธเธ—เธถเธเน€เธฅเธเนเธซเธกเนเน€เธเธทเนเธญเนเธชเธ”เธเธเธฅเธเธณเธเธงเธ“</div>
                @endif
            </div>
            <div class="text-muted small mt-2">
                เธชเธนเธ•เธฃเธเธณเธเธงเธ“: {{ $billing['formula'] ?? 'Usage ร— Rate + Tax' }}
            </div>
        </div>
    </div>
@endif

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('meters.readings.index', $meter) }}" class="row g-3">
            <div class="col-md-6">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="เธเนเธเธซเธฒเธซเธกเธฒเธขเน€เธซเธ•เธธ..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>{{ __("ui.search") }}
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Readings Table -->
<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>เธงเธฑเธเธ—เธตเน</th>
                    <th>เน€เธฅเธเธกเธดเน€เธ•เธญเธฃเน</th>
                    <th>เธเธนเนเธเธฑเธเธ—เธถเธ</th>
                    <th>เธซเธกเธฒเธขเน€เธซเธ•เธธ</th>
                    <th>เธเธฒเธฃเธเธฃเธฐเธ—เธณ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($readings as $reading)
                    <tr>
                        <td>{{ $reading->reading_date?->format('d/m/Y') ?? '-' }}</td>
                        <td><strong>{{ number_format((float)$reading->reading_value, 2) }}</strong></td>
                        <td>{{ $reading->recordedBy->name ?? '-' }}</td>
                        <td>{{ $reading->notes ?? '-' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="{{ route('meters.readings.edit', [$meter, $reading]) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('meters.readings.destroy', [$meter, $reading]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('เนเธเนเนเธเธซเธฃเธทเธญเนเธกเนเธ—เธตเนเธเธฐเธฅเธ?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2">เนเธกเนเธกเธตเธฃเธฒเธขเธเธฒเธฃเธเธฑเธเธ—เธถเธเน€เธฅเธเธกเธดเน€เธ•เธญเธฃเน</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
@if ($readings->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $readings->links('pagination::bootstrap-5') }}
    </div>
@endif
@endsection



