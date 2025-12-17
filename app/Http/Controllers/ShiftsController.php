<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Models\PersonalShift;
use App\Models\Worker;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\Zone;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Faker\Provider\ar_EG\Person;
use Illuminate\Support\Facades\DB;

class ShiftsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = Schedule::where('status', 1)->orderBy('id', 'DESC')->get();
        $zones = session('zones') ?? Zone::with('schedule')->where('status', 1)->orderBy('id', 'DESC')->get();
        $zoneFilter = session('zoneFilter');
        $personals = session('personals', collect());
        $dates = session('dates', collect());
        $pattern = session('pattern', collect());
        $zoneInfo = session('zoneInfo', collect());
        $totalProjected = session('totales', collect());
        $horasExtras = session('horasExtras', collect());
        $valoresExtrasCOP = session('valoresExtrasCOP', collect());
        $programacionPorTrabajador = session('programacionPorTrabajador', collect());

        if ($zoneFilter) {
            $zone = Zone::with('schedule')->find($zoneFilter);

            if (!$zone) {
                return redirect()->back()->with('error', 'La zona seleccionada no existe.');
            }

            if (!$zone->schedule) {
                return redirect()->back()->with('error', 'No se encontró información de programación para la zona seleccionada. Por favor, asígnala en el módulo de ubicaciones / Zonas.');
            }
        }

        if ($personals->isNotEmpty()) {
            session()->flash('programacion_success', true);
        }

        return view('shifts.index', compact('schedules', 'zones', 'zoneFilter', 'personals', 'dates', 'pattern', 'zoneInfo', 'totalProjected', 'horasExtras', 'valoresExtrasCOP', 'programacionPorTrabajador'));
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
            'schedule_id' => 'required',
            'day_since' => 'required',
            'day_until' => 'required',
            'val1' => 'required',
            'val2' => 'required',
            'n_workers' => 'required',
            'salario_base' => 'required',
            'programacion_json' => 'required',
        ];
        $messages = [
            'schedule_id.required' => 'El id de la programación es requerido.',
            'day_since.required' => 'La fecha inicial de la programación es requerida.',
            'day_until.required' => 'La fecha final de la programación es requerida.',
            'val1.required' => 'El valor 1 es requerido.',
            'val2.required' => 'El valor 2 es requerido.',
            'n_workers.required' => 'El número de trabajadores es requerido.',
            'salario_base.required' => 'El salario base es requerido.',
            'programacion_json.required' => 'El personal es requerido.'
        ];

        $request->validate($rules, $messages);

        try {
            DB::beginTransaction();

            $salary = str_replace('.', '', $request->salario_base);

            $shift = new Shift();
            $shift->zone_name = $request->zone_name;
            $shift->zone_id = $request->zone_id;
            $shift->schedule_id = $request->schedule_id;
            $shift->day_since = $request->day_since;
            $shift->day_until = $request->day_until;
            $shift->val1 = $request->val1;
            $shift->val2 = $request->val2;
            $shift->n_workers = $request->n_workers;
            $shift->salario_base = $salary;
            $shift->save();

            $turnsForWorker = json_decode($request->programacion_json, true);

            foreach ($turnsForWorker as $personalId => $turns) {
                foreach ($turns as $turn) {
                    $personalShift = new PersonalShift();
                    $personalShift->shift_id = $shift->id;
                    $personalShift->personal_id = $personalId;
                    $personalShift->schedule_id = $shift->schedule_id;
                    $personalShift->day_since = $request->day_since;
                    $personalShift->day_until = $request->day_until;
                    $personalShift->date_programation = $turn['start'];
                    $personalShift->turn = $turn['title'];
                    $personalShift->save();
                }
            }

            DB::commit();

            return redirect()->route('turnos.getShifts')->with('success', 'La programación de turnos ha sido registrada con exito.');
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return redirect()->back()->with('error', 'Oops, Algo salió mal en el registro de la programación, Por favor, intente mas tarde.');
        }
    }

    public function viewDetails($id)
    {
        $personal = Worker::find($id);

        if (!$personal) {
            return redirect()->back()->with('error', 'Oops, el personal seleccionado no ha sido encontrado. Por favor, intente más tarde.');
        }

        $firstShift = PersonalShift::where('personal_id', $id)->orderBy('date_programation')->first();

        if (!$firstShift) {
            return redirect()->back()->with('error', 'No se encontraron turnos asignados a este trabajador.');
        }

        $scheduleId = $firstShift->schedule_id;

        $turns = PersonalShift::where('personal_id', $id)
            ->where('schedule_id', $scheduleId)
            ->orderBy('date_programation')
            ->get()
            ->groupBy('date_programation');

        $fechaInicial = $firstShift->day_since;
        $fechaFinal = $firstShift->day_until;

        $observations = Observation::whereHas('personalShift', function ($query) use ($id, $scheduleId) {
            $query->where('personal_id', $id)
                ->where('schedule_id', $scheduleId);
        })->get();

        $workers = Worker::where('status', 1)
            ->whereRaw('LOWER(TRIM(bonding)) = ?', ['disponible'])
            ->get();

        return view('shifts.viewDetails', compact('turns', 'personal', 'scheduleId', 'fechaInicial', 'fechaFinal', 'observations', 'workers'));
    }


    public function getShifts()
    {
        $shifts = Shift::with('personalShifts.worker', 'zone.schedule')
            ->orderBy('id', 'DESC')
            ->get();

        $programacionPorTrabajador = [];

        $ultimoShift = null;

        foreach ($shifts as $shift) {
            $shift->salary = number_format($shift->salario_base, 0, ',', '.');

            $startDate = Carbon::parse($shift->day_since);
            $endDate = Carbon::parse($shift->day_until);
            $dates = collect();
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                $dates->push($date->copy());
            }

            $shift->dates = $dates;
            $shift->personals = $shift->personalShifts
                ->map(fn($ps) => $ps->worker)
                ->unique('id')
                ->values();
            $shift->zoneInfo = $shift->zone;

            $ultimoShift = $shift;

            foreach ($shift->personalShifts as $ps) {
                $personalId = $ps->personal_id;

                $programacionPorTrabajador[$personalId][] = [
                    'fecha' => $ps->date_programation,
                    'turno' => match ($ps->turn) {
                        'D' => 'Día',
                        'N' => 'Noche',
                        'X' => 'Descanso',
                        default => $ps->turn,
                    },
                ];
            }
        }

        return view('shifts.viewShifts', [
            'shifts' => $shifts,
            'dates' => $ultimoShift?->dates ?? collect(),
            'personals' => $ultimoShift?->personals ?? collect(),
            'zoneInfo' => $ultimoShift?->zoneInfo ?? null,
            'programacionPorTrabajador' => $programacionPorTrabajador,
        ]);
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
        //
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

    public function generateResults(Request $request)
    {
        $zones = Zone::where('status', 1)->orderBy('id', 'DESC')->get();

        $zoneFilter = $request->input('zone');
        $zoneInfo = Zone::where('id', $zoneFilter)->first();
        $workerIds = json_decode($zoneInfo->id_workers, true) ?? [];
        $personals = Worker::whereIn('id', $workerIds)->orderBy('id', 'DESC')->get();
        $startDate = Carbon::parse($zoneInfo->schedule->day_since)->locale('es');
        $endDate = Carbon::parse($zoneInfo->schedule->day_until)->locale('es');

        $numDays = $startDate->diffInDays($endDate) + 1;
        $pattern = ['Día', 'Día', 'Noche', 'Noche', 'Descanso', 'Descanso'];

        $dates = [];
        for ($i = 0; $i < $numDays; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }

        // Festivos
        $holidays = $this->getColombianHolidays($startDate->year) ?? [];
        if ($startDate->year !== $endDate->year) {
            $holidays = array_merge($holidays, $this->getColombianHolidays($endDate->year));
        }

        if ($personals->isEmpty()) {
            return redirect()->back()->with('error', 'Oops, No hay ningún personal asociado a la zona seleccionada, asígnalos en el módulo de personal operativo.');
        }

        $horarios = [
            $zoneInfo->schedule->day_hour_since,
            $zoneInfo->schedule->day_hour_until,
            $zoneInfo->schedule->night_hour_since,
            $zoneInfo->schedule->night_hour_until,
            $zoneInfo->schedule->break_hour_since,
            $zoneInfo->schedule->break_hour_until
        ];

        if (collect($horarios)->every(fn($hora) => is_null($hora))) {
            return redirect()->back()->with('error', 'Oops, no hay un horario asignado para esa programación. Por favor, asigna el horario en el módulo de programaciones.');
        }

        $extrasGlobales = ['diurnas' => 0, 'nocturnas' => 0];

        $programacionPorTrabajador = [];

        foreach ($personals as $index => $personal) {
            $tipo = "{$zoneInfo->schedule->val1}x{$zoneInfo->schedule->val2}";
            $personaTurnos = [];
            $totales = [
                'diurnas' => 0,
                'nocturnas' => 0,
                'festivo_diurno' => 0,
                'festivo_nocturno' => 0,
                'trabajadas' => 0,
            ];

            if ($tipo === '2x2') {
                $turnos = ['Día', 'Noche', 'Descanso'];
                $start = $index % 3;
                for ($i = 0; count($personaTurnos) < count($dates); $i++) {
                    $actual = $turnos[($start + $i) % 3];
                    $cantidad = $actual === 'Descanso' ? $zoneInfo->schedule->val2 : $zoneInfo->schedule->val1;
                    $personaTurnos = array_merge($personaTurnos, array_fill(0, $cantidad, $actual));
                }
                $personaTurnos = array_slice($personaTurnos, 0, count($dates));
            } elseif ($tipo === '5x2') {
                $turno = ($index % 2 === 0) ? 'Día' : 'Noche';
                $personaTurnos = [];
                $count = 0;
                foreach ($dates as $i => $d) {
                    $personaTurnos[] = $count < 5 ? $turno : 'Descanso';
                    $count++;
                    if ($count === 7) {
                        $count = 0;
                        $turno = $turno === 'Día' ? 'Noche' : 'Día';
                    }
                }
            }

            $programacionPorTrabajador[$personal->id] = [];

            foreach ($dates as $i => $fecha) {
                $programacionPorTrabajador[$personal->id][] = [
                    'fecha' => $fecha->toDateString(),
                    'turno' => $personaTurnos[$i] ?? 'Descanso',
                ];
            }

            $semanaTurnos = [];
            $totales['recargo_nocturno'] = 0;
            $contadorDiasBloque = 0;

            for ($i = 0; $i < count($dates); $i++) {
                $date = $dates[$i];
                $turno = $personaTurnos[$i] ?? 'Descanso';

                $contadorDiasBloque++; // siempre avanza

                if ($turno !== 'Descanso') {
                    if ($turno === 'Día') {
                        $inicio = $zoneInfo->schedule->day_hour_since;
                        $fin = $zoneInfo->schedule->day_hour_until;
                    } else {
                        $inicio = $zoneInfo->schedule->night_hour_since;
                        $fin = $zoneInfo->schedule->night_hour_until;
                    }

                    $inicioCompleto = Carbon::parse($date->format('Y-m-d') . ' ' . $inicio);
                    $finCompleto = Carbon::parse($date->format('Y-m-d') . ' ' . $fin);

                    if ($finCompleto->lessThan($inicioCompleto)) {
                        $finCompleto->addDay();
                    }

                    $rangoHoras = $this->calcularHorasPorFranja($inicioCompleto, $finCompleto);

                    $horasTotalesPrevias = array_reduce($semanaTurnos, function ($carry, $item) {
                        return $carry + $item['horas'];
                    }, 0);
                    $horasDisponibles = max(0, 46 - $horasTotalesPrevias);
                    $horasTurnoActual = $rangoHoras['diurnas'] + $rangoHoras['nocturnas'];

                    $horasAContar = min($horasTurnoActual, $horasDisponibles);

                    if ($horasAContar > 0) {
                        $recargoTurno = $this->calcularRecargoNocturno($inicioCompleto, $inicioCompleto->copy()->addMinutes($horasAContar * 60));
                        $totales['recargo_nocturno'] += $recargoTurno;
                    }

                    $semanaTurnos[] = [
                        'inicio' => $inicioCompleto,
                        'fin' => $finCompleto,
                        'horas' => $horasTurnoActual,
                    ];

                    $totales['diurnas'] += $rangoHoras['diurnas'];
                    $totales['nocturnas'] += $rangoHoras['nocturnas'];
                    $totales['trabajadas'] += $horasTurnoActual;
                }

                $esUltimoDia = ($i + 1 === count($dates));
                if ($contadorDiasBloque === 7 || $esUltimoDia) {
                    $totalSemana = array_sum(array_column($semanaTurnos, 'horas'));

                    if ($totalSemana > 46) {
                        $exceso = $totalSemana - 46;
                        $excesoRestante = $exceso;

                        for ($j = count($semanaTurnos) - 1; $j >= 0 && $excesoRestante > 0; $j--) {
                            $bloque = $semanaTurnos[$j];
                            $inicio = $bloque['inicio'];
                            $fin = $bloque['fin'];
                            $duracionHoras = $fin->floatDiffInRealHours($inicio);
                            $horasTomadas = min($duracionHoras, $excesoRestante);

                            $subFin = $fin->copy()->subHours($duracionHoras - $horasTomadas);
                            $rangoExceso = $this->calcularHorasPorFranja($subFin, $fin);

                            $extrasGlobales['diurnas'] += $rangoExceso['diurnas'];
                            $extrasGlobales['nocturnas'] += $rangoExceso['nocturnas'];

                            if ($inicio->isSunday() || $this->esFestivo($inicio)) {
                                $extrasGlobales['festivo_dominical_diurna'] = ($extrasGlobales['festivo_diurna'] ?? 0) + $rangoExceso['diurnas'];
                                $extrasGlobales['festivo_dominical_nocturna'] = ($extrasGlobales['festivo_nocturna'] ?? 0) + $rangoExceso['nocturnas'];
                            }

                            $excesoRestante -= $horasTomadas;
                        }
                    }

                    $semanaTurnos = [];
                    $contadorDiasBloque = 0;
                }
            }
        }

        $salarioLimpio = (int) str_replace('.', '', $zoneInfo->salary);
        $valorHoraOrdinaria = $salarioLimpio / 230;

        $recargoNocturno = round($valorHoraOrdinaria * $totales['recargo_nocturno'] * 0.35);
        $extraDiurna = round($valorHoraOrdinaria * $extrasGlobales['diurnas'] * 1.25);
        $extraNocturna = round($valorHoraOrdinaria * $extrasGlobales['nocturnas'] * 1.75);

        $horasDominicalDiurna = data_get($extrasGlobales, 'festivo_dominical_diurna', 0);
        $festivoDominicalDiurna = round($valorHoraOrdinaria * $horasDominicalDiurna * 2.00);

        $horasDominicalDiurna = data_get($extrasGlobales, 'festivo_dominical_diurna', 0);
        $festivoDominicalDiurna = round($valorHoraOrdinaria * $horasDominicalDiurna * 2.00);

        $horasDominicalNocturna = data_get($extrasGlobales, 'festivo_dominical_nocturna', 0);
        $festivoDominicalNocturna = round($valorHoraOrdinaria * $horasDominicalNocturna * 2.50);


        $programacionTotalCOP = $recargoNocturno + $extraDiurna + $extraNocturna + $festivoDominicalDiurna + $festivoDominicalNocturna;

        $valoresExtrasCOP = [
            'recargo_nocturno' => '$ ' . number_format(round($valorHoraOrdinaria * $totales['recargo_nocturno'] * 0.35), 0, ',', '.') . ' COP',
            'extra_diurna' => '$ ' . number_format(round($valorHoraOrdinaria * data_get($extrasGlobales, 'diurnas', 0) * 1.25), 0, ',', '.') . ' COP',
            'extra_nocturna' => '$ ' . number_format(round($valorHoraOrdinaria * data_get($extrasGlobales, 'nocturnas', 0) * 1.75), 0, ',', '.') . ' COP',
            'festivo_dominical_diurna' => '$ ' . number_format($festivoDominicalDiurna, 0, ',', '.') . ' COP',
            'festivo_dominical_nocturna' => '$ ' . number_format($festivoDominicalNocturna, 0, ',', '.') . ' COP',
            'programacion_total' => '$ ' . number_format(round($programacionTotalCOP), 0, ',', '.') . ' COP',
        ];


        return redirect()
            ->route('turnos.index')
            ->with([
                'zones' => $zones,
                'zoneFilter' => $zoneFilter,
                'zoneInfo' => $zoneInfo,
                'personals' => $personals,
                'dates' => $dates,
                'pattern' => $pattern,
                'totales' => $totales,
                'horasExtras' => $extrasGlobales,
                'valoresExtrasCOP' => $valoresExtrasCOP,
                'programacionPorTrabajador' => $programacionPorTrabajador,
            ]);
    }

    public function calcularPorTrabajador(Request $request)
    {
        $workerId = $request->input('worker_id');
        $zoneId = $request->input('zone_id');

        $zone = Zone::with('schedule')->findOrFail($zoneId);
        $worker = Worker::findOrFail($workerId);
        $schedule = $zone->schedule;

        $startDate = Carbon::parse($schedule->day_since)->locale('es');
        $endDate = Carbon::parse($schedule->day_until)->locale('es');
        $numDays = $startDate->diffInDays($endDate) + 1;

        $dates = [];
        for ($i = 0; $i < $numDays; $i++) {
            $dates[] = $startDate->copy()->addDays($i);
        }

        $holidays = $this->getColombianHolidays($startDate->year);
        if ($startDate->year !== $endDate->year) {
            $holidays = array_merge($holidays, $this->getColombianHolidays($endDate->year));
        }

        $turnos = [];
        $tipo = "{$schedule->val1}x{$schedule->val2}";

        // Asignación de turnos individual
        if ($tipo === '2x2') {
            $patron = ['Día', 'Noche', 'Descanso'];
            $start = $worker->id % 3;
            for ($i = 0; count($turnos) < count($dates); $i++) {
                $actual = $patron[($start + $i) % 3];
                $cantidad = $actual === 'Descanso' ? $schedule->val2 : $schedule->val1;
                $turnos = array_merge($turnos, array_fill(0, $cantidad, $actual));
            }
            $turnos = array_slice($turnos, 0, count($dates));
        } elseif ($tipo === '5x2') {
            $turno = ($worker->id % 2 === 0) ? 'Día' : 'Noche';
            $turnos = [];
            $count = 0;
            foreach ($dates as $i => $d) {
                $turnos[] = $count < 5 ? $turno : 'Descanso';
                $count++;
                if ($count === 7) {
                    $count = 0;
                    $turno = $turno === 'Día' ? 'Noche' : 'Día';
                }
            }
        }

        $numeroDiasTrabajados = collect($turnos)->filter(fn($t) => $t !== 'Descanso')->count();

        $bloque = [];
        $trabajados = [];
        $totales = [
            'diurnas' => 0,
            'nocturnas' => 0,
            'recargo_nocturno' => 0,
            'extra_diurnas' => 0,
            'extra_nocturnas' => 0,
            'festivo_diurnas' => 0,
            'festivo_nocturnas' => 0,
            'total_trabajadas' => 0,
        ];

        for ($i = 0; $i < count($dates); $i++) {
            $fecha = $dates[$i];
            $tipoTurno = $turnos[$i] ?? 'Descanso';

            if ($tipoTurno === 'Descanso') continue;

            $inicio = $tipoTurno === 'Día' ? $schedule->day_hour_since : $schedule->night_hour_since;
            $fin = $tipoTurno === 'Día' ? $schedule->day_hour_until : $schedule->night_hour_until;

            $inicioCompleto = Carbon::parse($fecha->format('Y-m-d') . ' ' . $inicio);
            $finCompleto = Carbon::parse($fecha->format('Y-m-d') . ' ' . $fin);
            if ($finCompleto->lessThan($inicioCompleto)) {
                $finCompleto->addDay();
            }

            $horas = $this->calcularHorasPorFranja($inicioCompleto, $finCompleto);
            $totalTurno = $horas['diurnas'] + $horas['nocturnas'];
            $recargo = $this->calcularRecargoNocturno($inicioCompleto, $finCompleto);

            $bloque[] = [
                'fecha' => $fecha->format('Y-m-d'),
                'inicio' => $inicioCompleto,
                'fin' => $finCompleto,
                'diurnas' => $horas['diurnas'],
                'nocturnas' => $horas['nocturnas'],
                'total' => $totalTurno,
                'recargo_nocturno' => $recargo,
                'es_festivo' => $fecha->isSunday() || in_array($fecha->format('Y-m-d'), $holidays),
            ];

            if (count($bloque) === 7 || $i + 1 === count($dates)) {
                $acumuladas = 0;
                foreach ($bloque as $j => $turnoDia) {
                    $ordinariasRestantes = max(0, 46 - $acumuladas);
                    $ordinariasDiurnas = min($turnoDia['diurnas'], $ordinariasRestantes);
                    $ordinariasRestantes -= $ordinariasDiurnas;

                    $ordinariasNocturnas = min($turnoDia['nocturnas'], $ordinariasRestantes);
                    $ordinariasRestantes -= $ordinariasNocturnas;

                    $extraD = $turnoDia['diurnas'] - $ordinariasDiurnas;
                    $extraN = $turnoDia['nocturnas'] - $ordinariasNocturnas;

                    $acumuladas += $ordinariasDiurnas + $ordinariasNocturnas;

                    $totales['diurnas'] += $ordinariasDiurnas;
                    $totales['nocturnas'] += $ordinariasNocturnas;
                    $totales['extra_diurnas'] += $extraD;
                    $totales['extra_nocturnas'] += $extraN;

                    if ($turnoDia['es_festivo']) {
                        $totales['festivo_diurnas'] += $turnoDia['diurnas'];
                        $totales['festivo_nocturnas'] += $turnoDia['nocturnas'];
                    }

                    $totales['recargo_nocturno'] += $turnoDia['recargo_nocturno'];
                    $totales['total_trabajadas'] += $turnoDia['total'];
                }

                $trabajados = array_merge($trabajados, $bloque);
                $bloque = [];
            }
        }

        $valorHora = intval(str_replace(',', '', $zone->salary)) / 230;

        $precios = [
            'diurnas' => round($totales['diurnas'] * $valorHora, 2),
            'nocturnas' => round($totales['nocturnas'] * $valorHora * 1.35, 2),
            'extra_diurnas' => round($totales['extra_diurnas'] * $valorHora * 1.25, 2),
            'extra_nocturnas' => round($totales['extra_nocturnas'] * $valorHora * 1.75, 2),
            'festivo_diurnas' => round($totales['festivo_diurnas'] * $valorHora * 1.75, 2),
            'festivo_nocturnas' => round($totales['festivo_nocturnas'] * $valorHora * 2.10, 2),
            'recargo_nocturno' => round(($totales['recargo_nocturno'] - $totales['extra_nocturnas'] - $totales['festivo_nocturnas']) * $valorHora * 0.35, 2),
        ];

        $precios['total'] = array_sum($precios);

        return response()->json([
            'trabajador' => [
                'id' => $worker->id,
                'nombre' => $worker->name,
            ],
            'resumen' => $totales,
            'precios' => $precios,
            'detalle' => $trabajados,
            'dias_trabajados' => $numeroDiasTrabajados
        ]);
    }

    private function getColombianHolidays($year)
    {
        $holidays = [];

        // Festivos fijos
        $holidays[] = "$year-01-01"; // Año Nuevo
        $holidays[] = "$year-05-01"; // Día del Trabajo
        $holidays[] = "$year-07-20"; // Independencia
        $holidays[] = "$year-08-07"; // Batalla de Boyacá
        $holidays[] = "$year-12-08"; // Inmaculada Concepción
        $holidays[] = "$year-12-25"; // Navidad

        // Ley Emiliani (festivos trasladables al lunes)
        $emiliani = function ($date) {
            $day = date('w', strtotime($date));
            return date('Y-m-d', strtotime("$date +" . ((8 - $day) % 7) . " days"));
        };

        $holidays[] = $emiliani("$year-01-06"); // Reyes Magos
        $holidays[] = $emiliani("$year-03-19"); // San José
        $holidays[] = $emiliani("$year-06-29"); // San Pedro y San Pablo
        $holidays[] = $emiliani("$year-08-15"); // Asunción
        $holidays[] = $emiliani("$year-10-12"); // Día de la Raza
        $holidays[] = $emiliani("$year-11-01"); // Todos los Santos
        $holidays[] = $emiliani("$year-11-11"); // Independencia de Cartagena

        // Festivos religiosos que dependen de la Semana Santa
        $easter = (new \DateTime())->setDate($year, 3, 21)->modify('+' . easter_days($year) . ' days');
        $easterDate = $easter->format('Y-m-d');

        $addDays = fn($days) => date('Y-m-d', strtotime("$easterDate +$days days"));
        $holidays[] = $addDays(-3); // Jueves Santo
        $holidays[] = $addDays(-2); // Viernes Santo
        $holidays[] = $emiliani($addDays(43)); // Ascensión del Señor
        $holidays[] = $emiliani($addDays(64)); // Corpus Christi
        $holidays[] = $emiliani($addDays(71)); // Sagrado Corazón

        return $holidays;
    }

    private function calcularHorasPorFranja($inicioTurno, $finTurno)
    {
        $inicioTurno = Carbon::parse($inicioTurno);
        $finTurno = Carbon::parse($finTurno);

        if ($finTurno->lessThan($inicioTurno)) {
            $finTurno->addDay(); // Para manejar turnos que cruzan medianoche
        }

        $franjaDiurnaInicio = $inicioTurno->copy()->setTime(6, 0);
        $franjaDiurnaFin = $inicioTurno->copy()->setTime(21, 0);

        if ($franjaDiurnaFin->lessThan($franjaDiurnaInicio)) {
            $franjaDiurnaFin->addDay();
        }

        // Rango legal diurno
        $inicioDiurno = $inicioTurno->copy()->max($franjaDiurnaInicio);
        $finDiurno = $finTurno->copy()->min($franjaDiurnaFin);
        $horasDiurnas = max(0, $finDiurno->floatDiffInHours($inicioDiurno));

        // Resto se considera nocturno
        $horasTotales = $finTurno->floatDiffInHours($inicioTurno);
        $horasNocturnas = $horasTotales - $horasDiurnas;

        return [
            'diurnas' => round($horasDiurnas, 2),
            'nocturnas' => round($horasNocturnas, 2),
        ];
    }

    private function calcularRecargoNocturno(Carbon $inicio, Carbon $fin): float
    {
        $recargoInicio = $inicio->copy()->setTime(21, 0);
        $recargoFin = $inicio->copy()->addDay()->setTime(6, 0);

        if ($inicio->hour < 6) {
            $recargoInicio->subDay();
            $recargoFin->subDay();
        }

        $inicioReal = $inicio->greaterThan($recargoInicio) ? $inicio : $recargoInicio;
        $finReal = $fin->lessThan($recargoFin) ? $fin : $recargoFin;

        if ($finReal->lessThanOrEqualTo($inicioReal)) {
            return 0;
        }

        return $inicioReal->diffInMinutes($finReal) / 60;
    }

    private function esFestivo(Carbon $fecha): bool
    {
        $festivos = $this->getColombianHolidays($fecha->year);
        return in_array($fecha->format('Y-m-d'), $festivos);
    }
}
