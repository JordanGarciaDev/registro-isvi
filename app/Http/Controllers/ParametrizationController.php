<?php

namespace App\Http\Controllers;

use App\Models\Parametrization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ParametrizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parametrization = Parametrization::orderBy('status', 'DESC')->get();

        foreach ($parametrization as $item) {
            $user = User::where('id', $item->user_id)->first();
            $item->user_name = $user ? $user->name : 'Usuario desconocido';
        }

        return view('parametrization.index', compact('parametrization'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::id();

        Parametrization::where('user_id', $userId)->update(['status' => 0]);

        $parametrization = new Parametrization();
        $parametrization->n_horas_semanales = $request->n_horas_semanales;
        $parametrization->rango_hora_inicio_nocturno = Carbon::createFromFormat('H:i', $request->rango_hora_inicio_nocturno)->format('h:i A');
        $parametrization->rango_hora_final_nocturno = Carbon::createFromFormat('H:i', $request->rango_hora_final_nocturno)->format('h:i A');
        $parametrization->user_id = $userId;
        $parametrization->status = 1;
        $parametrization->save();

        return redirect()->back()->with('success', 'La parametrización ha sido establecida y se usará para los turnos de la programación.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userId = Auth::id();

        $parametrization = Parametrization::find($id);

        if ($parametrization) {
            $parametrization->n_horas_semanales = $request->n_horas_semanales;
            $parametrization->rango_hora_inicio_nocturno = Carbon::createFromFormat('H:i', $request->rango_hora_inicio_nocturno)->format('h:i A');
            $parametrization->rango_hora_final_nocturno = Carbon::createFromFormat('H:i', $request->rango_hora_final_nocturno)->format('h:i A');
            $parametrization->user_id = $userId;
            $parametrization->status = 1;
            $parametrization->save();

            return redirect()->back()->with('success', 'La parametrización ha sido establecida y se usará para los turnos de la programación.');
        } else {
            return redirect()->back()->with('error', 'Oops, ocurrio un error al editar el registro, por favor validar la información ingresada o contacte con un administrador.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
