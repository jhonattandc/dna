<?php

namespace App\Q10\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Q10\Models\Campus;
use App\Q10\Models\Asignatura;
use App\Http\Controllers\Controller;

class AsignaturasController extends Controller
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
            $asignaturas = $campus->asignaturas()->select([
                'Codigo', 
                'Abreviacion',
                'Nombre',
                'Estado',
            ]);

            return Datatables::eloquent($asignaturas)
                ->editColumn('Estado', function ($asignatura) {
                    return $asignatura->Estado ? __('keys.active') : __('keys.inactive');
                })
                ->make(true);
        }
    }
}