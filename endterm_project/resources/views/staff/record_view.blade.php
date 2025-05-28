@extends('layouts.main')

@section('title', 'Record View')

@section('content')
<div class="container py-4">
<a href="{{ route('records.index') }}" class="btn btn-secondary mb-4">
    &#8592; Back to Record List
</a>

<h2>Record View</h2>

@if ($record->number && $record->refnumber)
    <div class="row g-3 mb-4">
        {{-- Record Number Card (Pastel Blue) --}}
        <div class="col-md-4">
            <div class="card text-dark" style="background-color: #d0e7ff;">
                <div class="card-body">
                    <h6 class="card-title mb-1">Record Number</h6>
                    <p class="card-text fw-bold">{{ $record->number }}</p>
                </div>
            </div>
        </div>

        {{-- Reference Number Card (Light Blue) --}}
        <div class="col-md-4">
            <div class="card text-dark bg-info bg-opacity-25">
                <div class="card-body">
                    <h6 class="card-title mb-1">Reference Number</h6>
                    <p class="card-text fw-bold">{{ $record->refnumber }}</p>
                </div>
            </div>
        </div>

        {{-- Status Card (Dynamic Color) --}}
        <div class="col-md-4">
            @php
                $status = strtolower($record->status);
                $statusColor = match($status) {
                    'pending' => 'secondary',
                    'ready' => 'warning',
                    'completed' => 'success',
                    'failed' => 'danger',
                    default => 'light'
                };
            @endphp

            <div class="card text-dark bg-{{ $statusColor }} bg-opacity-25">
                <div class="card-body">
                    <h6 class="card-title mb-1">Status</h6>
                    <p class="card-text fw-bold text-{{ $statusColor }}">{{ $record->status }}</p>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($readonly)
    <div class="alert alert-warning">{{ $message }}</div>
@endif

<h4 id="section1">Basic Information</h4>
@if (session('success1'))
    <div class="alert alert-success">{{ session('success1') }}</div>
@endif
<form method="POST" action="{{ route('record.update.basic', $record->id) }}">
    @csrf
    <div class="form-group mb-3">
        <label>Firstname</label>
        <input class="form-control" name="fname" value="{{ $record->fname }}" {{ $readonly ? 'readonly' : '' }}>
        @if ($errors->has('fname'))
            <div class="alert alert-danger mt-2">{{ $errors->first('fname') }}</div>
        @endif
    </div>
    <div class="form-group mb-3">
        <label>Middlename</label>
        <input class="form-control" name="mname" value="{{ $record->mname }}" {{ $readonly ? 'readonly' : '' }}>
        @if ($errors->has('mname'))
            <div class="alert alert-danger mt-2">{{ $errors->first('mname') }}</div>
        @endif
    </div>
    <div class="form-group mb-3">
        <label>Lastname <small>(with suffix, if any)</small></label>
        <input class="form-control" name="lname" value="{{ $record->lname }}" {{ $readonly ? 'readonly' : '' }}>
        @if ($errors->has('lname'))
            <div class="alert alert-danger mt-2">{{ $errors->first('lname') }}</div>
        @endif
    </div>
    <div class="form-group mb-3">
        <label>Program</label>
        <select class="form-control" name="program" {{ ($record->number && $record->refnumber) || $readonly ? 'disabled' : '' }}>
            @foreach ($programs as $program)
                <option value="{{ $program }}" {{ $record->program == $program ? 'selected' : '' }}>{{ $program }}</option>
            @endforeach
        </select>
        @if ($errors->has('program'))
            <div class="alert alert-danger mt-2">{{ $errors->first('program') }}</div>
        @endif
        @if ($record->number && $record->refnumber)
            <small class="form-text text-muted">Program is locked after reference number is confirmed and generated.</small>
            <input type="hidden" name="program" value="{{ $record->program }}">
        @endif
    </div>
    @unless($readonly)
        <button type="submit" class="btn btn-primary mt-2">Save Information</button>
    @endunless
</form>

