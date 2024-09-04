<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use URL;

class TestExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection()
    // {
    //     return view('exports.invoices', [
    //         'invoices' => User::all()
    //     ]);
    // }

    public function view(): View
    {
        return view('exports.invoices', [
            'invoices' => User::all(),
            'image'=>"xbfdsgufsvg"

            // 'image'=>URL::asset('public/amc-logo/1682059750.9317.png')
        ]);
    }
}