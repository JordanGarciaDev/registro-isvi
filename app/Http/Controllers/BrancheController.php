<?php

namespace App\Http\Controllers;

use App\Models\Branche;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Zone;

class BrancheController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches = Branche::with('user')->orderBy('id', 'DESC')->get();

        $zones = Zone::where('status', 1)
            ->orderBy('id', 'ASC')
            ->pluck('name', 'id');

        return view('branches.index', compact('branches', 'zones'));
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
        $rules = [
            'name' => 'required',
        ];
        $messages = [
            'name.required' => 'required',
        ];
        $request->validate($rules, $messages);

        $exists = Branche::where('name', $request->name)
            ->exists();

        if ($exists) {
            return redirect()->route('regiones.index')->with('error', 'Oops, Esta región/sucursal ya se encuentra registrada, intenta con otra.');
        }

        $branche = new Branche();
        $branche->name = $request->name;
        $branche->zones = json_encode($request->zones);
        $branche->user_id = Auth::user()->id;
        $branche->status = 1; //1:activo, 0:inactivo
        $branche->save();

        return redirect()->route('regiones.index')->with('success', 'La Región/sucursal ha sido editada exitosamente.');
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
        try {
            $branche = Branche::findOrFail($id);

            $zones = Zone::where('status', 1)
                ->orderBy('id', 'ASC')
                ->pluck('name', 'id');

            return view('branches.edit', compact('branche', 'zones'));
        } catch (\Exception $e) {
            return redirect()->route('regiones.index')->with('error', "Oops, no se puede realizar esa acción, Por favor, intente mas tarde.");
        }
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
        $rules = [
            'name' => 'required',
        ];
        $messages = [
            'name.required' => 'El nombre es obligatorio.',
        ];
        $request->validate($rules, $messages);

        $branche = Branche::find($id);

        if (!$branche) {
            return redirect()->route('regiones.edit')->with('error', 'La región/sucursal no fue encontrada, Por favor, vuelva a intentar.');
        }

        $exists = Branche::where('name', $request->name)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->route('regiones.edit')->with('error', 'Oops, Esta región/sucursal ya se encuentra registrada, intenta con otra.');
        }

        $branche->name = $request->name;
        $branche->zones = json_encode($request->zones);
        $branche->user_id = Auth::user()->id;
        $branche->status = 1; //1:activo, 0:inactivo
        $branche->save();

        return redirect()->route('regiones.index')->with('success', 'La Región/sucursal ha sido creada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $branche = Branche::findOrFail($id);

            $branche->update([
                'status' => $branche->status === 0 ? 1 : 0
            ]);

            $statusMessage = $branche->status ? "Activada" : "Inactivada";

            return redirect()->route('regiones.index')->with('success', "La región/sucursal ha sido {$statusMessage} correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('regiones.index')->with('error', "Error al cambiar el estado de la región/sucursal.");
        }
    }
}
