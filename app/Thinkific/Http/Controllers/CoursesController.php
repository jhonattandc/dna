<?php

namespace App\Thinkific\Http\Controllers;

use DataTables;
use App\Thinkific\Models\Course;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CoursesController extends Controller
{

    /**
     * Set the course as default.
     * 
     * @param Request $request
     */
    public function set(Request $request)
    {
        $course = Course::findOrFail($request->course);

        $course->default = !$course->default;
        $course->save();

        return redirect()->route('thinkific/courses');
    }

    /**
     * Process the ajax request from the datatable plugin.
     *
     * 
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $courses = Course::select(['id', 'thinkific_id','name','default',]);

            return Datatables::eloquent($courses)
                ->editColumn('default', function ($course) {
                    return $course->default ? __('keys.yes') : __('keys.no');
                })
                ->addColumn('action', function ($course) {
                    // TODO: Cambiar para que se genere un request POST desde javascript y no se haga un form
                    // asi se evita el refresco de la pagina.
                    $text = $course->default ? __('thinkific.table.unset_as_default') : __('thinkific.table.set_as_default');

                    $button = '<form action="' . route('thinkific.courses.set', $course->id) . '" method="POST">';
                    $button .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
                    $button .= '<button type="submit" class="btn btn-secondary btn-sm">' . $text . '</button>';
                    $button .= '</form>';

                    return $button;
                })
                ->make(true);
        }
    }
}