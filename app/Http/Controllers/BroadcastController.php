<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class BroadcastController extends Controller
{

    public function authenticate(Request $request)
    {
        return Broadcast::auth($request);
    }
}
