<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientifyRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientifyContactController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param App\Http\Controllers $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientifyRequest $request)
    {
        $validated = $request->validated();
        Log::debug("Se recibio un nuevo request", ["request"=>$validated]);
    }
}
