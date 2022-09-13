<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    //

    public function create(Request $request)
    {

        $data = $request->validate([
            'name'    => 'required|string',
            'email'    => 'required|email|unique:users',
            'password'    => 'required|string',
            'roles'    => 'sometimes|array',
        ]);

        $user = User::create(array_merge($request->except(['roles', 'confirm_password']), [
            'password' => Hash::make($request->password)
        ]));

        if ($request->has('roles')) {
            foreach ($request->roles as $role) {
                $role = Role::findOrFail($role);
                $user->assignRole($role);
            }
        }


        // Todo Add event listener
        Activity::create(['event' => "User Created", 'model_uuid' => $user->uuid, 'model_id' => $user->id, 'model' => 'User']);



        return response()->json(['user' => $user], 200);
    }

    public function updateUser(Request $request)
    {

        // return $request->all();

        $user = User::where('uuid', $request->user_uuid)->firstOrFail();


        $data = $request->validate([
            'name'    => 'required|string',
            'email'    => "required|email|unique:users,email,$user->id",
            'password'    => 'sometimes|string',
            'roles'    => 'sometimes|array',
        ]);

        if ($request->has('password') && $request->email !== "") {
            $data['password'] = Hash::make($request->password);
        }


        $user->update($data);

        if (count($request->roles) > 0) {
            foreach ($user->roles as $role) {
                $user->removeRole($role);
            }
            foreach ($request->roles as $role) {
                $role = Role::findOrFail($role);
                if (!$user->hasRole($role->name)) {
                    $user->assignRole($role);
                }
            }
        } else {
            if ($user->roles()->exists()) {
                foreach ($user->roles as $role) {
                    $user->removeRole($role);
                }
            }
        }



        Activity::create(['event' => "User Updated", 'model_uuid' => $user->uuid, 'model_id' => $user->id, 'model' => 'User']);


        return response()->json(['user' => User::where('uuid', $request->user_uuid)->first()], 200);
    }

    public function index()
    {
        return User::paginate(5);
    }


    public function singleUser($uuid)
    {
        $user = User::where('uuid', $uuid)->firstOrFail();
        return response()->json(['user' => $user], 200);
    }

    public function allRoles()
    {
        return response()->json(['roles' => Role::all()], 200);
    }

    public function detachedUsers(Request $request, $uuid)
    {
        $project = Project::where('uuid', $uuid)->firstOrFail();
        if ($project->users()->exists()) {
            $detachedUsers = $project->users()->pluck('user_id');
            return response()->json(['users' => User::whereNotIn('id', $detachedUsers)->paginate(20)], 200);
        }

        return response()->json(['users' => User::paginate(20)], 200);
    }
}
