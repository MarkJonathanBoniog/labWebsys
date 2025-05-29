<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Record;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;

class StaffController extends Controller
{
    public function dashboard(Request $request)
    {
        $userId = auth()->id();
        $currentYear = Carbon::now()->year;
        $filter = $request->query('filter', 'yearly');
        $schoolYearFilter = $request->query('schoolyear', null);

        // Generate list of school years (e.g. last 10 years)
        $schoolYears = [];
        for ($i = 0; $i < 10; $i++) {
            $startYear = $currentYear - $i;
            $endYear = $currentYear - $i + 1;
            $schoolYears[] = $startYear . '-' . $endYear;
        }

        // Records created per period for current year based on filter
        $recordsQuery = Record::where('user_id', $userId);

        if ($schoolYearFilter) {
            $recordsQuery->where('schoolyear', $schoolYearFilter);
        } else {
            $recordsQuery->whereYear('created_at', $currentYear);
        }

        if ($filter === 'biannual') {
            // Group by half year: 1 for Jan-Jun, 2 for Jul-Dec
            $recordsPerPeriod = $recordsQuery->select(
                DB::raw('CASE WHEN MONTH(created_at) BETWEEN 1 AND 6 THEN 1 ELSE 2 END as period'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('count', 'period')
            ->toArray();

            // Fill missing periods with 0
            $recordsData = [];
            for ($i = 1; $i <= 2; $i++) {
                $recordsData[$i] = $recordsPerPeriod[$i] ?? 0;
            }
        } elseif ($filter === 'quarterly') {
            // Group by quarter: 1 to 4
            $recordsPerPeriod = $recordsQuery->select(
                DB::raw('QUARTER(created_at) as period'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->pluck('count', 'period')
            ->toArray();

            // Fill missing quarters with 0
            $recordsData = [];
            for ($i = 1; $i <= 4; $i++) {
                $recordsData[$i] = $recordsPerPeriod[$i] ?? 0;
            }
        } else {
            // Default yearly: group by month
            $recordsPerMonth = $recordsQuery->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

            // Fill missing months with 0
            $recordsData = [];
            for ($i = 1; $i <= 12; $i++) {
                $recordsData[$i] = $recordsPerMonth[$i] ?? 0;
            }
        }

        // Record status counts
        $statusQuery = Record::where('user_id', $userId);
        if ($schoolYearFilter) {
            $statusQuery->where('schoolyear', $schoolYearFilter);
        }
        $statusCounts = $statusQuery->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Prepare status data for pie chart
        $completed = $statusCounts['Completed'] ?? 0;
        $pending = $statusCounts['Pending'] ?? 0;
        $ready = $statusCounts['Ready'] ?? 0;
        $cancelled = $statusCounts['Failed'] ?? 0;

        // Records count per program
        $programQuery = Record::where('user_id', $userId);
        if ($schoolYearFilter) {
            $programQuery->where('schoolyear', $schoolYearFilter);
        }
        $recordsPerProgram = $programQuery->select('program', DB::raw('COUNT(*) as count'))
            ->groupBy('program')
            ->orderBy('program')
            ->get();

        $programLabels = $recordsPerProgram->pluck('program')->toArray();
        $programCounts = $recordsPerProgram->pluck('count')->toArray();

        // Records count per gender
        $genderQuery = Record::where('user_id', $userId);
        if ($schoolYearFilter) {
            $genderQuery->where('schoolyear', $schoolYearFilter);
        }
        $recordsPerGender = $genderQuery->select('sex', DB::raw('COUNT(*) as count'))
            ->groupBy('sex')
            ->orderBy('sex')
            ->get();

        $genderLabels = $recordsPerGender->pluck('sex')->toArray();
        $genderCounts = $recordsPerGender->pluck('count')->toArray();

        // Records count per semester
        $semesterQuery = Record::where('user_id', $userId);
        if ($schoolYearFilter) {
            $semesterQuery->where('schoolyear', $schoolYearFilter);
        }
        $recordsPerSemester = $semesterQuery->select(
                DB::raw("COALESCE(NULLIF(semester, ''), 'Unspecified') as semester_label"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('semester_label')
            ->orderBy('semester_label')
            ->get();

        $semesterLabels = $recordsPerSemester->pluck('semester_label')->toArray();
        $semesterCounts = $recordsPerSemester->pluck('count')->toArray();

        // Calculate total counts for cards
        $totalPending = Record::where('user_id', $userId)
            ->where('status', 'Pending')->count();
        $totalReady = Record::where('user_id', $userId)
            ->where('status', 'Ready')->count();
        $totalCompleted = Record::where('user_id', $userId)
            ->where('status', 'Completed')->count();
        $totalFailed = Record::where('user_id', $userId)
            ->where('status', 'Failed')->count();


        $latestPendingRecords = Record::where('user_id', $userId)
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $latestReadyRecords = Record::where('user_id', $userId)
            ->where('status', 'Ready')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('staff.dashboard', [
            'recordsData' => $recordsData,
            'statusData' => [
                'Completed' => $completed,
                'Pending' => $pending,
                'Ready' => $ready,
                'Cancelled' => $cancelled,
            ],
            'programLabels' => $programLabels,
            'programCounts' => $programCounts,
            'genderLabels' => $genderLabels,
            'genderCounts' => $genderCounts,
            'semesterLabels' => $semesterLabels,
            'semesterCounts' => $semesterCounts,
            'filter' => $filter,
            'schoolYears' => $schoolYears,
            'totalPending' => $totalPending,
            'totalReady' => $totalReady,
            'totalCompleted' => $totalCompleted,
            'totalFailed' => $totalFailed,
            'latestPendingRecords' => $latestPendingRecords,
            'latestReadyRecords' => $latestReadyRecords,
        ]);
    }

    public function generateRecord() {
        $record = Record::create([
            'user_id'      => Auth::id(),
            'hasOtr'       => false,
            'hasForm'      => false,
            'number'       => '',
            'refnumber'    => '',
            'status'       => 'Pending', 
            'fname'        => '',
            'mname'        => '',
            'lname'        => '',
            'sex'          => '',
            'semester'     => '',
            'schoolyear'   => '',
            'program'      => '',
            'transferfrom' => null,
            'transferto'   => null,
            'isUndergrad'  => true,
            'year'         => Carbon::now()->year,
            'claimed'      => null,
        ]);

        return redirect()->route('staff.record.view', [
            'record_id' => Crypt::encrypt($record->id)
        ]);
    }

    public function showRecordView($encrypted_id)
    {
        try {
            $record_id = Crypt::decrypt($encrypted_id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Invalid record link.');
        }

        $record = Record::with('user')->findOrFail($record_id);

        if (!$record) {
            abort(404, 'Record not found.');
        }

        $user = Auth::user();

        $readonly = false;
        $message = null;

        if ($record->user_id !== $user->id && !in_array($record->status, ['Completed', 'Failed'])) {
            $readonly = true;
            $message = "This record is handled by a different staff, {$record->user->fname} {$record->user->lname}. You have a read-only access to this record. You cannot edit this record.";
        } elseif ($record->status === "Completed") {
            $readonly = true;
            $message = "This record is already completed.  You cannot further update this record.";
        } elseif ($record->status === "Failed") {
            $readonly = true;
            $message = "This record is marked as a failed transaction. You cannot edit this record.";
        }
        function generateSchoolYears() {
            $current = now()->year;
            $years = [];
            for ($i = $current; $i >= 1950; $i--) {
                $years[] = ($i) . '-' . ($i + 1);
            }
            return $years;
        }

        return view('staff.record_view', [
            'record' => $record,
            'readonly' => $readonly,
            'message' => $readonly ? $message : '',
            'programs' => DB::table('programs')->pluck('name'),
            'schoolYears' => generateSchoolYears()
        ]);

    }

    public function markAsCompleted($encrypted_id)
    {
        try {
            $id = Crypt::decrypt($encrypted_id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Invalid record link.');
        }

        $record = Record::findOrFail($id);

        if ($record->status !== 'Completed') {
            $record->status = 'Completed';
            $record->claimed = now();
            $record->save();
        }

        return back()->with('success', 'Record marked as completed.');
    }

    public function markAsFailed($encrypted_id)
    {
        try {
            $id = Crypt::decrypt($encrypted_id);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            abort(403, 'Invalid record link.');
        }

        $record = Record::findOrFail($id);

        if ($record->status !== 'Failed' && $record->refnumber == "") {
            $record->delete();
            return redirect()->route("records.index");
        } elseif ($record->status !== 'Failed') {
            $record->status = 'Failed';
            $record->save();
        }

        return back()->with('error', 'Record marked as failed.');
    }
    public function editProfile()
    {
        $user = Auth::user();
        return view('staff.settings', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [];

        // Detect which form was submitted
        if ($request->filled('fname') || $request->filled('lname') || $request->filled('email')) {
            $rules = [
                'fname' => 'required|string|max:255',
                'lname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
            ];
        } elseif ($request->filled('password')) {
            $rules = [
                'password' => 'required|confirmed|min:8',
            ];
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (isset($rules['password'])) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            return back()->with('success', 'Password updated successfully.');
        } else {
            $user->update([
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
            ]);
            return back()->with('success', 'Profile updated successfully.');
        }
    }
    public function auditStaff()
    {
        $user = Auth::user();
        return view('staff.auditStaff', compact('user'));
    }

    public function generateAuditReport(Request $request)
    {
        $request->validate([
            'year' => 'required|string',
            'range' => 'required|string',
            'semester' => 'required|in:1st,2nd,both',
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
        ]);

        // Parse school year
        [$startYear, $endYear] = explode('-', $request->year);

        $ranges = [
            'Whole Year' => ["$startYear-01-01", "$startYear-12-31"],
            'First Half' => ["$startYear-01-01", "$startYear-06-30"],
            'Second Half' => ["$startYear-07-01", "$startYear-12-31"],
            'First Quarter' => ["$startYear-01-01", "$startYear-03-31"],
            'Second Quarter' => ["$startYear-04-01", "$startYear-06-30"],
            'Third Quarter' => ["$startYear-07-01", "$startYear-09-30"],
            'Fourth Quarter' => ["$startYear-10-01", "$startYear-12-31"],
        ];

        // Determine date range
        if ($request->range === 'Manual') {
            $startDate = Carbon::parse($request->from);
            $endDate = Carbon::parse($request->to);
        } else {
            [$rawStart, $rawEnd] = $ranges[$request->range];
            $startDate = Carbon::parse($rawStart);
            $endDate = Carbon::parse($rawEnd);
        }

        // Format for display in the view
        $formattedStartDate = $startDate->format('F j, Y');
        $formattedEndDate = $endDate->format('F j, Y');

        // Base query
        $query = Record::where('user_id', Auth::id())
    ->where('status', 'Completed')
    ->whereBetween('created_at', [$startDate, $endDate])
    ;

    // ->where('created_at', $request->year)

        if ($request->semester !== 'both') {
            $query->where('semester', $request->semester);
        }

        $includeGenders = $request->has('include_genders');
        $records = $query->get();

        // Aggregation
        $data = [];
        $totals = [
            'graduate' => 0,
            'undergraduate' => 0,
            'male' => 0,
            'female' => 0,
            'other' => 0,
        ];

        foreach ($records as $record) {
            $program = $record->program ?? 'Unspecified';

            if (!isset($data[$program])) {
                $data[$program] = [
                    'graduate' => 0,
                    'undergraduate' => 0,
                    'male' => 0,
                    'female' => 0,
                    'other' => 0,
                ];
            }

            if ($record->isUndergrad) {
                $data[$program]['undergraduate']++;
                $totals['undergraduate']++;
            } else {
                $data[$program]['graduate']++;
                $totals['graduate']++;
            }

            if ($includeGenders) {
                $sex = strtolower($record->sex);
                if ($sex === 'male') {
                    $data[$program]['male']++;
                    $totals['male']++;
                } elseif ($sex === 'female') {
                    $data[$program]['female']++;
                    $totals['female']++;
                } else {
                    $data[$program]['other']++;
                    $totals['other']++;
                }
            }
        }

        return view('staff.auditTracingResult', [
            'data' => $data,
            'totals' => $totals,
            'semester' => $request->semester === 'both' ? 'Both Semesters' : ucfirst($request->semester) . ' Semester',
            'date_from' => $formattedStartDate,
            'date_to' => $formattedEndDate,
            'includeGenders' => $includeGenders,
            'schoolyear' => $request->year,
            'range' => $request->range,
        ]);
    }
}