@if ($record->number && $record->refnumber)
    <hr class="my-4">
    <h4 id="section2" class="mb-3">Document Submission</h4>
    @if (session('success2'))
        <div class="alert alert-success">{{ session('success2') }}</div>
    @endif
    <form method="POST" action="{{ route('record.update.documents', $record->id) }}">
        @csrf

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="hasOtr" id="hasOtr" value="1" {{ $record->hasOtr ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}>
            <label class="form-check-label" for="hasOtr">Has Official Transcript of Records (OTR)</label>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="hasForm" id="hasForm" value="1" {{ $record->hasForm ? 'checked' : '' }} {{ $readonly ? 'disabled' : '' }}> 
            <label class="form-check-label" for="hasForm">Has Form 137</label>
        </div>

        @if ($errors->has("hasOtr") || $errors->has("hasForm"))
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @unless($readonly)
            <button type="submit" class="btn btn-primary">Save Document Status</button>
        @endunless
    </form>
@endif

@if ($record->hasOtr && $record->hasForm)
    <hr class="my-4">
    <h4 id="section3" class="mt-5">Transfer Credential Information</h4>
    @if (session('success3'))
        <div class="alert alert-success">{{ session('success3') }}</div>
    @endif
    <form method="POST" action="{{ route('record.update.transfer_info', $record->id) }}">
        @csrf

        <div class="form-group mb-3">
            <label>Sex</label><br>
            @foreach (['Male', 'Female', 'Other'] as $sex)
                <label>
                    <input type="radio" name="sex" value="{{ $sex }}"
                        {{ $record->sex === $sex ? 'checked' : '' }}
                        {{ $readonly ? 'disabled' : '' }}> {{ $sex }}
                </label>
            @endforeach
            @if ($errors->has('sex'))
                <div class="alert alert-danger mt-2">{{ $errors->first('sex') }}</div>
            @endif
        </div>

        <div class="form-group mb-3">
            <label>Semester</label><br>
            @foreach (['1st', '2nd'] as $sem)
                <label>
                    <input type="radio" name="semester" value="{{ $sem }}"
                        {{ $record->semester === $sem ? 'checked' : '' }}
                        {{ $readonly ? 'disabled' : '' }}> {{ $sem }}
                </label>
            @endforeach
            @if ($errors->has('semester'))
                <div class="alert alert-danger mt-2">{{ $errors->first('semester') }}</div>
            @endif
        </div>

        <div class="form-group mb-3">
            <label>Academic Year</label>
            <select name="schoolyear" class="form-control" {{ $readonly ? 'disabled' : '' }}>
                @foreach ($schoolYears as $sy)
                    <option value="{{ $sy }}" {{ $record->schoolyear === $sy ? 'selected' : '' }}>
                        {{ $sy }}
                    </option>
                @endforeach
            </select>
            @if ($errors->has('schoolyear'))
                <div class="alert alert-danger mt-2">{{ $errors->first('schoolyear') }}</div>
            @endif
        </div>

        <div class="form-group mb-3">
            <label>Transfer From</label>
            <input type="text" name="transferfrom" class="form-control"
                value="{{ old('transferfrom', $record->transferfrom) }}"
                {{ $readonly ? 'readonly' : '' }}>
            @unless($readonly)
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                    onclick="document.querySelector('[name=transferfrom]').value='Pangasinan State University - Urdaneta City Campus'">
                    This University
                </button>
            @endunless
            @if ($errors->has('transferfrom'))
                <div class="alert alert-danger mt-2">{{ $errors->first('transferfrom') }}</div>
            @endif
        </div>

        <div class="form-group mb-3">
            <label>Transfer To</label>
            <input type="text" name="transferto" class="form-control"
                value="{{ old('transferto', $record->transferto) }}"
                {{ $readonly ? 'readonly' : '' }}>
            @unless($readonly)
                <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                    onclick="document.querySelector('[name=transferto]').value='Pangasinan State University - Urdaneta City Campus'">
                    This University
                </button>
            @endunless
            @if ($errors->has('transferto'))
                <div class="alert alert-danger mt-2">{{ $errors->first('transferto') }}</div>
            @endif
        </div>

        <div class="form-group mb-3">
            <input id="isUndergradCheckbox" class="form-check-input" type="checkbox" name="isUndergrad" value="1"
                {{ $record->isUndergrad ? 'checked' : '' }}
                {{ $readonly ? 'disabled' : '' }}
                onchange="toggleGraduationField()" id="isUndergradCheckbox">
            <label class="form-check-label" for="isUndergradCheckbox">Is Undergraduate?</label>
        </div>

        <div class="form-group mb-4" id="graduationYearGroup" @if ($record->isUndergrad) style="display: none;" @endif>
            <label>Year Graduated</label>
            <input type="number" name="yearGraduated" class="form-control"
                value="{{ old('yearGraduated', $record->yearGraduated) }}"
                {{ $readonly ? 'readonly' : '' }}
                min="1950" max="{{ now()->year }}">
            @if ($errors->has('yearGraduated'))
                <div class="alert alert-danger mt-2">{{ $errors->first('yearGraduated') }}</div>
            @endif
        </div>
        @unless($readonly)
            <button type="submit" class="btn btn-success mt-2">Save Information</button>
        @endunless
    </form>
@endif
@if ($record->status === 'Pending' && !$readonly && $record->refnumber)
    @if ($record->status !== 'Completed')
    <hr class="my-4">
    <div class="d-flex justify-content-center gap-3 mb-3" style="max-width: 200px; margin: 0 auto;">
        <form action="{{ route('record.invalidate', Crypt::encrypt($record->id)) }}" method="post" class="flex-fill m-0">
        @csrf
        @method('PUT')
        <button type="submit" class="btn btn-outline-danger w-100">Invalidate Record</button>
        </form>
    </div>
    @endif
@endif
@if ($record->status === 'Ready' || ($readonly && $record->status === 'Completed'))
    <hr class="my-4">
    <div class="d-flex justify-content-center gap-3 mb-3" style="max-width: 600px; margin: 0 auto;">
    <a href="#" class="btn btn-outline-warning flex-fill text-center">Preview Certificate</a>
    <a href="#" class="btn btn-outline-primary flex-fill text-center">Print Certificate</a>
    @if (!$readonly)
        {{-- Only show form if record status is not "Completed" or "Failed" --}}
        @if ($record->status !== 'Completed' || $record->status !== 'Failed')
            <form action="{{ route('record.complete', Crypt::encrypt($record->id)) }}" method="post" class="flex-fill m-0">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-outline-success w-100">Complete Transaction</button>
            </form>
        @endif

        {{-- Only show form if record status is not "Completed" --}}
        @if ($record->status !== 'Completed')
            <form action="{{ route('record.invalidate', Crypt::encrypt($record->id)) }}" method="post" class="flex-fill m-0">
            @csrf
            @method('PUT')
            <button type="submit" class="btn btn-outline-danger w-100">Invalidate Record</button>
            </form>
        @endif
    @endif
    </div>
@endif
</div>
@endsection

@section('scripts')
    <script>
        function toggleGraduationField() {
            const checkbox = document.getElementById('isUndergradCheckbox');
            const group = document.getElementById('graduationYearGroup');
            group.style.display = checkbox.checked ? 'none' : 'block';
        }
        document.addEventListener('DOMContentLoaded', toggleGraduationField);
    </script>
@endsection

