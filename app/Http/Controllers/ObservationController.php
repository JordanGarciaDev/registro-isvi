<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Models\PersonalShift;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ObservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $observations = Observation::orderBy('id', 'DESC')->get();
        $workers = Worker::where('status', 1)->orderBy('id', 'DESC')->get();

        foreach ($observations as $observation) {
            $user = User::find($observation->personal_register);
            $observation->user_document = $user->document ?? 'N/D';
            $observation->user_register = $user->name ?? 'N/D';

            $personalShift = PersonalShift::find($observation->personal_shift_id);

            if ($personalShift && $personalShift->worker) {
                $observation->personal_document = $personalShift->worker->document;
                $observation->personal_names = $personalShift->worker->name . ' ' . $personalShift->worker->lastname;
            } else {
                $observation->personal_document = 'N/D';
                $observation->personal_names = 'N/D';
            }

            $workerReleva = Worker::find($observation->personal_releva);

            if ($workerReleva) {
                $observation->name_personal_releva = $workerReleva->name . ' ' . $workerReleva->lastname . ' (C.C ' . $workerReleva->document . ')';
            } else {
                $observation->name_personal_releva = 'N/D';
            }
        }

        return view('observations.index', compact('observations', 'workers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
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
        $personalShift = PersonalShift::where('personal_id', $request->personal)
            ->where('schedule_id', $request->schedule_id)
            ->whereDate('day_since', '<=', $request->date_since)
            ->whereDate('day_until', '>=', $request->date_until)
            ->first();

        if (!$personalShift) {
            return redirect()->back()->with('error', 'Oops, la novedad a registrar está fuera del rango de la programación, verifica el turno correspondiente del trabajador');
        }

        // Guardar archivo si se subió
        $path = null;
        if ($request->hasFile('soporte')) {
            $path = $request->file('soporte')->store('novedades', 'public');
        }

        // Crear novedad
        $observation = new Observation();
        $observation->personal_shift_id = $personalShift->id;
        $observation->date_until = $request->date_until;
        $observation->date_since = $request->date_since;
        $observation->observation = $request->novedad;
        $observation->path_document = $path;
        $observation->personal_releva = $request->personal_releva;
        $observation->prioridad = $request->prioridad;
        $observation->personal_register = auth()->user()->id ?? null;
        $observation->status = 1;
        $observation->save();

        return redirect()->back()->with('success', 'Novedad registrada exitosamente.');
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
        $observation = Observation::findOrFail($id);

        $status = $request->action;

        if ($status === "aprobar") {
            $observation->status = 2;
        } else {
            $observation->status = 0;
        }

        $observation->save();

        return redirect()->back()->with('success', 'El estado de la novedad ha sido actualizado.');
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

    public function generateReport(Request $request)
    {
        $data = $request->all();
        $fechaDesde = $data['fecha_desde'];
        $fechaHasta = $data['fecha_hasta'];
        $usuarios = $data['usuarios'];

        Carbon::setLocale('es');

        // Determinar si se deben incluir todos los usuarios
        if (in_array('all', $usuarios)) {
            if (count($usuarios) === 1) {
                $novedades = Observation::whereBetween('created_at', [$fechaDesde, $fechaHasta])
                    ->get();

                foreach ($novedades as $novedad) {
                    $novedad->usuario_registra = User::where('id', $novedad->personal_register)->value('name');
                    $novedad->usuario_releva = Worker::where('id', $novedad->personal_releva)->value('name');
                }
            } else {
                $usuarios = array_filter($usuarios, fn($u) => $u !== 'all');

                $novedades = Observation::whereIn('user_id', $usuarios)
                    ->whereBetween('created_at', [$fechaDesde, $fechaHasta])
                    ->get();

                foreach ($novedades as $novedad) {
                    $novedad->usuario_registra = User::where('id', $novedad->personal_register)->value('name');
                    $novedad->usuario_releva = Worker::where('id', $novedad->personal_releva)->value('name');
                }
            }
        } else {
            $novedades = Observation::whereIn('user_id', $usuarios)
                ->whereBetween('created_at', [$fechaDesde, $fechaHasta])
                ->get();

            foreach ($novedades as $novedad) {
                $novedad->usuario_registra = User::where('id', $novedad->personal_register)->value('name');
                $novedad->usuario_releva = Worker::where('id', $novedad->personal_releva)->value('name');
            }
        }

        // Preparar datos para la vista PDF
        $data = [
            'novedades' => $novedades,
            'fecha_actual' => Carbon::now()->format('d F Y'),
            'fecha_desde' => Carbon::parse($fechaDesde)->format('d F Y'),
            'fecha_hasta' => Carbon::parse($fechaHasta)->format('d F Y'),
        ];

        // Generar el PDF
        $pdf = Pdf::loadView('reports.novedades', $data);

        // Descargar o mostrar el PDF
        return $pdf->download('Reporte_Novedades_' . Carbon::now()->format('Ymd') . '.pdf');
    }
}
