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
    public function dashboard()
    {
        return view('staff.dashboard');
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
                $years[] = ($i - 1) . '-' . $i;
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
}
