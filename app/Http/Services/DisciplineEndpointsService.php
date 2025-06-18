<?php
namespace App\Http\Services;
use App\Models\Guardian;
use App\Models\SchoolStaff;
use App\Models\Student;
use App\Models\StudentDiscipline;
use App\Models\UserFCMToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use DB;
use Auth;
class DisciplineEndpointsService
{
   public function getMyStudents($request){
    $parent=$this->getMyDetails()->id;
    $students=Student::where("guardian_id",$parent)->get()->toArray();
    return response()->json(['data'=>$students,'parent'=>$parent]);
   }
   public function getMyDetails(){
    $userId= Auth::user()->id;
    $guardian = Guardian::with("user")->join("users","users.id","=","guardians.user_id")->where("user_id",$userId)
    ->select("guardians.*")
    ->first();
    return $guardian;
   }
   public function saveIndisciplineCase(Request $request){
            return DB::transaction(function () use ($request) {
            $data = $request->only([
                'student_id',
                'location',
                'offense',
                'action_taken',
                'parent_notification',
                'follow_up',                
                'notes',
            ]);
            $data['reported_by']=Auth::id();
            $imageUrls = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('student-disciplines', 'public');
                    $imageUrls[] = Storage::url($path);
                }
            }
            $data['images'] = $imageUrls;
            return StudentDiscipline::create($data);
        });
   }
}