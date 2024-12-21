<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\StepTemp;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

class PaymentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            $participant = Participant::find($request->id);
            $participant->payment = 'paid';
            $participant->status = 'proceed';
            $participant->amount = $request->amount;
            $participant->save();
            StepTemp::where('user_id', $participant->user_id)->where('reception_id', $participant->reception_id)->update(['step' => 4]);
            return response()->json(['status' => 'success']);
        }catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
