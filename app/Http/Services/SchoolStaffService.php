<?php
namespace App\Http\Services;
use App\Mail\StaffMails;
use App\Models\SchoolStaff;
use App\Models\User;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mail;
use Spatie\Permission\Models\Role;

class SchoolStaffService
{
    public function registerStaff(Request $request)
    {
        $request->password = generateRandomPassword();
        $unencodedpassword = $request->password;
        try {
            DB::beginTransaction();

            $user = User::create([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'account_status' => 'ACTIVE',
            ]);
            $staff = SchoolStaff::create([
                'user_id' => $user->id,
                'date_of_employment' => $request->date_of_employment,
                'staff_id' => $request->staff_id,
                'professional_registration_number' => $request->professional_registration_number,
                'level_of_education' => $request->level_of_education,
                'years_of_experience_prior_employment' => $request->years_of_experience_prior_employment,
                'status' => 'ACTIVE',
            ]);
            $role = Role::findById($request->registered_as);
            $user->assignRole($role);
            $thestaff = SchoolStaff::with(['user'])->where("id", $staff->id)->first();
            $this->sendEmailToStaff($thestaff, $unencodedpassword);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Teacher registered successfully.',
                'data' => $thestaff,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to register teacher. Error: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function getStaff($request)
    {
        $staff = SchoolStaff::paginate($request->perPage ?? 10);
        return response()->json(['data' => $staff]);
    }
    public function sendEmailToStaff($thestaff, $unencodedPassword)
    {
        Mail::to([$thestaff->user->email])
            ->queue(new StaffMails([
                'subject' => 'Your Staff Account Created.',
                'password' => $unencodedPassword,
                'user_type' => 'staff',
                'staff' => $thestaff,
                'student' => []
            ], 'staffWelcome'));

        return response()->json(['data' => []]);
    }
}