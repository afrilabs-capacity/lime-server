<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    //

    public function index()
    {
        return Activity::paginate(8);
    }
}
