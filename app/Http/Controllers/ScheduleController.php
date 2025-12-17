<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = Schedule::orderBy('status', 'DESC')->get();

        $schedules->transform(function ($item) {
            $item->schedule_type = "{$item->val1}x{$item->val2}";
            return $item;
        });

        return view('schedules.index', compact('schedules'));
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
        try {
            $rules = $this->getValidationRules();
            $messages = $this->getValidationMessages();
            $request->validate($rules, $messages);

            // sacamos los valores de la programación para almacenarlos
            if ($request->schedule_type) {
                [$val1, $val2] = explode('x', strtolower($request->schedule_type));
                $request->merge([
                    'val1' => intval($val1),
                    'val2' => intval($val2),
                ]);
            }

            $schedule = new Schedule();
            $schedule->name = $request->name;
            $schedule->schedule_format = $request->schedule_format;
            $schedule->val1 = $request->val1;
            $schedule->val2 = $request->val2;
            $schedule->day_since = $request->day_since;
            $schedule->day_until = $request->day_until;
            $schedule->day_hour_since = $request->day_hour_since;
            $schedule->day_hour_until = $request->day_hour_until;
            $schedule->night_hour_since = $request->night_hour_since;
            $schedule->night_hour_until = $request->night_hour_until;
            $schedule->break_hour_since = $request->break_hour_since;
            $schedule->break_hour_until = $request->break_hour_until;
            $schedule->save();

            return redirect()->route('programaciones.index')->with('success', 'La programación ha sido creada con exito');
        } catch (\Exception $e) {
            return redirect()->route('programaciones.index')->with('error', 'Oops, algo salió mal. Por favor, intenta mas tarde. ' . $e);
        }
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
        try {
            $rules = $this->getValidationRules();
            $messages = $this->getValidationMessages();
            $request->validate($rules, $messages);

            // sacamos los valores de la programación para almacenarlos
            if ($request->schedule_type) {
                [$val1, $val2] = explode('x', strtolower($request->schedule_type));
                $request->merge([
                    'val1' => intval($val1),
                    'val2' => intval($val2),
                ]);
            }

            $schedule = Schedule::find($id);
            $schedule->name = $request->name;
            $schedule->schedule_format = $request->schedule_format;
            $schedule->val1 = $request->val1;
            $schedule->val2 = $request->val2;
            $schedule->day_since = $request->day_since;
            $schedule->day_until = $request->day_until;
            $schedule->day_hour_since = $request->day_hour_since;
            $schedule->day_hour_until = $request->day_hour_until;
            $schedule->night_hour_since = $request->night_hour_since;
            $schedule->night_hour_until = $request->night_hour_until;
            $schedule->break_hour_since = $request->break_hour_since;
            $schedule->break_hour_until = $request->break_hour_until;
            $schedule->save();

            return redirect()->route('programaciones.index')->with('success', 'La programación seleccionada ha sido editada correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('programaciones.index')->with('error', 'Oops, algo salió mal, por favor, intente mas tarde.');
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
        try {
            $schedule = Schedule::findOrFail($id);
            if ($schedule->status === 1) {
                $schedule->status = 0;
            } else {
                $schedule->status = 1;
            }
            $schedule->save();

            return redirect()->route('programaciones.index')->with('success', 'La programación seleccionada ha sido inactivada.');
        } catch (\Exception $e) {
            return redirect()->route('programaciones.index')->with('success', 'Oops, algo salió mal, Por favor, Intenta mas tarde.');
        }
    }

    private function getValidationRules()
    {
        return [
            'name' => 'required',
            'day_since' => 'required',
            'day_until' => 'required',
        ];
    }

    private function getValidationMessages()
    {
        return [
            'name.required' => 'El campo horario es obligatorio.',
            'day_since.required' => 'El campo fecha desde electrónico es obligatorio.',
            'day_until.required' => 'El campo Correo electrónico es obligatorio.',
        ];
    }
}
