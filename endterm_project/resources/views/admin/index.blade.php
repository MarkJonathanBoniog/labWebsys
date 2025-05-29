@extends('layouts.main')

@section('title', 'Record List')

@section('content')
<div class="mt-4">
    <h3 class="mb-4">Record List</h3>
    
    <p>Monitor and manage all records made on the system as an administrator.</p>

    <hr class="mx-2">

    <h5>Filtering Options</h5>
    <!-- Filter Form -->
    <form method="GET" action="{{ route('records.index') }}" class="row g-3 mb-4">
        <div class="col-md-12">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ request('name') }}" placeholder="Search by recepient's name">
        </div>
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
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-3">
            <label for="to_date" class="form-label">To Date</label>
            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
        <div class="col-12">
            <a href="#" class="text-primary" onclick="event.preventDefault(); document.getElementById('advanced-filters').classList.toggle('d-none');">
                Toggle Advanced Filters
            </a>
        </div>
        @php
            $advancedVisible = request()->filled('semester') || request()->filled('gender') || request()->filled('schoolyear') || request()->filled('staff');
        @endphp
        <div id="advanced-filters" class="row g-3 mt-2 {{ $advancedVisible ? '' : 'd-none' }}">
            <div class="col-md-3">
                <label for="semester" class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-select">
                    <option value="">All</option>
                    <option value="1st" {{ request('semester') == '1st' ? 'selected' : '' }}>1st</option>
                    <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="gender" class="form-label">Gender</label>
                <select name="gender" id="gender" class="form-select">
                    <option value="">All</option>
                    @foreach (['Male', 'Female', 'Other'] as $gender)
                        <option value="{{ $gender }}" {{ request('gender') == $gender ? 'selected' : '' }}>{{ $gender }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="schoolyear" class="form-label">Academic Year</label>
                <select name="schoolyear" id="schoolyear" class="form-select">
                    <option value="">All</option>
                    @for ($y = 2025; $y >= 1950; $y--)
                        @php $pair = $y . '-' . ($y + 1); @endphp
                        <option value="{{ $pair }}" {{ request('schoolyear') == $pair ? 'selected' : '' }}>{{ $pair }}</option>
                    @endfor
                </select>
            </div>

            <div class="col-md-3">
                <label for="staff" class="form-label">Handled By (Staff)</label>
                <select name="staff" id="staff" class="form-select">
                    <option value="">All</option>
                    @foreach ($staffUsers as $staff)
                        <option value="{{ $staff->id }}" {{ request('staff') == $staff->id ? 'selected' : '' }}>
                            {{ $staff->fname }} {{ $staff->lname }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
        </div>
    </form>

    <!-- Record Table -->
     <hr id="list">
     <h5>Record Listing</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Reference Number</th>
                    <th>Transferee Name</th>
                    <th>Program</th>
                    <th>Handled By</th>
                    <th>Date Requested</th>
                    <th>Status</th>
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
                        <td>{{ $record->user->fname }} {{ $record->user->lname }}</td>
                        <td>{{\Carbon\Carbon::parse($record->created_at)->format('F j, Y g:i A')  }}</td>
                        <td><span class="badge bg-{{ $badgeClass }}">{{ $record->status }}</span></td>
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
        window.addEventListener('DOMContentLoaded', () => {
            const url = new URL(window.location.href);
            const hasParams = url.searchParams.toString().length > 0;

            if (hasParams) {
                const target = document.getElementById('list');
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    </script>
@endsection