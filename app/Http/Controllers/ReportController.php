<?php

namespace App\Http\Controllers;


use App\Models\Participant;
use App\Models\Reception;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function registration($id)
    {
        $pdf = Pdf::loadView('livewire.pages.participant.report.proof');
        return $pdf->stream('registration.pdf');
//        return view('livewire.pages.participant.report.proof', ['id' => $id]);
    }

    public function absent($period, $time, $program, $level)
    {
        $reception = Reception::where('id', $time)->first();
        $participants = Participant::with('user.personal_data', 'program')
            ->where('reception_id', $reception->id)
            ->where('program_id', $program)
            ->where('level', $level)
            ->where('payment', 'paid')
            ->latest()->get();

        try {
            $pdf = Pdf::loadView('livewire.pages.leader.report.absent', [
                'participants' => $participants,
                'period' => $period,
                'reception' => $reception,
                'program' => $program,
                'level' => $level
            ])->setPaper('a4', 'landscape');
            return $pdf->download('absent.pdf');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
//        return view('livewire.pages.leader.report.absent', ['participants' => $participants, 'period' => $period, 'reception' => $reception, 'program' => $program, 'level' => $level]);
    }
}
