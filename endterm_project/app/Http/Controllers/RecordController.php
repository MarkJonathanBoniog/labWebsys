<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $record->status       = "Ready";

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
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $records = $query->latest()->paginate(15);

        $programs = Record::distinct()->pluck('program');
        $years = Record::distinct()->pluck('year')->sortDesc();

        return view('staff.index', compact('records', 'programs', 'years'));
    }
}
