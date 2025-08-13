<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class TerminologyAuditController extends Controller
{
    /**
     * Display the terminology audit page
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('TerminologyAudit');
    }
}
