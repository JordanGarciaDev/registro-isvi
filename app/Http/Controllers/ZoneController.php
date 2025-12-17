<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Models\Zone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ZonasExport;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $zones = Zone::orderBy('id', 'DESC')->get();
        $schedules = Schedule::where('status', 1)->get();
        $personals = Worker::where('status', 1)->orderBy('id', 'DESC')->get();

        foreach ($personals as $personal) {
            $personal->custom_name = $personal->document . " - " . $personal->name . " " . $personal->lastname;
        }

        return view('zones.index', compact('zones', 'schedules', 'personals'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $schedules = Schedule::where('status', 1)->orderBy('id', 'DESC')->get();
        $personals = Worker::where('status', 1)->orderBy('id', 'DESC')->get();

        foreach ($schedules as $schedule) {
            $schedule->custom_name = "{$schedule->name} - {$schedule->day_since} a {$schedule->day_until} ({$schedule->val1}x{$schedule->val2})";
        }

        foreach ($personals as $personal) {
            $personal->custom_name = "{$personal->document} - {$personal->name} {$personal->lastname}";
        }

        return view('zones.create', compact('schedules', 'personals'));
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
            'coordinates' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'salary' => 'required',
        ];
        $messages = [
            'name.required' => 'required',
            'coordinates.required' => 'Las coordenadas son obligatorias.',
            'address.required' => 'La dirección es obligatoria',
            'phone.required' => 'El número telefonico es obligatorio.',
            'email.required' => 'El correo es obligatorio.',
            'salary.required' => 'El salario es obligatorio.',
        ];
        $request->validate($rules, $messages);

        $photoPath = Storage::disk('public')->put('zones/photos', $request->file('photo'));

        $logoPath = null;
        if ($request->hasFile('logoInput')) {
            $logoPath = Storage::disk('public')->put('zones/logos', $request->file('logoInput'));
        }

        $isShifts = $request->has('is_shifts') ? 1 : 0;

        $zone = new Zone();
        $zone->id_customer = $request->id_customer;
        $zone->name = $request->name;
        $zone->coordinates = $request->coordinates;
        $zone->address = $request->address;
        $zone->phone = $request->phone;
        $zone->email = $request->email;
        $zone->salary = $request->salary;
        $zone->others_income = $request->others_income;
        $zone->contract_bonus = $request->contract_bonus;
        $zone->n_workers = $request->n_workers;
        $zone->id_workers = json_encode($request->personal_worker);
        $zone->photo = $photoPath;
        $zone->logo = $logoPath;
        $zone->region = 'Ninguna';
        $zone->descriptions = !empty($request->description) ? $request->description : 'N/A';
        $zone->user_id = Auth::user()->id;
        $zone->schedule_id = $request->schedule;
        $zone->save();

        return redirect()->route('zonas.index')->with('success', 'El personal operativo ha sido creado con exito.');
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
        $rules = [
            'zone_id' => 'required',
            'schedule_id' => 'required',
        ];
        $messages = [
            'zone_id.required' => 'La zona es obligatoria',
            'schedule_id.required' => 'El tipo de programación es obligatorio.',
        ];
        $request->validate($rules, $messages);

        $zone = Zone::find($id);

        if (!$zone) {
            return redirect()->back()->with('error', 'Oops, la zona seleccionada no ha sido encontrada, por favor intente mas tarde.');
        }

        $zone->schedule_id = $request->schedule_id;
        $zone->save();

        return redirect()->route('zonas.index')->with('success', 'El tipo de programación de la zona o puesto ha sido establecida con exito.');
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
            $zone = Zone::findOrFail($id);

            $zone->update([
                'status' => $zone->status === 0 ? 1 : 0
            ]);

            $statusMessage = $zone->status ? "Activada" : "Inactivada";

            return redirect()->route('zonas.index')->with('success', "La zona ha sido {$statusMessage} correctamente.");
        } catch (\Exception $e) {
            return redirect()->route('zonas.index')->with('error', "Error al cambiar el estado de la zona.");
        }
    }

    public function export()
    {
        return Excel::download(new ZonasExport, 'zonas_import.xlsx');
    }
}
