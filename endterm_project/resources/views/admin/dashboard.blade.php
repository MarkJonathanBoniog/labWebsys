@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <h3>Administrator's Dashboard</h3>
    <hr>
    <h4>Overall System Service Status</h4>
    <p>Number of all transactions done by all the staffs grouped via their status.</p>
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

    <hr id="stats">
    <h4>Overall System Service Statistics</h4>
    <p>Analyzation tools to see your performance for every academic year.</p>
    <div class="row mt-3 mb-3">
        <div class="col-md-6 d-flex align-items-center">
            <label for="schoolYearSelect" class="me-2" style="font-weight: 600;">Filter by Academic Year:</label>
            <select id="schoolYearSelect" class="form-select form-select-sm" style="max-width: 180px;">
                <option value="">All Academic Years</option>
                @foreach ($schoolYears as $year)
                    <option value="{{ $year }}" {{ (request('schoolyear') === $year) ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 d-flex align-items-center">
            <label for="staffSelect" class="me-2" style="font-weight: 600;">Filter by Staff:</label>
            <select id="staffSelect" class="form-select form-select-sm" style="max-width: 220px;">
                <option value="">All Staff</option>
                @foreach ($staffUsers as $staff)
                    <option value="{{ $staff->id }}" {{ (request('staff_id') == $staff->id) ? 'selected' : '' }}>
                        {{ $staff->fname }} {{ $staff->lname }}
                    </option>
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

    // Staff filter dropdown
    document.getElementById('staffSelect').addEventListener('change', function() {
        const url = new URL(window.location.href);
        const selectedStaffId = this.value;
        if (selectedStaffId) {
            url.searchParams.set('staff_id', selectedStaffId);
        } else {
            url.searchParams.delete('staff_id');
        }
        url.hash = 'stats';
        window.location.href = url.toString();
    });
</script>
@endsection
