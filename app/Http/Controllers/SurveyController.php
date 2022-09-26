<?php

namespace App\Http\Controllers;

use App\Models\MailingList;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SurveyInvite;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;

class SurveyController extends Controller
{
    //
    public function index(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            return response()->json(['surveys' => Survey::latest()->paginate(2)]);
        } elseif (auth()->user()->isCollector()) {
            return response()->json(['surveys' => auth()->user()->surveys()->paginate(1)]);
        }
    }

    public function create(Request $request)
    {

        $data = $request->validate([
            'name'    => 'required|string',
            'project_uuid'    => 'nullable|string'
        ]);

        if ($request->project_uuid !== null) {
            $project = Project::where('uuid', $request->project_uuid)->firstOrFail();

            if (Survey::where('name', $request->name)->where('project_id', $project->id)->exists()) {
                return response()->json(['duplicate' => []], 422);
            }
            $survey = Survey::create(array_merge($request->except(['project_uuid']), [
                'project_id' => $project->id
            ]));
        } else {
            $survey = Survey::create($request->except(['project_uuid']));
        }


        Activity::create(['event' => "Survey Created", 'model_uuid' => $survey->uuid, 'model_id' => $survey->id, 'model' => 'Survey']);

        return response()->json(['survey' => $survey], 200);
    }


    public function cloneSurvey(Request $request)
    {

        $data = $request->validate([
            'name'    => 'required|string',
            'surveyuuid' => 'required|string',
            'projectuuid' => 'required|string'
        ]);

        $project = Project::where('uuid', $request->projectuuid)->firstOrFail();
        $surveyToClone = Survey::where('uuid', $request->surveyuuid)->firstOrFail();
        $newSurvey  = Survey::create(array_merge($request->except(['surveyuuid', 'projectuuid']), [
            'project_id' => $project->id
        ]));


        Survey::where('uuid', $newSurvey->uuid)->update(['data' => $surveyToClone->data]);
        $survey = Survey::where('uuid', $newSurvey->uuid)->firstOrFail();
        return response()->json(['survey' => $survey], 200);
    }

    public function singleSurvey(Request $request, $uuid)
    {
        return response()->json(['survey' => Survey::where('uuid', $uuid)->firstOrFail()]);
    }

    public function updateSurvey(Request $request)
    {
        // return $request->all();

        if ($request->projectuuid !== null) {
            $startDate = Carbon::createFromFormat('m/d/Y', Carbon::parse($request->start_date)->format('m/d/Y'));
            $endDate = Carbon::createFromFormat('m/d/Y', Carbon::parse($request->end_date)->format('m/d/Y'));
            $project = Project::where('uuid', $request->projectuuid)->firstOrFail();
            Survey::where('uuid', $request->surveyuuid)->where('project_id', $project->id)->update(['data' => $request->data, 'start_date' =>  $startDate, 'end_date' =>  $endDate, 'location' => $request->location]);
        } else {
            Survey::where('uuid', $request->surveyuuid)->update(['data' => $request->data]);
        }

        $survey = Survey::where('uuid', $request->surveyuuid)->first();
        Activity::create(['event' => "Survey Updated", 'model_uuid' => $survey->uuid, 'model_id' => $survey->id, 'model' => 'Survey']);
        return response()->json([], 200);
    }

    public function deleteSurvey($surveyuuid)
    {
        $survey = Survey::where('uuid', $surveyuuid)->firstOrFail();
        Activity::where('model', 'Survey')->where('model_uuid', $survey->uuid)->delete();
        Survey::where('uuid', $surveyuuid)->delete();
    }


    public function detachedSurveys(Request $request, $uuid)
    {
        $project = Project::where('uuid', $uuid)->firstOrFail();
        if ($project->surveys()->exists()) {
            $detachedSurveys = $project->surveys()->pluck('survey_id');
            return response()->json(['surveys' => Survey::whereNotIn('id', $detachedSurveys)->paginate(20)], 200);
        }

        return response()->json(['surveys' => Survey::paginate(20)], 200);
    }

    public function distribute(Request $request)
    {
        // $visitor = Visitor::create($request->all());
        $emailList = MailingList::where('uuid', $request->list_uuid)->first();

        if ($emailList) {
            foreach ($emailList->contacts()->get() as $contact) {
                Notification::route('mail', $contact->email)->notify(new SurveyInvite($request->all()));
            }
        }

        return response()->json(['count' => $request->list_uuid], 200);
    }

    public function attachUser(Request $request)
    {

        $survey = Survey::where('uuid', $request->surveyuuid)->firstOrFail();
        $user = User::where('uuid', $request->useruuid)->firstOrFail();
        $survey->users()->attach($user->id);
        return response()->json(['success' => true], 200);
    }

    public function detachUser(Request $request)
    {

        $survey = Survey::where('uuid', $request->surveyuuid)->firstOrFail();
        $user = User::where('uuid', $request->useruuid)->firstOrFail();
        $survey->users()->detach($user->id);
        return response()->json(['success' => true], 200);
    }

    public function surveyUsers($surveyuuid)
    {
        $surveyUsers = Survey::where('uuid', $surveyuuid)->first()->users()->paginate(5);
        return response()->json(['survey_users' => $surveyUsers], 200);
    }

    public function detachedUsers(Request $request, $uuid)
    {
        $survey =  Survey::where('uuid', $uuid)->firstOrFail();
        if ($survey->users()->exists()) {
            $detachedUsers = $survey->users()->pluck('user_id');
            return response()->json(['users' => User::whereNotIn('id', $detachedUsers)->paginate(20)], 200);
        }

        return response()->json(['users' => User::paginate(20)], 200);
    }

    public function getSurveyByDate(Request $request)
    {
        // return $request->all();
        $startDate = Carbon::createFromFormat('d-m-Y', Carbon::parse($request->startDate)->format('d-m-Y'));
        $endDate = Carbon::createFromFormat('d-m-Y', Carbon::parse($request->endDate)->format('d-m-Y'));

        $survey = Survey::where('uuid', $request->surveyuuid)->first();

        $reports = $survey->responses()->whereBetween('created_at', [$startDate, $endDate])->get();

        return response()->json(['reports' => $reports], 200);
    }
}
