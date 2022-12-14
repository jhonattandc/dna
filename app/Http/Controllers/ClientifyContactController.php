<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientifyContactRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientifyContactController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param App\Http\Requests\ClientifyContactRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientifyContactRequest $request)
    {
        $validated = $request->validated();
        Log::debug("Se recibio un nuevo request", ["request"=>$validated]);
    }
}
