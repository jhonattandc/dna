<?php

namespace App\Q10\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Q10\Models\Campus;
use App\Q10\Models\Programa;
use App\Http\Controllers\Controller;

class ProgramasController extends Controller
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
            $programas = $campus->programas()->select([
                'Codigo', 
                'Nombre', 
                'Aplica_preinscripcion', 
                'Tipo_evaluacion', 
                'Estado',
            ]);

            return Datatables::eloquent($programas)
                ->editColumn('Aplica_preinscripcion', function ($programa) {
                    return $programa->Aplica_preinscripcion ? __('keys.yes') : __('keys.no');
                })
                ->editColumn('Estado', function ($programa) {
                    return $programa->Estado ? __('keys.active') : __('keys.inactive');
                })
                ->make(true);
        }
    }
}