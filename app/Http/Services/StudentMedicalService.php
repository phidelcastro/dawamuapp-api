<?php
namespace App\Http\Services;
use App\Models\Guardian;
use App\Models\SchoolStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Models\StudentMedicalHistory;
use DB;
class StudentMedicalService
{ 
    public function saveStudentMedicalRecords(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'complaint' => 'required|string',
            'procedure' => 'nullable|string',
            'medicines' => 'nullable|string',
            'next_checkup_date' => 'nullable|date',
            'alert_parent' => 'boolean',
            'refer_external' => 'boolean',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        DB::beginTransaction();
    
        try {

   
            $record = StudentMedicalHistory::create($validated);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('medical_images', 'public');        
                    $record->studentMedicalHistoryImages()->create([
                        'path' => $path,
                    ]);
                }
            }
            DB::commit();
    
            return response()->json([
                'message' => 'Medical record saved successfully.',
                'record' => $record->load('student')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
    
            return response()->json([
                'message' => 'Failed to save medical record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function updateStudentMedicalRecords(Request $request,$id)
    {
        $record = StudentMedicalHistory::findOrFail($id);

        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'complaint' => 'nullable|string',
            'procedure' => 'nullable|string',
            'medicines' => 'nullable|string',
            'next_checkup_date' => 'nullable|date',
            'alert_parent' => 'boolean',
            'refer_external' => 'boolean',
        ]);
    
        DB::beginTransaction();
    
        try {
            $record->update($data);
    
            DB::commit();
    
            return response()->json([
                'message' => 'Medical record Updated successfully.',
                'record' => $record->load('student')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
    
            return response()->json([
                'message' => 'Failed to save medical record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getMedicalRecords(Request $request){

        $query = StudentMedicalHistory::with('student.user');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('admission_number', 'like', "%$search%")
                  ->orWhere('first_name', 'like', "%$search%")
                  ->orWhere('middle_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%");
            });
        }    
        $perPage = $request->input('perPage', 10);
        $records = $query->latest()->paginate($perPage);    
        return response()->json(['records' => $records]);

    }
   
}