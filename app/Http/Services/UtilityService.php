<?php
namespace App\Http\Services;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

Class UtilityService{
    public function getAllRoles(){
        
        $roles=Role::where("guard_name","api")->get();
        return response()->json(['data'=>$roles]);
    }
}