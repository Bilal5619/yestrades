<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view(
            'company.reports.index'
        );
    }
}