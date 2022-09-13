<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\MailingListImport;
use App\Models\MailingList;
use Maatwebsite\Excel\Facades\Excel;


class MailingListController extends Controller
{
    //

    public function index()
    {
        return MailingList::paginate(2);
    }

    public function create(Request $request)
    {

        $data = $request->validate([
            'name'    => 'required|string',
        ]);


        $list = MailingList::create($request->all());

        return response()->json(['list' => $list], 200);
    }

    public function singleEmailList(Request $request, $uuid)
    {
        return response()->json(['list' => MailingList::where('uuid', $uuid)->firstOrFail()]);
    }
}
