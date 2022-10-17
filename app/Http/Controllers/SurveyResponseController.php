<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use App\Services\SurveyResponseCalculator;
use App\Models\SurveyResponse;
use App\Models\User;

class SurveyResponseController extends Controller
{
    //
    public function index(Request $request)
    {
        if (auth()->user()->isAdmin()) {
            return response()->json(['responses' => SurveyResponse::paginate(20)]);
        } elseif (auth()->user()->isCollector()) {
            return response()->json(['responses' => SurveyResponse::where('collectr_id', auth()->user()->id)->paginate(20)]);
        }
        return response()->json(['responses' => []]);
    }

    public function create(Request $request)
    {


        $data = $request->validate([
            'uuid'    => 'required|string',
            'collector_id'    => 'required|integer',
            'data'    => 'required|json'
        ]);

        // return response()->json(['request' => $request->all()]);
        $doesCollectorExist = User::findOrFail($request->collector_id);
        $survey = Survey::where('uuid', $request->uuid)->firstOrFail();
        $surveyData = $request->all();
        $surveyData['survey_id'] = $survey->id;

        $response = SurveyResponse::create($surveyData);

        return response()->json(['response' => $response], 200);
    }

    public function createByUser(Request $request)
    {
        $data = $request->validate([
            'uuid'    => 'required|string',
            'collector_id'    => 'sometimes|integer',
            'data'    => 'required|json'
        ]);

        // return response()->json(['request' => $request->all()]);
        $survey = Survey::where('uuid', $request->uuid)->firstOrFail();
        $surveyData = $request->all();
        $surveyData['survey_id'] = $survey->id;
        $surveyData['collector_id'] = auth()->user()->id;

        $response = SurveyResponse::create($surveyData);

        return response()->json(['response' => $response], 200);
    }

    public function singleSurveyResponses(Request $request, $uuid)
    {


        $survey = Survey::where('uuid', $uuid)->firstOrFail();
        $analytics = SurveyResponseCalculator::calculate(SurveyResponse::where('survey_id', $survey->id)->get());
        return response()->json(['responses' => SurveyResponse::where('survey_id', $survey->id)->paginate(10), 'analytics' => $analytics]);
    }
}
