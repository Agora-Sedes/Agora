<?php

namespace App\Http\Controllers;

class IntranetConferenceController extends Controller
{
    public function list()
    {
        // TODO: Database integration
        return view('intranet.conferences.list');
    }

    public function dashboard(int $id)
    {
        // TODO: Database integration
        return view('intranet.conferences.dashboard');
    }

    public function qrScan(int $id)
    {
        // TODO: Database integration
        return view('intranet.conferences.qr-scan');
    }
}
