<?php

namespace App\Http\Controllers;
use PDF;
use App\Models\User;
use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    public function updateBasic(Request $request, $record_id)
    {
        $validator = Validator::make($request->all(), [
            'fname'   => 'required|string|max:255',
            'mname'   => 'nullable|string|max:255',
            'lname'   => 'required|string|max:255',
            'program' => 'required|string|exists:programs,name',
        ], [
            'fname.required'   => 'First name is required.',
            'lname.required'   => 'Last name is required.',
            'program.required' => 'Please select a program.',
            'program.exists'   => 'The selected program is invalid.',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#section1')
                ->withErrors($validator)
                ->withInput();
        }

        $record = Record::findOrFail($record_id);

        // Only allow program change if number and refnumber are NOT yet set
        if (empty($record->number) || empty($record->refnumber)) {
            $record->program = $request->program;

            $year = $record->year;
            $count = Record::where('program', $request->program)
                        ->where('year', $year)
                        ->count();

            $number = $count + 1;

            $program = DB::table('programs')->where('name', $request->program)->first();
            $abbrev = strtoupper($program->abbrev ?? 'UNKNOWN');

            $refnumber = "{$abbrev}-{$number}-{$year}";

            // Check for uniqueness
            $exists = Record::where('refnumber', $refnumber)->exists();
            if ($exists) {
                return redirect()->to(url()->previous() . '#section1')
                ->withErrors([
                    'refnumber' => 'The generated reference number already exists. Please try again.',
                ])
                ->withInput();
            }

            $record->number = $number;
            $record->refnumber = $refnumber;
        } else {
            // Prevent program change after refnumber is set
            if (($record->number && $record->refnumber) &&
                strtolower(trim($record->program)) !== strtolower(trim($request->program))) {
                return redirect()->to(url()->previous() . '#section1')
                ->withErrors([
                    'program' => 'You cannot change the program after the reference number has been generated.',
                ])
                ->withInput();
            }
        }

        // Always update name fields
        $record->fname = $request->fname;
        $record->mname = $request->mname;
        $record->lname = $request->lname;

        $record->save();

        return redirect()->to(url()->previous() . '#section1')
    ->with('success1', 'Basic information updated successfully.');
    }


    public function updateDocuments(Request $request, $record_id)
    {
        $record = Record::findOrFail($record_id);

        $validator = Validator::make($request->all(), [
            'hasOtr'  => 'nullable|boolean',
            'hasForm' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#section2')
                ->withErrors($validator)
                ->withInput();
        }

        // Set values (checkboxes send value only if checked)
        $record->hasOtr = $request->has('hasOtr') ? true : false;
        $record->hasForm = $request->has('hasForm') ? true : false;

        $record->save();

        return redirect()->to(url()->previous() . '#section2')
    ->with('success2', 'Document status updated successfully.');
    }


    public function updateTransferInfo(Request $request, $record_id)
    {
        $record = Record::findOrFail($record_id);

        $validator = Validator::make($request->all(), [
            'sex'           => 'required|in:Male,Female,Other',
            'semester'      => 'required|in:1st,2nd',
            'schoolyear'    => 'required|string',
            'transferfrom'  => 'nullable|string|max:255',
            'transferto'    => 'nullable|string|max:255',
            'isUndergrad'   => 'nullable|boolean',
            'yearGraduated' => 'nullable|integer|min:1950|max:' . now()->year,
            'address'       => 'nullable|string|max:255',
        ], [
            'sex.required'           => 'Please select a valid sex.',
            'sex.in'                 => 'The selected sex is invalid. Please choose Male, Female, or Other.',
            
            'semester.required'      => 'Please select a semester.',
            'semester.in'            => 'The selected semester is invalid. Please choose either 1st or 2nd semester.',

            'schoolyear.required'    => 'Please select an academic year.',
            'schoolyear.string'      => 'The academic year must be a valid string value.',

            'transferfrom.string'    => 'The "Transfer From" field must be a valid text.',
            'transferfrom.max'       => 'The "Transfer From" field must not exceed 255 characters.',

            'transferto.string'      => 'The "Transfer To" field must be a valid text.',
            'transferto.max'         => 'The "Transfer To" field must not exceed 255 characters.',

            'isUndergrad.boolean'    => 'The undergraduate field must be a valid true or false value.',

            'yearGraduated.integer'  => 'The year graduated must be a valid year.',
            'yearGraduated.min'      => 'The year graduated cannot be earlier than 1950.',
            'yearGraduated.max'      => 'The year graduated cannot be later than the current year.',

            'address.string'         => 'The address must be a valid text.',
            'address.max'            => 'The address must not exceed 255 characters.',
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#section3')
                ->withErrors($validator)
                ->withInput();
        }

        $record->sex           = $request->sex;
        $record->semester      = $request->semester;
        $record->schoolyear    = $request->schoolyear;
        $record->transferfrom  = $request->transferfrom;
        $record->transferto    = $request->transferto;
        $record->isUndergrad   = $request->has('isUndergrad');
        $record->yearGraduated = $record->isUndergrad ? null : $request->yearGraduated;
        $record->address       = $request->address;
        $record->status        = "Ready";

        $record->save();

        return redirect()->to(url()->previous() . '#section3')
            ->with('success3', 'Transfer credential info updated.');
    }

    public function index(Request $request)
    {
        Record::whereNull('refnumber')
        ->orWhere('refnumber', '')
        ->where('status', '!=', 'Failed')
        ->each(function ($record) {
            $record->delete();
        });

        $query = Record::with('user');

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }
        if ($request->filled('from_date')) {
    $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('gender')) {
            $query->where('sex', $request->gender);
        }

        if ($request->filled('schoolyear')) {
            $query->where('schoolyear', $request->schoolyear);
        }

        if ($request->filled('name')) {
            $search = $request->name;
            $query->where(function ($q) use ($search) {
                $q->where('fname', 'like', "%$search%")
                ->orWhere('mname', 'like', "%$search%")
                ->orWhere('lname', 'like', "%$search%");
            });
        }

        if ($request->filled('staff')) {
            $query->where('user_id', $request->staff);
        }

        $records = $query->latest()->paginate(15);

        $programs = Record::distinct()->pluck('program');
        $staffUsers = User::where('role', 'staff')->get();

        return view('staff.index', compact('records', 'programs', 'staffUsers'));
    }

    public function previewCertificate($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $record = Record::findOrFail($id);

        if (!in_array($record->status, ['Ready', 'Completed'])) {
            abort(403, 'Certificate not available for this record.');
        }

        // Determine graduate or undergraduate view
        if ($record->isUndergrad == '0') {
            $view = 'staff.certificates.certGraduate';
        } else {
            $view = 'staff.certificates.certUnderGrad';
        }

        $name = strtoupper("{$record->fname} {$record->mname} {$record->lname}");
        $address = $record->address ?? '__________________________';
        $tcno = $record->refnumber;
        $registrarName = $record->user->fname . " " . $record->user->mname . " " . $record->user->lname . ", " . $record->user->title;
        $sy = $record->schoolyear;
        $program = $record->program;
        $day = now()->format('j');
        $month = now()->format('F');
        $year = now()->format('Y');

        return view($view, compact('name', 'address', 'tcno', 'registrarName', 'day', 'month', 'year', 'sy', 'program'));
    }

    public function printCertificate($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $record = Record::findOrFail($id);

        if (!in_array($record->status, ['Ready', 'Completed'])) {
            abort(403, 'Certificate not available for this record.');
        }

        if ($record->isUndergrad == '0') {
            $view = 'staff.certificates.graduate';
        } else {
            $view = 'staff.certificates.undergraduate';
        }

        $name = strtoupper("{$record->fname} {$record->mname} {$record->lname}");
        $address = $record->address ?? '__________________________';
        $tcno = $record->refnumber;
        $registrarName = $record->user->fname . " " . $record->user->mname . " " . $record->user->lname . ", " . $record->user->title;
        $sy = $record->schoolyear;
        $program = $record->program;
        $day = now()->format('j');
        $month = now()->format('F');
        $year = now()->format('Y');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, compact('name', 'address', 'tcno', 'registrarName', 'day', 'month', 'year', 'sy', 'program'))->setPaper('a4', 'portrait');

        $filename = 'certificate_' . strtolower($record->lname) . '_' . ($record->fname) . '.pdf';

        return $pdf->download($filename);
    }
}
