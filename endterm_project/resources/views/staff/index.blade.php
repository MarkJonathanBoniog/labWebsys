@extends('layouts.main')

@section('title', 'Record List')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Record List</h3>
    
    <a href="{{ route('staff.record.generate') }}" class="btn btn-success mb-4">Create new record</a>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('records.index') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select">
                <option value="">All</option>
                @foreach (['Pending', 'Ready', 'Completed', 'Failed'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="program" class="form-label">Program</label>
            <select name="program" id="program" class="form-select">
                <option value="">All</option>
                @foreach ($programs as $program)
                    <option value="{{ $program }}" {{ request('program') == $program ? 'selected' : '' }}>
                        {{ $program }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="year" class="form-label">Year</label>
            <select name="year" id="year" class="form-select">
                <option value="">All</option>
                @foreach ($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </div>
    </form>

    <!-- Record Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Reference Number</th>
                    <th>Transferee Name</th>
                    <th>Program</th>
                    <th>Status</th>
                    <th>Handled By</th>
                    <th>Claimed At</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($records as $record)
                    @php
                        $badgeClass = match ($record->status) {
                            'Pending' => 'secondary',
                            'Ready' => 'warning',
                            'Completed' => 'success',
                            'Failed' => 'danger',
                            default => 'light'
                        };
                        $recordUrl = route('staff.record.view', Crypt::encrypt($record->id));
                    @endphp
                    <tr class="clickable-row" data-href="{{ $recordUrl }}" style="cursor: pointer;">
                        <td>{{ $record->refnumber }}</td>
                        <td>{{ $record->lname }}, {{ $record->fname }} {{ $record->mname }}</td>
                        <td>{{ $record->program }}</td>
                        <td><span class="badge bg-{{ $badgeClass }}">{{ $record->status }}</span></td>
                        <td>{{ $record->user->fname }} {{ $record->user->lname }}</td>
                        <td>{{ $record->claimed ? \Carbon\Carbon::parse($record->claimed)->format('F j, Y g:i A') : ($record->status == "Failed" ? 'Unavailable' : 'Not Yet') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $records->withQueryString()->links() }}
    </div>
</div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.clickable-row').forEach(function(row) {
                row.addEventListener('click', function (e) {
                    // prevent clicks on buttons/links from triggering row click
                    if (e.target.tagName.toLowerCase() !== 'a' && e.target.tagName.toLowerCase() !== 'button') {
                        window.location = this.dataset.href;
                    }
                });
            });
        });
    </script>
@endsection