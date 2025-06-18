<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Spatie\Permission\Models\Role;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;
class AuthController extends Controller
{
    public function assignUserAdmin(Request $request)
    {
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
        $user = User::where("id", $user->id)->first();
        $user->assignRole(roles: 'super admin');
        return response()->json([
            'message' => 'admin created successfully',
            'user' => $user
        ], 201);
    }
    public function registerStudent(Request $request)
    {
        // Validate the request
        $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'phone_number' => 'nullable|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'student_admission_number' => 'required|string|unique:students,student_admission_number',
            'date_of_admission' => 'required|date'
        ]);

        try {
            return \DB::transaction(function () use ($request) {
                // Create user with all required fields
                $user = User::create([
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    'date_of_birth' => $request->date_of_birth,
                    'gender' => $request->gender,
                    'phone_number' => $request->phone_number,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'account_status' => 'ACTIVE'
                ]);

                // Assign student role
                $role = Role::findByName('student');
                $user->assignRole($role);

                // Create student record
                $student = \App\Models\Student::create([
                    'user_id' => $user->id,
                    'student_admission_number' => $request->student_admission_number,
                    'date_of_admission' => $request->date_of_admission,
                    'status' => 'ACTIVE'
                ]);

                return response()->json([
                    'message' => 'Student registered successfully',
                    'data' => [
                        'user' => $user,
                        'student' => $student
                    ]
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Login Failed. Wrong username/password combination',
                'error' => 'Unauthorized'
            ], 401);
        }

        // Set token TTL (in minutes) - default to 60 if not set
        JWTAuth::factory()->setTTL((int) env('JWT_TTL', 60));

        $user = auth()->user();

        // Get user permissions (direct + via roles)
        $directPermissions = $user->getAllPermissions();
        $rolePermissions = $user->getPermissionsViaRoles();
        $allPermissions = $directPermissions->merge($rolePermissions)->pluck('name')->unique();

        $role = $user->roles->isNotEmpty() ? $user->roles[0]->name : '';

        return response()->json([
            'success' => true,
            'message' => 'Login Successful',
            'token' => $token,
            'role' => $role,
            'permissions' => $allPermissions,
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'middle_name' => $user->middle_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ],
        ]);
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }
    public function health()
    {
        return response()->json(['message' => "running"]);
    }
    public function testPushNotifications(Request $request)
    {

        $title = "Hello from Laravel";
        $body = "This is a test notification";
        $tokens = [
            'eF80m0DQSLKIYbC0ZEq4QF:APA91bFS7ZX49KHjNXqvOFj157c-1xuYtjt_OYCGkRnz61t2JjjEj4bfIBVsqpBQE4h0uK5zyVb1jj5HH52-i3sLirqtUgJFMgjvDZ0J6rqPDiMUm5NqMdY',
            // add more device tokens
        ];

        $messaging = app('firebase.messaging');

        $notification = Notification::create($title, $body);
        $cloudMessage = CloudMessage::new()
            ->withNotification($notification)
            ->withData([
                'custom_key' => 'custom_value',
            ]);

        try {
            $report = $messaging->sendMulticast($cloudMessage, $tokens);

            $failures = [];
foreach ($report->failures() as $failure) {
    $failures[] = $failure;
}


            return response()->json([
                'successCount' => $report->successes()->count(),
                'failureCount' => $report->failures()->count(),
                'failures' => array_map(fn($failure) => $failure->error()->getMessage(), $failures),
            ]);
        } catch (\Throwable $e) {
            \Log::error('Push notification failed: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}


