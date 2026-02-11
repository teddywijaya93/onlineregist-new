<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilResikoController extends Controller
{
    // public function submit(Request $request) {
    //     $request->validate([
    //         'q1' => 'required|integer',
    //         'q2' => 'required|integer',
    //         'q3' => 'required|integer',
    //         'q4' => 'required|integer',
    //         'q5' => 'required|integer',
    //     ]);

    //     $total = $request->q1
    //            + $request->q2
    //            + $request->q3
    //            + $request->q4
    //            + $request->q5;

    //     if ($total >= 5 && $total <= 8) {
    //         $profil = 'Konservatif';
    //     } elseif ($total >= 9 && $total <= 14) {
    //         $profil = 'Moderat';
    //     } else {
    //         $profil = 'Agresif';
    //     }

    //     session([
    //         'profil_risiko_total' => $total,
    //         'profil_risiko_hasil' => $profil,
    //     ]);

    //     return redirect()->route('profil-resiko-result');
    // }

    // public function result() {
    //     if (!session()->has('profil_risiko_total')) {
    //         return redirect()->route('profil-resiko');
    //     }

    //     return view('profil-resiko-result');
    // }
}