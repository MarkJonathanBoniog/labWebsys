@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <h3>Dashboard</h3>
    <p>Easy to access generation of record here.</p>
    <form action="{{ route('staff.record.generate') }}" method="GET" class="d-inline">
        <button type="submit" class="btn btn-success btn-lg mt-4 d-flex align-items-center shadow-sm" style="gap: 8px; font-weight: 600; font-size: 1.1rem; border-radius: 0.375rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="white" class="bi bi-plus-circle" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
            </svg>
            Create New Record
        </button>
    </form>
    <hr>
    <h4>Your Overall Number of Transactions</h4>
    <p>Number of your transactions grouped via their status.</p>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-white bg-secondary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <p class="card-text fs-3">{{ $totalPending }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <h5 class="card-title">Ready</h5>
                    <p class="card-text fs-3">{{ $totalReady }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <p class="card-text fs-3">{{ $totalCompleted }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <h5 class="card-title">Failed</h5>
                    <p class="card-text fs-3">{{ $totalFailed }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <h4>Complete Your Past Transactions</h4>
    <p>Easy to access tables to see your latest records on the system.</p>
    <div class="row mt-3 mb-6 justify-content-between">
        <div class="col-md-6">
            <div class="card border-secondary mb-3">
                <div class="card-header bg-secondary text-white">Latest Pending Records</div>
                <ul class="list-group list-group-flush">
                    @if ($latestPendingRecords->isEmpty())
                        <li class="list-group-item">No Records Found</li>
                    @else
                        @foreach ($latestPendingRecords as $record)
                            <li class="list-group-item">
                                <a href="{{ route('staff.record.view', Crypt::encrypt($record->id)) }}" class="text-decoration-none">
                                    {{ $record->fname }} {{ $record->mname }} {{ $record->lname }} - {{ $record->created_at->format('F j, Y g:i A') }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-warning mb-3">
                <div class="card-header bg-warning text-white">Latest Ready To Claim Records</div>
                <ul class="list-group list-group-flush">
                    @if ($latestReadyRecords->isEmpty())
                        <li class="list-group-item">No Records Found</li>
                    @else
                        @foreach ($latestReadyRecords as $record)
                            <li class="list-group-item">
                                <a href="{{ route('staff.record.view', Crypt::encrypt($record->id)) }}" class="text-decoration-none">
                                    {{ $record->fname }} {{ $record->mname }} {{ $record->lname }} - {{ $record->created_at->format('F j, Y g:i A') }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <hr id="stats">

    <h4>Your Service Statistics</h4>
    <p>Analyzation tools to see your performance for every academic year.</p>
    <div class="row mt-3 mb-3">
        <div class="col-md-12 d-flex justify-content-start">
            <label for="schoolYearSelect" class="me-2 align-self-center" style="font-weight: 600;">Filter by Academic Year:</label>
            <select id="schoolYearSelect" class="form-select form-select-sm" style="max-width: 169px;">
                <option value="">All Academic Years</option>
                @foreach ($schoolYears as $year)
                    <option value="{{ $year }}" {{ (request('schoolyear') === $year) ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5>Records Created</h5>
                <select id="filterSelect" class="form-select form-select-sm" style="max-width: 150px;">
                    <option value="yearly" {{ (isset($filter) && $filter === 'yearly') ? 'selected' : '' }}>Yearly (Monthly)</option>
                    <option value="biannual" {{ (isset($filter) && $filter === 'biannual') ? 'selected' : '' }}>Bi-Annual</option>
                    <option value="quarterly" {{ (isset($filter) && $filter === 'quarterly') ? 'selected' : '' }}>Quarterly</option>
                </select>
            </div>
            <canvas id="barChart" style="max-width: 100%; max-height: 300px;"></canvas>
            <p class="text-muted mt-2">Total number of records created</p>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <h5>Records Per Semester</h5>
            <canvas id="semesterChart" style="max-width: 100%; max-height: 300px;"></canvas>
            <p class="text-muted mt-2">Distribution of records by semester, including unspecified semesters</p>
        </div>
        <div class="col-md-6">
            <h5>Record Status Distribution</h5>
            <canvas id="pieChart" style="max-width: 100%; max-height: 300px;"></canvas>
            <p class="text-muted mt-2">Distribution of records by Status</p>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <h5>Records Per Program</h5>
            <canvas id="programChart" style="max-width: 100%; max-height: 300px;"></canvas>
            <p class="text-muted mt-2">Distribution of records by Program</p>
        </div>
        <div class="col-md-6">
            <h5>Records Per Gender</h5>
            <canvas id="genderChart" style="max-width: 100%; max-height: 300px;"></canvas>
            <p class="text-muted mt-2">Distribution of records by Gender</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const filter = @json($filter ?? 'yearly');

            let labels = [];
            if (filter === 'biannual') {
                labels = ['Jan-Jun', 'Jul-Dec'];
            } else if (filter === 'quarterly') {
                labels = ['Q1', 'Q2', 'Q3', 'Q4'];
            } else {
                labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            }

            // Bar Chart - Records Created Per Period
            const barCtx = document.getElementById('barChart').getContext('2d');
            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Records Created',
                        data: [
                            @foreach ($recordsData as $period => $count)
                                {{ $count }},
                            @endforeach
                        ],
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0
                        }
                    }
                }
            });


        // Pie Chart - Record Status Distribution
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Completed', 'Pending', 'Ready','Cancelled'],
                datasets: [{
                    label: 'Status',
                    data: [
                        {{ $statusData['Completed'] }},
                        {{ $statusData['Pending'] }},
                        {{ $statusData['Ready'] }},
                        {{ $statusData['Cancelled'] }}
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(230, 229, 227, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(43, 42, 40, 0.6)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });

        // Program Chart - Records Per Program
        const programCtx = document.getElementById('programChart').getContext('2d');
        const programChart = new Chart(programCtx, {
            type: 'doughnut',
            data: {
                labels: @json($programLabels),
                datasets: [{
                    label: 'Records Per Program',
                    data: @json($programCounts),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                cutout: '50%',
            }
        });


        // Gender Chart - Records Per Gender
        const genderCtx = document.getElementById('genderChart').getContext('2d');

        // Normalize gender labels to capitalize first letter
        const normalizedGenderLabels = @json($genderLabels).map(label => {
            if (!label) return 'Unknown';
            return label.charAt(0).toUpperCase() + label.slice(1).toLowerCase();
        });

        const genderChart = new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: normalizedGenderLabels,
                datasets: [{
                    label: 'Records Per Gender',
                    data: @json($genderCounts),
                    backgroundColor: [
                        'rgba(61, 47, 12, 0.6)',  // Unknown - yellow
                        'rgba(255, 99, 132, 0.6)', // Female - red
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)', // Male - blue
                    ],
                    borderColor: [
                        'rgb(0, 0, 0)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true
            }
        });


        // Semester Chart - Records Per Semester
        const semesterCtx = document.getElementById('semesterChart').getContext('2d');
        const semesterChart = new Chart(semesterCtx, {
            type: 'bar',
            data: {
                labels: @json($semesterLabels),
                datasets: [{
                    label: 'Records Per Semester',
                    data: @json($semesterCounts),
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    });

    // Add event listener for filter change
    document.getElementById('filterSelect').addEventListener('change', function() {
        const selectedFilter = this.value;
        const url = new URL(window.location.href);
        url.searchParams.set('filter', selectedFilter);
        url.hash = 'stats';
        window.location.href = url.toString();
    });

    // Add event listener for school year change
    document.getElementById('schoolYearSelect').addEventListener('change', function() {
        const selectedYear = this.value;
        const url = new URL(window.location.href);
        if (selectedYear) {
            url.searchParams.set('schoolyear', selectedYear);
        } else {
            url.searchParams.delete('schoolyear');
        }
        url.hash = 'stats';
        window.location.href = url.toString();
    });
</script>
@endsection
