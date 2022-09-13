<?php

namespace App\Imports;

use App\Models\MailingList;
use Maatwebsite\Excel\Concerns\ToModel;
use \Maatwebsite\Excel\Concerns\WithHeadingRow;

class MailingListImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new MailingList([
            //
            'email'     => $row[0],
        ]);
    }
}
