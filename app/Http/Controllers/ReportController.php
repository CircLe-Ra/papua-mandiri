<?php

namespace App\Http\Controllers;


use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function registration($id)
    {
        return SnappyPdf::loadView('livewire.pages.participant.report.proof')->stream('Bukti.pdf');
//        return view('livewire.pages.participant.report.proof', ['id' => $id]);
    }
}
