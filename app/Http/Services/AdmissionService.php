<?php
namespace App\Http\Services;
use App\Mail\ParentMails;
use App\Mail\StudentMails;
use App\Models\Guardian;
use App\Models\OtherContact;
use App\Models\Student;
use App\Models\StudentSchoolClassStream;
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
                'first_name' => $data['guardian']['first_name'],
                'middle_name' => $data['guardian']['middle_name'] ?? null,
                'last_name' => $data['guardian']['last_name'],
                'date_of_birth' => $data['guardian']['dob'],
                'gender' => $data['guardian']['gender'],
                'phone_number' => $data['guardian']['phone'],
                'email' => $data['guardian']['email'],
                'password' => Hash::make($gunencodedpss),
                'account_status' => 'ACTIVE',
            ]);

            $role = Role::findByName("parent");
            $userG->assignRole($role);

            // 1. Create Guardian
            $guardian = Guardian::create([
                'user_id' => $userG->id,
                'relationship' => $data['guardian']['relationship'],
                'phone' => $data['guardian']['phone'],
                'town' => $data['guardian']['town'],
                'city' => $data['guardian']['town'],
                'address' => $data['guardian']['address'],
                'box_number' => $data['guardian']['box_number'],
                'zip_code' => $data['guardian']['zip_code'],
            ]);

            //send the parent welcome mail
            $thisGuardian = Guardian::where("id", $guardian->id)->with(['user'])->first();
            $this->sendEmailToParent($thisGuardian, $gunencodedpss);
            //send the parent welcome mail

            // 2. Add Students
            $st =1;
            foreach ($data['students'] as $studentData) {
                //generate student admission number
                $generatestudentadmissionNumber = generateStdAdmission();
                //generate student admission number
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

                $roles = Role::findByName("student");
                $userS->assignRole($roles);
                $createdst = Student::create([
                    'user_id' => $userS->id,
                    'date_of_admission' => $studentData['stream_admission_date'],
                    'status' => 'ACTIVE',
                    'admitted_on_school_class_id' => $studentData['class_id'],
                    'student_admission_number' => $generatestudentadmissionNumber,
                    'guardian_id' => $guardian->id
                ]);
                //register to stream
                StudentSchoolClassStream::create([
                  'student_id'=>$createdst ->id,
                  'school_class_stream_id'=> $studentData['stream_id'],
                  'start_date' => $studentData['stream_admission_date'] ?? date("Y-m-d"),
                  'status'=>'ACTIVE'
                ]);
                     
                //send the student welcome mail
                $thisStudent = Student::where("id", $createdst->id)->with(['user'])->first();
                $this->sendEmailToStudent($thisStudent, $stunencodedpss);
                //send the student welcome mail
                foreach ($data['otherContacts'] as $contactData) {
                    OtherContact::create([
                        'first_name' => $contactData['first_name'],
                        'last_name' => $contactData['last_name'],
                        'relationship' => $contactData['relationship'],
                        'phone' => $contactData['phone'],
                        'email' => $contactData['email'],
                        'student_id' => $createdst->id,
                        'guardian_id' => $guardian->id
                    ]);
                }
                $st++;
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
            ], 'studentWelcome'));

        return response()->json(['data' => []]);
    }

}