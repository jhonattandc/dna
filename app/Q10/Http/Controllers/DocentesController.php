<?php

namespace App\Q10\Http\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Q10\Models\Campus;
use App\Q10\Models\Docente;
use App\Http\Controllers\Controller;

class DocentesController extends Controller
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
            // Get all identification types for the campus
            $tipos_id = $campus->tipos_id;
            // Filter the administrativos by the identification types
            $docentes = Docente::select([
                'Codigo', 
                'Primer_nombre', 
                'Primer_apellido', 
                'Numero_identificacion', 
                'Email', 
                'Celular',
            ])->whereIn('tipo_id_id', $tipos_id->pluck('id'));

            return Datatables::eloquent($docentes)
                ->editColumn('Primer_nombre', function ($docentes) {
                    // frist lower case the first name then capitalize the first letter
                    return ucfirst(strtolower($docentes->Primer_nombre));
                })
                ->editColumn('Primer_apellido', function ($docentes) {
                    // frist lower case the first name then capitalize the first letter
                    return ucfirst(strtolower($docentes->Primer_apellido));
                })
                ->make(true);
        }
    }
}