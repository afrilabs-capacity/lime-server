<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    //

    public function index()
    {

        return  Project::latest()->paginate(5);
    }

    public function allProjects()
    {

        return  Project::latest()->paginate(100);
    }

    public function create(Request $request)
    {

        $data = $request->validate([
            'name'    => 'required|string'
        ]);

        if (Project::where('name', $request->name)->exists()) {
            return response()->json(['duplicate' => []], 422);
        }


        $list = Project::create($request->all());

        return response()->json(['list' => $list], 200);
    }

    public function singleProject(Request $request, $uuid)
    {
        return response()->json(['project' => Project::where('uuid', $uuid)->firstOrFail()]);
    }

    // public function attachSurvey(Request $request)
    // {
    //     $project = Project::where('uuid', $request->projectuuid)->firstOrFail();
    //     $survey = Survey::where('uuid', $request->surveyuuid)->firstOrFail();
    //     $project->surveys()->attach($survey->id);
    //     return response()->json(['success' => true], 200);
    // }

    // public function detachSurvey(Request $request)
    // {
    //     $project = Project::where('uuid', $request->projectuuid)->firstOrFail();
    //     $survey = Survey::where('uuid', $request->surveyuuid)->firstOrFail();
    //     $project->surveys()->detach($survey->id);
    //     return response()->json(['success' => true], 200);
    // }

    public function projectSurveys($projectuuid)
    {
        $projectSurveys = Project::where('uuid', $projectuuid)->first()->surveys()->paginate(5);
        return response()->json(['project_surveys' => $projectSurveys], 200);
    }


    public function attachUser(Request $request)
    {

        $project = Project::where('uuid', $request->projectuuid)->firstOrFail();
        $user = User::where('uuid', $request->useruuid)->firstOrFail();
        $project->users()->attach($user->id);
        return response()->json(['success' => true], 200);
    }

    public function detachUser(Request $request)
    {

        $project = Project::where('uuid', $request->projectuuid)->firstOrFail();
        $user = User::where('uuid', $request->useruuid)->firstOrFail();
        $project->users()->detach($user->id);
        return response()->json(['success' => true], 200);
    }

    public function projectUsers($projectuuid)
    {
        $projectUsers = Project::where('uuid', $projectuuid)->first()->users()->paginate(1);
        return response()->json(['project_users' => $projectUsers], 200);
    }

    public function deleteProject($projectuuid)
    {

        $project = Project::where('uuid', $projectuuid)->firstOrFail();
        if ($project->surveys()->exists()) {
            foreach ($project->surveys()->get() as $survey) {
                $survey->delete();
            }
        }

        if ($project->users()->exists()) {
            foreach ($project->users()->get() as $user) {
                $project->users()->detach($user->id);
            }
        }

        $project->delete();
    }
}
