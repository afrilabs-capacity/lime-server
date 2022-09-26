<?php

namespace App\Services;

class Helpers
{

    public static function resolveSurveyLabelInconsistencies($rawSurveyDataDecoded, $datas)
    {
        foreach ($rawSurveyDataDecoded as $key1 => $rsdd) {
            if (array_key_exists('label', $rsdd)) {
                foreach ($datas as $key => $dtas) {
                    if ($rsdd['unique_key'] == $dtas["unique_key"]) {
                        // $datas["unique_key"]["label"] = $rsdd["label"];
                        // $datas[$key]['label'] = $rsdd["label"];
                        $datas[$key]['label'] = $rsdd['label'];
                    }
                }
            } else {
                continue;
            }
        }

        return $datas;
    }


    public static function removeWidgetFromResponseIfNotInSurvey($rawSurveyDataDecoded, $datas)
    {
        foreach ($datas as $key => $dtas) {
            if (array_key_exists('label', $dtas)) {

                $keyFound = false;

                foreach ($rawSurveyDataDecoded as $key1 => $rsdd) {
                    if ($rsdd['unique_key'] == $dtas["unique_key"]) {
                        $keyFound = true;
                    }
                }
                if (!$keyFound) {
                    unset($datas[$key]);
                }
            } else {
                continue;
            }
        }

        return $datas;
    }


    public static function addWidgetToResponseIfInSurvey($rawSurveyDataDecoded, $datas)
    {
        foreach ($rawSurveyDataDecoded as $key1 => $rsdd) {
            if (array_key_exists('label', $rsdd)) {
                $keyFound = false;
                foreach ($datas as $key => $dtas) {
                    if ($rsdd['unique_key'] == $dtas["unique_key"]) {
                        $keyFound = true;
                    }
                }
                if (!$keyFound) {
                    // print $rsdd['label'];
                    $rsdd[] = ['data' => []];
                    $datas[] = $rsdd;
                }
            } else {
                continue;
            }
        }

        return $datas;
    }
}
