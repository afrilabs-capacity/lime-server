<?php

namespace App\Services;

use App\Models\SurveyResponse;
use Illuminate\Database\Eloquent\Collection;

class SurveyResponseCalculator
{
    static function calculate(Collection $surveyResponseCollection)
    {
        $stats = [];
        foreach ($surveyResponseCollection as $surveyResponse) {


            $result = [];

            $datas = json_decode($surveyResponse->data, true);

            foreach ($datas as $data) {

                $analytics = [];
                if (
                    $data['type'] == 'data' &&
                    ($data['name'] == "radio"  || $data['name'] == "dropdown" || $data['name'] == "checkbox")
                ) {
                    // print "<h1>" . $data['label'] . "</h1>" . "</n>";

                    if ($data['name'] == "checkbox") {
                        $analytics['label'] = strip_tags($data['label']);
                        if (count($data['data']) > 0) {
                            foreach ($data['data'] as $resData) {

                                foreach ($data['options'] as $value)
                                    if ($value['value'] == $resData) {
                                        $analytics['data'][$value['option']] = 1;
                                        $analytics["name"] = $data['name'];
                                    } else {
                                        if (!isset($analytics['data'][$value['option']])) {
                                            $analytics['data'][$value['option']] = 0;
                                            $analytics["name"] = $data['name'];
                                        }
                                    }
                            }
                            //print  json_encode($data['options']);
                        }
                        // print(json_encode($data['data']));
                    } else {
                        $analytics['label'] = strip_tags($data['label']);
                        foreach ($data['options'] as $option) {
                            if ($option['value'] == $data['data']) {
                                $analytics['data'][$option['option']] = 1;
                                $analytics["name"] = $data['name'];
                            } else {
                                $analytics['data'][$option['option']] = 0;
                                $analytics["name"] = $data['name'];
                            }
                            // print $option['value'] . "</n>";
                        }
                    }
                    $result[$data['unique_key']] = $analytics;
                }
            }
            $stats[$surveyResponse->uuid] = $result;
        }

        $analyticsData['data'] = $stats;

        $analyticsNew = [];
        $analyticsCounter = 0;
        foreach ($analyticsData['data'] as $branch) {
            if (count($branch)) {
                foreach ($branch as $keyf => $fruit) {
                    $analyticsNew[$keyf]['label'] = $fruit['label'];
                    if (isset($fruit['data'])) {
                        foreach ($fruit['data'] as $key => $apple) {
                            if ($apple == 1) {
                                $analyticsNew[$keyf][$key] = isset($analyticsNew[$keyf][$key]) ? $analyticsNew[$keyf][$key] + 1 : $apple + 1;
                            } else {
                                if (!isset($analyticsNew[$keyf][$key])) {
                                    $analyticsNew[$keyf][$key] =  $apple;
                                }
                            }
                        }
                    }
                }
            }
            // $finalStats[] = $analyticsNew;

            $analyticsCounter++;
        }

        return $analyticsNew;

        // print json_encode($analytics);
    }

    // return  $stats;


}
