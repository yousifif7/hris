<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function show(Payroll $payroll): View
    {
        return view('hris.payroll-show', [
            'payroll' => $payroll->load(['employee', 'generatedBy']),
        ]);
    }

    public function exportPdf(Payroll $payroll): Response
    {
        $payroll->load(['employee', 'generatedBy']);

        return Pdf::loadView('hris.payroll-pdf', [
            'payroll' => $payroll,
        ])->download('payroll-'.$payroll->id.'.pdf');
    }
}