<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Http\Requests\CampusRequest;

use Illuminate\Http\Request;

class CampusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campus = Campus::latest()->paginate(5);
        return view('admin.campus.index',compact('campus'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.campus.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CampusRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CampusRequest $request)
    {
        Campus::create($request->validated());
        return redirect()->route('admin.campus.index')
            ->with('success','Campus created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Campus  $campus
     * @return \Illuminate\Http\Response
     */
    public function show(Campus $campus)
    {
        return view('admin.campus.show', compact('campus'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Campus  $campus
     * @return \Illuminate\Http\Response
     */
    public function edit(Campus $campus)
    {
        return view('admin.campus.edit',compact('campus'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CampusRequest  $request
     * @param  \App\Models\Campus  $campus
     * @return \Illuminate\Http\Response
     */
    public function update(CampusRequest $request, Campus $campus)
    {
        $campus->update($request->validated());
        return redirect()->route('admin.campus.index')
            ->with('success','Campus updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Campus  $campus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Campus $campus)
    {
        $campus->delete();
        return redirect()->route('admin.campus.index')
            ->with('success','Campus deleted successfully');
    }
}
