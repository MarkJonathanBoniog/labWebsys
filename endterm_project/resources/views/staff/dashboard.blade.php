@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <h3>Dashboard</h3>
    <a href="{{ route('staff.record.generate') }}" class="btn btn-success mt-4">Create new record</a>
</div>
@endsection
