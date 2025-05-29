@extends('layouts.main')

@section("title", "Staff Audit Tracing")

@section('content')
<div class="mt-4">
    <h3>Staff Audit Tracing</h3>
    <h5>Administrator Access for All Transfer Credential Reports of All Staff</h5>
    
    <hr>
    <p>This page will automatically generate a <strong>Transferees Report File</strong>. Filter out the details of the summary report. All fields are required.</p>
    <form action="{{ route('admin.auditTracing.result') }}" method="GET" target="_blank">
        @csrf
        
        <div class="mb-3">
            <label class="form-label"><strong>Staff</strong> <i>(defaulted to all staff)</i></label>
            <select name="staff_id" class="form-select">
                <option value="all">All Staff</option>
                @foreach ($staffList as $staff)
                    <option value="{{ $staff->id }}">{{ $staff->fname }} {{ $staff->lname }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>All Records Requested on Academic Year</strong></label>
            <select name="year" class="form-select" required>
                @php
                    $currentYear = now()->year;
                @endphp

                @for ($y = $currentYear; $y >= 1950; $y--)
                    <option value="{{ $y }}-{{ $y + 1 }}">{{ $y }}-{{ $y + 1 }}</option>
                @endfor
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>All Records Requested Between This Time Range</strong></label><br>
            @foreach (['Whole Year', 'First Half', 'Second Half', 'First Quarter', 'Second Quarter', 'Third Quarter', 'Fourth Quarter', 'Manual'] as $r)
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="range" value="{{ $r }}" required onchange="toggleManualRange(this)">
                    <label class="form-check-label">{{ $r }}</label>
                </div>
            @endforeach
        </div>

        <div id="manual-range" class="row g-3 d-none mt-1">
            <div class="col-md-6">
                <label><strong>From</strong></label>
                <input type="date" name="from" class="form-control">
            </div>
            <div class="col-md-6">
                <label><strong>To</strong></label>
                <input type="date" name="to" class="form-control">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Filter Records By Semester</strong></label>
            <select name="semester" class="form-select" required>
                <option value="1st">1st Semester</option>
                <option value="2nd">2nd Semester</option>
                <option value="both">Both</option>
            </select>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="include_genders" id="include_genders">
            <label class="form-check-label" for="include_genders"><strong>Include Genders Columns To The Table</strong></label>
        </div>

        <div class="d-flex justify-content-center gap-3 mb-3" style="max-width: 800px; margin: 0 auto;">
            <button type="submit" name="action" value="view" class="btn btn-primary">Preview Report</button>
            <button type="submit" name="action" value="pdf" class="btn btn-success">Download PDF</button>
        </div>
    </form>
<script>
function toggleManualRange(radio) {
    const manual = document.getElementById('manual-range');
    manual.classList.toggle('d-none', radio.value !== 'Manual');
}
</script>
</div>
@endsection
