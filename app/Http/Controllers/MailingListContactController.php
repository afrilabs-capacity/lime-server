<?php

namespace App\Http\Controllers;

use App\Imports\MailingListImport;
use App\Models\MailingListContact;
use App\Models\MailingList;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class MailingListContactController extends Controller
{
    //

    public function index($listuuid)
    {
        $mailingList = MailingList::where('uuid', $listuuid)->firstOrFail();
        return  MailingListContact::where('mailing_list_id', $mailingList->id)->paginate(3);
    }

    public function importEmailList(Request $request)
    {
        $data = $request->validate([
            'file'  => 'file|mimes:csv,xlsx,xls',
            'list_uuid'      => 'required|string',
        ]);

        $mailingList = MailingList::where('uuid', $request->list_uuid)->firstOrFail();
        $res = Excel::toArray(new MailingListImport(), request()->file('file'));
        $refactored = [];

        foreach ($res[0] as $r) {
            $refactored[] = $r['emails'];
        }

        if (count($refactored) > 0) {

            foreach ($refactored as $contact) {
                MailingListContact::create(['email' => $contact, 'mailing_list_id' => $mailingList->id]);
            }
            return response()->json(['success' => true], 200);
        }
        return response()->json(['success' => false], 500);
    }


    public function deleteEmail($emailuuid)
    {
        MailingListContact::where('uuid', $emailuuid)->firstOrFail();
        MailingListContact::where('uuid', $emailuuid)->delete();
        return response()->json(['success' => true], 200);
    }
}
