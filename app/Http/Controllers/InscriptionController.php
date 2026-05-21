<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InscriptionController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $quantity = (int)$request->query('quantity', 1);
        $method = $request->query('method', 'card');
        return view('forminscription', ['quantity' => $quantity, 'method' => $method]);
    }
     public function store(Request $request)
    {
        $data = $request->validate([
            'participants' => 'required|array',
            'participants.*.name' => 'required|string|max:255',
            'participants.*.lastname' => 'required|string|max:255',
            'participants.*.dni' => 'required|string|max:255',
            'participants.*.email' => 'required|email|max:255',
            'participants.*.phone' => 'required|string|max:50',
        ]);

        return view('inscription-confirmation', ['participants' => $data['participants']]);
    }
}
