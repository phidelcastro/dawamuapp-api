<?php
namespace App\Http\Services;
use App\Models\Guardian;
use App\Models\GuardianEventRecipient;
use App\Models\ParentSchoolParentMessage;
use App\Models\SchoolStaff;
use App\Models\Student;
use App\Models\UserFCMToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use DB;
use Auth;
class ParentEndpointsService
{
    public function getMyStudents($request)
    {
        $parent = $this->getMyDetails()->id;
        $students = Student::where("guardian_id", $parent)->get()->toArray();
        return response()->json(['data' => $students, 'parent' => $parent]);
    }
    public function getMyDetails()
    {
        $userId = Auth::user()->id;
        $guardian = Guardian::with("user")->join("users", "users.id", "=", "guardians.user_id")->where("user_id", $userId)
            ->select("guardians.*")
            ->first();
        return $guardian;
    }

    public function getMyEventInvites($id)
    {
        $userId = generateGuardianInfo();
        $recipients = GuardianEventRecipient::join('guardian_events', 'guardian_events.id', '=', 'guardian_event_recipients.guardian_event_id')
            ->where('guardian_id', $userId->id)
            ->with('guardian.user')
            ->select(
                'guardian_events.*',
                'guardian_event_recipients.*',
                'guardian_event_recipients.id as confirmation_ref'
            )
            ->paginate();
        return response()->json(['message' => 'loaded.', 'data' => $recipients]);
    }
    public function getMyMessages()
    {
        $userId = generateGuardianInfo();
        $recipients = ParentSchoolParentMessage::join('school_parent_messages', 'school_parent_messages.id', '=', 'parent_school_parent_messages.school_parent_message_id')
            ->where('parent_id', $userId->id)
            ->with('parent.user')
            ->select(
                'school_parent_messages.*',
                'parent_school_parent_messages.*'
            )
            ->paginate();
        return response()->json(['message' => 'loaded.', 'data' => $recipients]);
    }
    public function confirmEventAttendance(Request $request)
    {

        try {
            DB::beginTransaction();

            $record = GuardianEventRecipient::find($request->confirmation_ref);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.'
                ], 404);
            }

            $record->update(['status' => 'confirmed']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attendance confirmed successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm attendance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function editEventComment(Request $request)
    {

        try {
            DB::beginTransaction();

            $record = GuardianEventRecipient::find($request->confirmation_ref);

            if (!$record) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.'
                ], 404);
            }

            $record->update(['comment' => $request->comment]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Attendance confirmed successfully.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to confirm attendance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}