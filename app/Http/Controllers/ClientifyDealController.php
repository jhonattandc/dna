<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientifyDealRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientifyDealController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param use App\Http\Requests\ClientifyDealRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClientifyDealRequest$request)
    {
        $validated = $request->validated();
        Log::debug("Se recibio un nuevo request", ["request"=>$validated]);
    }
}
