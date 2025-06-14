<?php
namespace App\Http\Services;
use App\Mail\ParentMails;
use App\Mail\StudentMails;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mail;
use Spatie\Permission\Models\Role;
use DB;

class AdmissionService
{
    public function newAdmission(Request $request)
    {
        $data = $request->all();

        DB::beginTransaction();

        try {

            $gunencodedpss = generateRandomPassword();
          $userG = User::create([
    'first_name'    => $data['gurdian']['first_name'],
    'middle_name'   => $data['gurdian']['middle_name'] ?? null,
    'last_name'     => $data['gurdian']['last_name'],
    'date_of_birth' => $data['gurdian']['dob'],
    'gender'        => $data['gurdian']['gender'],
    'phone_number'  => $data['gurdian']['phone'],
    'email'         => $data['gurdian']['email'],
    'password'      => Hash::make($gunencodedpss),
    'account_status'=> 'ACTIVE',
]);

            $role = Role::findByName("parent");
            $userG->assignRole($role);

            // 1. Create Guardian
            $guardian = Guardian::create([
                'user_id' => $userG->id,
                'relationship' => $data['gurdian']['relationship'],
                'phone' => $data['gurdian']['phone'],
                'town' => $data['gurdian']['town'],
                'address' => $data['gurdian']['address'],
                'box_number' => $data['gurdian']['box_number'],
                'zip_code' => $data['gurdian']['zip_code'],
            ]);

            //send the parent welcome mail
            $thisGuardian = Guardian::where("id", $guardian->id)->with(['user'])->first();
            $this->sendEmailToParent($thisGuardian, $gunencodedpss);
            //send the parent welcome mail

            // 2. Add Students
            foreach ($data['students'] as $studentData) {
                $stunencodedpss = generateRandomPassword();
                $userS = User::create([
                    'first_name' => $studentData['first_name'],
                    'middle_name' => $studentData['middle_name'],
                    'last_name' => $studentData['last_name'],
                    'date_of_birth' => $studentData['dob'],
                    'gender' => $studentData['gender'],
                    'phone_number' => $studentData['phone_number'],
                    'email' => $studentData['email'],
                    'password' => Hash::make($stunencodedpss),
                    'account_status' => 'ACTIVE',
                ]);
                $createdst = Student::create([
                    'user_id' => $userS->id,
                    'school_admission_date' => $studentData['school_admission_date'],
                    'stream_admission_date' => $studentData['stream_admission_date'],
                    'dob' => $studentData['dob'],
                    'gender' => $studentData['gender'],
                    'class_id' => $studentData['class_id'],
                    'stream_id' => $studentData['stream_id'],
                    'profile_pic' => $studentData['profilePicUrl'] ?? null,
                    'gurdian_id' => $guardian->id
                ]);
                //send the student welcome mail
                $thisStudent = Student::where("id", $createdst->id)->with(['user'])->first();
                $this->sendEmailToStudent($thisStudent, $stunencodedpss);
                //send the student welcome mail
            }

            foreach ($data['otherContacts'] as $contactData) {
                $guardian->otherContacts()->create([
                    'first_name' => $contactData['first_name'],
                    'last_name' => $contactData['last_name'],
                    'relationship' => $contactData['relationship'],
                    'phone' => $contactData['phone'],
                    'email' => $contactData['email'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Registration successful.',
                'guardian_id' => $guardian->id
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Registration failed.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function getParents()
    {


    }
    public function sendEmailToParent($theguardian, $unencodedPassword)
    {
        Mail::to([$theguardian->user->email])
            ->queue(new ParentMails([
                'subject' => 'Your Dawamu Parent Account Created.',
                'password' => $unencodedPassword,
                'user_type' => 'parent',
                'guardian' => $theguardian,
                'student' => []
            ], 'parentWelcome'));

        return response()->json(['data' => []]);
    }

    public function sendEmailToStudent($thestudent, $unencodedPassword)
    {
        Mail::to([$thestudent->user->email])
            ->queue(new StudentMails([
                'subject' => 'Your Dawamu Student Account Created.',
                'password' => $unencodedPassword,
                'user_type' => 'student',
                'student' => $thestudent
            ], 'parentWelcome'));

        return response()->json(['data' => []]);
    }

}