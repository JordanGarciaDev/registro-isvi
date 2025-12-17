<?php

namespace App\Exports;

use App\Models\Worker;
use Maatwebsite\Excel\Concerns\FromCollection;

class WorkersExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Worker::all();
    }
}
