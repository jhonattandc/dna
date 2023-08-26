<?php

namespace App\Prosegur\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Prosegur\Models\Alarm;
use App\Http\Controllers\Controller;

class AlarmsController extends Controller
{

    /**
     * Process the ajax request from the datatable plugin.
     *
     * 
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $alarms = Alarm::select(['system', 'location', 'event', 'operator', 'triggered_at', 'created_at', 'updated_at']);

            return Datatables::eloquent($alarms)
                ->editColumn('triggered_at', function ($alarm) {
                    return $alarm->triggered_at->format('d/m/Y H:i:s');
                })
                ->make(true);
        }
    }
}