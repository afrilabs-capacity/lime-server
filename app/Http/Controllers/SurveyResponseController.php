<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;
use App\Services\SurveyResponseCalculator;
use App\models\SurveyResponse;

class SurveyResponseController extends Controller
{
    //
    public function index(Request $request)
    {
        return response()->json(['responses' => SurveyResponse::paginate(20)]);
    }

    public function create(Request $request)
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
