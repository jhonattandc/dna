<?php

namespace App\Q10\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Q10\Models\Campus;
use App\Q10\Models\Periodo;
use App\Http\Controllers\Controller;

class PeriodosController extends Controller
{

    /**
     * Process the ajax request from the datatable plugin.
     *
     * 
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $campus = Campus::find(request()->campus);
            $periodos = $campus->periodos()->select([
                'Consecutivo', 
                'Nombre',
                'Estado',
                'Fecha_inicio',
                'Fecha_fin',
            ]);

            return Datatables::eloquent($periodos)
                ->editColumn('Estado', function ($periodo) {
                    return $periodo->Estado ? __('keys.active') : __('keys.inactive');
                })
                ->make(true);
        }
    }
}