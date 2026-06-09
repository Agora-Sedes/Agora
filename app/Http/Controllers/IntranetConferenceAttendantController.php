<?php

namespace App\Http\Controllers;

class IntranetConferenceAttendantController extends Controller
{
    public function list(int $id) {
        // TODO: Conectar a la DB
        return view('intranet.conferences.attendants.list');
    }

    public function new(int $id) {
        // TODO: Conectar a la DB
        return view('intranet.conferences.attendants.new');
    }

    public function store(int $id) {
        // TODO: Conectar a la DB
        return 'Falta terminar';
    }
}
