@extends('adminlte::page')

@section('title', 'Turnos')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-calendar-alt mr-2"></i>Nueva programación de turnos</h1>
    <div class="container mt-5">
        <div class="text-right mb-3">
            <a class="btn btn-outline-primary btn-rounded" data-mdb-ripple-init data-mdb-ripple-color="dark"
                href="{{ route('turnos.getShifts') }}">
                <i class="fas fa-chevron-circle-left mr-2"></i>Regresar
            </a>
        </div>
        <div class="row">
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    toastr.options = {
                        showMethod: "show",
                        hideMethod: "hide",
                        showDuration: 250,
                        hideDuration: 800,
                        timeOut: 5000,
                    }
                    @if (session('programacion_success'))
                        toastr.success("La programación ha sido generada con exito, ten presente el personal activo.");
                    @elseif (session('error'))
                        toastr.error("{{ session('error') }}");
                    @elseif ($errors->any())
                        @foreach ($errors->all() as $error)
                            toastr.error("{{ $error }}");
                        @endforeach
                    @endif

                    const form = document.getElementById('saved_programation');
                    const btnGuardar = document.getElementById('btn-saved-programation');

                    btnGuardar.addEventListener('click', function(e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "Una vez registrados los turnos no se podrá editar.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: "#28a745",
                            cancelButtonColor: "#e2eaf7",
                            confirmButtonText: "Sí, guardar",
                            cancelButtonText: "Cancelar",
                            customClass: {
                                confirmButton: "btn btn-success btn-rounded",
                                cancelButton: "btn btn-secondary btn-rounded shadow",
                            },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                const inputHidden = document.getElementById('programacion_json');
                                inputHidden.value = JSON.stringify(calendarTurnos);
                                form.submit();
                            }
                        });
                    });
                });
            </script>
        </div>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('turnos.generateResults') }}" id="generateForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-8 col-sm-12">
                            <select class="form-select mb-3 select2" id="zone_select" name="zone">
                                <option value="" disabled selected>Selecciona la zona</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}"
                                        data-range="{{ $zone->schedule ? \Carbon\Carbon::parse($zone->schedule->day_since)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($zone->schedule->day_until)->format('d/m/Y') : 'Sin definir' }}"
                                        data-type="{{ $zone->schedule ? $zone->schedule->val1 . ' x ' . $zone->schedule->val2 : 'Sin definir' }}">
                                        {{ $zone->name . ' (' . $zone->schedule->val1 . ' x ' . $zone->schedule->val2 . ')' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 col-sm-12 text-right">
                            <button type="submit" class="btn btn-primary btn-rounded" id="verProgramacionBtn">
                                Programar turnos <i class="fas fa-eye ml-2"></i>
                            </button>
                        </div>

                        <div class="col-md-12">
                            <!-- info -->
                            <div id="schedule-info" class="mt-2 text-muted small" style="display: none;">
                                <div class="alert alert-info" role="alert">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    <strong>Rango de fecha:</strong> <span id="schedule-range"></span> | <strong>Tipo de
                                        programación:</strong> <span id="schedule-type"></span>
                                </div>
                            </div>

                            <!-- error -->
                            <div id="schedule-alert" class="mt-2 alert alert-danger d-none small" role="alert">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                No hay una programación asociada a la zona seleccionada para ejecutar.
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($personals->isEmpty())
            <div class="text-center">
                <p class="text-muted">No hay personal asociado a esta zona.</p>
            </div>
        @else
            <div class="row">
                <div class="col-md-6 col-sm-12 mb-3">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center">
                            <div
                                style="width: 20px; height: 20px; background-color: #c6e5d2; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-sun" style="font-size: 12px; color: #28a745;"></i>
                            </div>
                            <span style="color: #28a745;">Turno de Día</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div
                                style="width: 20px; height: 20px; background-color: #d0e4ff; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-moon" style="font-size: 12px; color: #0056b3;"></i>
                            </div>
                            <span style="color: #0056b3;">Turno de Noche</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div
                                style="width: 20px; height: 20px; background-color: #f8d7da; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-bed" style="font-size: 12px; color: #842029;"></i>
                            </div>
                            <span class="text-danger">Descanso</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <div
                                style="width: 20px; height: 20px; background-color: #fff3cd; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-house-user" style="font-size: 12px; color: #856404;"></i>
                            </div>
                            <span class="text-warning">Domingo</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 mb-3 text-right">
                    <form id="saved_programation" action="{{ route('turnos.store') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-success btn-rounded btn-sm" id="btn-saved-programation"
                            data-mdb-ripple-init href="{{ route('personal.create') }}">
                            Guardar programación <i class="fas fa-calendar-check ml-2"></i>
                        </button>
                        <input type="hidden" name="zone_id" value="{{ $zoneInfo->id }}">
                        <input type="hidden" name="zone_name" value="{{ $zoneInfo->name }}">
                        <input type="hidden" name="schedule_id" value="{{ $zoneInfo->schedule->id }}">
                        <input type="hidden" name="day_since" value="{{ $zoneInfo->schedule->day_since }}">
                        <input type="hidden" name="day_until" value="{{ $zoneInfo->schedule->day_until }}">
                        <input type="hidden" name="val1" value="{{ $zoneInfo->schedule->val1 }}">
                        <input type="hidden" name="val2" value="{{ $zoneInfo->schedule->val2 }}">
                        <input type="hidden" name="n_workers" value="{{ $zoneInfo->n_workers }}">
                        <input type="hidden" name="salario_base" value="{{ $zoneInfo->salary }}">
                        <input type="hidden" name="programacion_json" id="programacion_json">
                    </form>
                </div>
                <div class="col-md-12 mb-3">
                    <!-- info programación -->
                    <div id="schedule-info" class="mt-2 text-muted small">
                        <div class="alert alert-info" role="alert">
                            <div class="row">
                                <div class="col-12 text-center mb-3">
                                    <h5>PROYECCIÓN TOTAL</h5>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <i class="fas fa-calendar-week mr-2"></i>
                                    <strong>Rango de fecha:</strong>
                                    <span>{{ $zoneInfo->schedule ? \Carbon\Carbon::parse($zoneInfo->schedule->day_since)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($zoneInfo->schedule->day_until)->format('d/m/Y') : 'Sin definir' }}</span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <i class="fas fa-sync mr-2"></i>
                                    <strong>Tipo de
                                        programación:</strong>
                                    <span>{{ $zoneInfo->schedule ? $zoneInfo->schedule->val1 . ' x ' . $zoneInfo->schedule->val2 : 'Sin definir' }}</span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <i class="fas fa-dollar-sign mr-2"></i>
                                    <strong>salario base:</strong>
                                    <span>{{ $zoneInfo->salary . ' COP.' ?? 'Sin definir' }}</span>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <i class="fas fa-user-friends mr-2"></i>
                                    <strong>Número de trabajadores:</strong>
                                    <span>{{ $zoneInfo->n_workers ?? 'Sin definir' }}.</span>
                                </div>
                                 <div class="col-md-6 col-sm-12">
                                    <i class="fas fa-dollar-sign mr-2"></i>
                                    <strong>Otros ingresos:</strong>
                                    <span>{{ $zoneInfo->others_income . ' COP.' ?? 'Sin definir' }}</span>
                                </div>
                                 <div class="col-md-6 col-sm-12">
                                    <i class="fas fa-dollar-sign mr-2"></i>
                                    <strong>Bono x contrato:</strong>
                                    <span>{{ $zoneInfo->contract_bonus . ' COP.' ?? 'Sin definir' }}</span>
                                </div>
                                @if ($totalProjected)
                                    <div class="table-responsive mt-3">
                                        <table
                                            class="table table-bordered table-sm text-sm table-bordered rounded overflow-hidden shadow-sm">
                                            <thead class="table-info">
                                                <tr>
                                                    <th>Tipo de recargo</th>
                                                    <th>Número de horas</th>
                                                    <th>Presupuesto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <script>
                                                    const resumenHoras = {
                                                        recargo_nocturno: {{ $totalProjected['recargo_nocturno'] ?? 0 }},
                                                        festivo_diurno: {{ $totalProjected['festivo_diurno'] ?? 0 }},
                                                        festivo_nocturno: {{ $totalProjected['festivo_nocturno'] ?? 0 }},
                                                        extra_diurna: {{ $horasExtras['diurnas'] ?? 0 }},
                                                        extra_nocturna: {{ $horasExtras['nocturnas'] ?? 0 }},
                                                        festivo_dominical_diurna: {{ $horasExtras['festivo_dominical_diurna'] ?? 0 }},
                                                        festivo_dominical_nocturna: {{ $horasExtras['festivo_dominical_nocturna'] ?? 0 }},
                                                    };

                                                    document.getElementById('horas_totales_input').value = JSON.stringify(resumenHoras);
                                                    document.getElementById('total_presupuesto_input').value = '{{ $valoresExtrasCOP['programacion_total'] ?? 0 }}'
                                                </script>

                                                <tr>
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Recargo nocturno ordinario
                                                        (0,35)</td>
                                                    <td>{{ $totalProjected['recargo_nocturno'] }} Horas</td>
                                                    <td>{{ $valoresExtrasCOP['recargo_nocturno'] }}</td>
                                                </tr>
                                                <tr class="td-warning">
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Diurno dominical festivo
                                                        sin descanso (1,80)</td>
                                                    <td>{{ $totalProjected['festivo_diurno'] }} Horas</td>
                                                    <td>$ 0 COP</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Dominical y/o festivo
                                                        diurno (0,80)</td>
                                                    <td>{{ $totalProjected['festivo_diurno'] }} Horas</td>
                                                    <td>$ 0 COP</td>
                                                </tr>
                                                <tr class="td-warning">
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Recargo nocturno festivo
                                                        sin descanso (2,15)</td>
                                                    <td>{{ $totalProjected['festivo_diurno'] }} Horas</td>
                                                    <td>$ 0 COP</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Recargo nocturno dominical
                                                        y/o festivo (1,15)</td>
                                                    <td>{{ $totalProjected['festivo_nocturno'] }} Horas</td>
                                                    <td>$ 0 COP</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra diurna ordinaria
                                                        (1,25)</td>
                                                    <td>{{ $horasExtras['diurnas'] }} Horas</td>
                                                    <td>{{ $valoresExtrasCOP['extra_diurna'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra nocturna ordinaria
                                                        (1,75)</td>
                                                    <td>{{ $horasExtras['nocturnas'] }} Horas</td>
                                                    <td>{{ $valoresExtrasCOP['extra_nocturna'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra diurna dominical
                                                        festiva (2,00)</td>
                                                    <td>{{ $horasExtras['festivo_dominical_diurna'] ?? 0 }} Horas</td>
                                                    <td>{{ $valoresExtrasCOP['festivo_dominical_diurna'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra nocturna dominical
                                                        festiva (2,50)</td>
                                                    <td>{{ $horasExtras['festivo_dominical_nocturna'] ?? 0 }} Horas</td>
                                                    <td>{{ $valoresExtrasCOP['festivo_dominical_nocturna'] }}</td>
                                                </tr>
                                                {{-- 
                                                <tr>
                                                    <td><i class="fas fa-clock mr-2"></i> Horas totales trabajadas</td>
                                                    <td>{{ $totalProjected['trabajadas'] }} Horas</td>
                                                </tr>
                                                --}}
                                                <tr>
                                                    <td><i class="fas fa-coins mr-2"></i> Nómina proyectada
                                                        ({{ $zoneInfo->salary && $zoneInfo->n_workers ? number_format((int) str_replace('.', '', $zoneInfo->salary) * $zoneInfo->n_workers, 0, ',', '.') . ' COP' : 'Sin definir' }})
                                                    </td>
                                                    <td></td>
                                                    <td class="text-center"><span class="badge badge-info fs-6">
                                                            {{ $valoresExtrasCOP['programacion_total'] }}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive rounded shadow-sm">
                <table id="datatable" class="table table-hover align-middle text-center mb-0 table-soft w-100">
                    <thead class="bg-blue text-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Cédula</th>
                            <th>Detalles</th>
                            <th>Estado</th>
                            @foreach ($dates as $date)
                                @php
                                    $isSunday = $date->dayOfWeekIso == 7;
                                @endphp
                                <th
                                    @if ($isSunday) style="background-color: #fff3cd; color: #856404;" @endif>
                                    {{ $date->isoFormat('MMM DD') }}<br>
                                    <small>
                                        {{ strtoupper($date->isoFormat('ddd')) }}
                                        @if ($isSunday)
                                            <i class="fas fa-house-user ms-1"></i>
                                        @endif
                                    </small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $horas_por_persona = [];
                        @endphp
                        @foreach ($personals as $index => $personal)
                            @php
                                $val1 = $zoneInfo->schedule->val1;
                                $val2 = $zoneInfo->schedule->val2;
                                $scheduleType = "{$val1}x{$val2}";

                                $personaTurnos = [];

                                if ($scheduleType === '2x2') {
                                    $turnos = ['Día', 'Noche', 'Descanso'];
                                    $startTurnIndex = $index % 3;

                                    for ($i = 0; count($personaTurnos) < count($dates); $i++) {
                                        $currentTurn = $turnos[($startTurnIndex + $i) % 3];
                                        $count = $currentTurn === 'Descanso' ? $val2 : $val1;
                                        $personaTurnos = array_merge(
                                            $personaTurnos,
                                            array_fill(0, $count, $currentTurn),
                                        );
                                    }

                                    $personaTurnos = array_slice($personaTurnos, 0, count($dates));
                                } elseif ($scheduleType === '5x2') {
                                    $startWithDay = $index % 2 === 0;
                                    $currentTurn = $startWithDay ? 'Día' : 'Noche';
                                    $personaTurnos = [];

                                    $turnoActualCount = 0;

                                    foreach ($dates as $i => $date) {
                                        if ($turnoActualCount < 5) {
                                            $personaTurnos[] = $currentTurn;
                                        } else {
                                            $personaTurnos[] = 'Descanso';
                                        }

                                        $turnoActualCount++;

                                        if ($turnoActualCount == 7) {
                                            $currentTurn = $currentTurn === 'Día' ? 'Noche' : 'Día';
                                            $turnoActualCount = 0;
                                        }
                                    }

                                    foreach ($dates as $i => $date) {
                                        $programacionPorTrabajador[$personal->id][] = [
                                            'fecha' => $date->toDateString(),
                                            'turno' => $personaTurnos[$i] ?? 'Descanso',
                                        ];
                                    }
                                }

                                $bloques = [];
                                $start = 0;
                                while ($start < count($dates)) {
                                    $bloques[] = array_slice($dates, $start, 7);
                                    $start += 7;
                                }

                                $totalHorasOrdinarias = 0;
                                $totalHorasExtraDiurnas = 0;
                                $totalHorasExtraNocturnas = 0;

                                foreach ($bloques as $bloqueIndex => $bloque) {
                                    $horasSemana = [];
                                    foreach ($bloque as $j => $fechaRaw) {
                                        $fecha = \Carbon\Carbon::parse($fechaRaw);
                                        $i = collect($dates)->search(
                                            fn($d) => $d->toDateString() === $fecha->toDateString(),
                                        );
                                        $turno = $personaTurnos[$i] ?? 'Descanso';

                                        if ($turno === 'Día') {
                                            $horasSemana[] = [
                                                'tipo' => 'diurna',
                                                'cantidad' => $zoneInfo->schedule->day_hours,
                                            ];
                                        } elseif ($turno === 'Noche') {
                                            $horasSemana[] = [
                                                'tipo' => 'nocturna',
                                                'cantidad' => $zoneInfo->schedule->night_hours,
                                            ];
                                        }
                                    }

                                    $acumulado = 0;
                                    foreach ($horasSemana as $info) {
                                        if ($acumulado < 46) {
                                            $disponible = 46 - $acumulado;
                                            if ($info['cantidad'] <= $disponible) {
                                                $totalHorasOrdinarias += $info['cantidad'];
                                                $acumulado += $info['cantidad'];
                                            } else {
                                                $totalHorasOrdinarias += $disponible;
                                                $excedente = $info['cantidad'] - $disponible;
                                                $acumulado = 46;
                                                if ($info['tipo'] === 'diurna') {
                                                    $totalHorasExtraDiurnas += $excedente;
                                                } else {
                                                    $totalHorasExtraNocturnas += $excedente;
                                                }
                                            }
                                        } else {
                                            if ($info['tipo'] === 'diurna') {
                                                $totalHorasExtraDiurnas += $info['cantidad'];
                                            } else {
                                                $totalHorasExtraNocturnas += $info['cantidad'];
                                            }
                                        }
                                    }
                                }

                                $horas_por_persona[$personal->id] = [
                                    'ordinarias' => $totalHorasOrdinarias,
                                    'extra_diurnas' => $totalHorasExtraDiurnas,
                                    'extra_nocturnas' => $totalHorasExtraNocturnas,
                                ];
                            @endphp

                            <tr>
                                <td>{{ $personal->name }}</td>
                                <td>C.C {{ $personal->document }}</td>
                                <td>
                                    <button type="button" class="btn btn-floating btn-sm btn-primary btn-personal"
                                        data-bs-toggle="modal" data-bs-target="#detalleTurnoModal" id="btn-detail"
                                        data-id="{{ $personal->id }}" data-photo="{{ $personal->photo }}"
                                        data-nombre="{{ $personal->name . ' ' . $personal->lastname }}"
                                        data-documento="C.C {{ $personal->document }}" data-celular="{{ $personal->phone }}"
                                        data-zone='{{ $zoneInfo->id }}'>
                                        <i class="fas fa-eye" title="Ver proyectada"></i>
                                    </button>
                                </td>
                                <td>
                                    @if ($personal->status === 1)
                                        <span class="badge rounded-pill badge-success">Activo</span>
                                    @else
                                        <span class="badge rounded-pill badge-danger">Inactivo</span>
                                    @endif
                                </td>
                                @foreach ($dates as $i => $date)
                                    @php
                                        $isSunday = $date->dayOfWeekIso == 7;
                                        $turno = $personaTurnos[$i] ?? 'Descanso';

                                        $bg = match ($turno) {
                                            'Día' => 'turno-dia',
                                            'Noche' => 'turno-noche',
                                            'Descanso' => 'turno-descanso',
                                            default => '',
                                        };

                                        $horario = match ($turno) {
                                            'Día' => $zoneInfo->schedule->day_hour_since .
                                                ' - ' .
                                                $zoneInfo->schedule->day_hour_until,
                                            'Noche' => $zoneInfo->schedule->night_hour_since .
                                                ' - ' .
                                                $zoneInfo->schedule->night_hour_until,
                                            'Descanso' => 'D',
                                            default => '',
                                        };
                                    @endphp

                                    <td class="{{ $bg }}" data-fecha="{{ $date->toDateString() }}"
                                        data-personal-id="{{ $personal->id }}"
                                        @if ($isSunday) style="background-color: #fff3cd; color: #856404;" @endif>
                                        {{ $horario }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        <script>
                            window.programaciones = @json($programacionPorTrabajador);
                        </script>
                    </tbody>
                </table>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="detalleTurnoModal" tabindex="-1" aria-labelledby="detalleTurnoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered"> <!-- modal grande -->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Programación del trabajador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-primary" role="alert">
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Nombres:</div>
                                    <div class="col-8" id="modal-nombre"></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 fw-bold">Número de documento:</div>
                                    <div class="col-8" id="modal-documento"></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4 fw-bold">Número de teléfono:</div>
                                    <div class="col-8" id="modal-phone"></div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-4 fw-bold">Dias totales trabajados:</div>
                                    <div class="col-8" id="modal-days-workers"></div>
                                </div>
                            </div>

                            <!-- Aquí va el calendario -->
                            <div id="calendarTurnoPersonal" style="height: 500px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

    @endif

    </div>
    <script>
        const calendarTurnos = {};
        @foreach ($personals as $index => $personal)
            @php
                $val1 = $zoneInfo->schedule->val1;
                $val2 = $zoneInfo->schedule->val2;
                $scheduleType = "{$val1}x{$val2}";
                $personaTurnos = [];

                $dayStart = \Carbon\Carbon::parse($zoneInfo->schedule->day_hour_since)->format('H:i');
                $dayEnd = \Carbon\Carbon::parse($zoneInfo->schedule->day_hour_until)->format('H:i');
                $nightStart = \Carbon\Carbon::parse($zoneInfo->schedule->night_hour_since)->format('H:i');
                $nightEnd = \Carbon\Carbon::parse($zoneInfo->schedule->night_hour_until)->format('H:i');

                if ($scheduleType === '2x2') {
                    $turnos = ['Día', 'Noche', 'Descanso'];
                    $startTurnIndex = $index % 3;
                    for ($i = 0; count($personaTurnos) < count($dates); $i++) {
                        $currentTurn = $turnos[($startTurnIndex + $i) % 3];
                        $count = $currentTurn === 'Descanso' ? $val2 : $val1;
                        $personaTurnos = array_merge($personaTurnos, array_fill(0, $count, $currentTurn));
                    }
                    $personaTurnos = array_slice($personaTurnos, 0, count($dates));
                } elseif ($scheduleType === '5x2') {
                    $startWithDay = $index % 2 === 0;
                    $currentTurn = $startWithDay ? 'Día' : 'Noche';
                    $turnoActualCount = 0;
                    for ($i = 0; $i < count($dates); $i++) {
                        if ($turnoActualCount < 5) {
                            $personaTurnos[] = $currentTurn;
                        } else {
                            $personaTurnos[] = 'Descanso';
                        }
                        $turnoActualCount++;
                        if ($turnoActualCount == 7) {
                            $currentTurn = $currentTurn === 'Día' ? 'Noche' : 'Día';
                            $turnoActualCount = 0;
                        }
                    }
                }

                $eventos = collect($personaTurnos)
                    ->map(function ($turno, $i) use ($dates, $dayStart, $dayEnd, $nightStart, $nightEnd) {
                        if ($turno === 'Descanso') {
                            return [
                                'title' => 'D',
                                'start' => $dates[$i]->toDateString(),
                                'allDay' => true,
                                'classNames' => ['turno-descanso'],
                            ];
                        }

                        if ($turno === 'Día') {
                            return [
                                'title' => "$dayStart - $dayEnd",
                                'start' => $dates[$i]->toDateString(),
                                'allDay' => true,
                                'classNames' => ['turno-dia'],
                            ];
                        }

                        if ($turno === 'Noche') {
                            return [
                                'title' => "$nightStart - $nightEnd",
                                'start' => $dates[$i]->toDateString(),
                                'allDay' => true,
                                'classNames' => ['turno-noche'],
                            ];
                        }
                    })
                    ->filter()
                    ->values();
            @endphp

            calendarTurnos["{{ $personal->id }}"] = {!! json_encode($eventos) !!};
        @endforeach
    </script>

@stop

@section('footer')
    <div class="text-footer">
        <strong>Copyright &copy; {{ date('Y') }}
            <a href="#" class="text-blue">ISVI Ltda.</a>
        </strong>
        Todos los derechos reservados.
        <div class="float-end d-none d-sm-inline-block">
            <b>Versión</b> {{ env('APP_VERSION') }}
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.0/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        table,
        .dataTables_wrapper,
        .modal,
        {
        font-size: 15px !important;
        }

        .form-label {
            font-weight: normal !important;
        }

        .small-input {
            font-size: 14px;
            padding: 5px;
        }

        .toast {
            opacity: 1 !important;
        }

        .select2-container--default .select2-selection--single.is-invalid {
            border: 1px solid #dc3545;
            border-radius: .25rem;
        }

        thead th {
            position: sticky;
            top: 0;
            background: #f8f9fa;
            z-index: 1;
        }

        #verProgramacionBtn {
            display: none;
        }

        .turno-dia {
            background-color: #d1f0db !important;
            color: #1e7e34 !important;
            font-weight: 600 !important;
        }

        .turno-noche {
            background-color: #d0e4ff !important;
            color: #0056b3 !important;
            font-weight: 600 !important;
        }

        .turno-descanso {
            background-color: #f8d7da !important;
            color: #842029 !important;
            font-weight: 600 !important;
        }

        .turno-secondary {
            background-color: #dee2e6 !important;
            color: #000 !important;
        }

        .table-soft td,
        .table-soft th {
            padding: 0.45rem 0.75rem;
            font-size: 0.9rem;
            font-weight: 400;
            vertical-align: middle;
        }

        .table-soft span,
        .table-soft small {
            font-size: 0.85rem;
            font-weight: 400;
        }

        td.turno-dia,
        td.turno-noche,
        td.turno-descanso {
            white-space: nowrap !important;
        }

        .dataTables_wrapper {
            overflow-x: auto !important;
        }

        .clicked-cell {
            background-color: #6c757d !important;
            color: white !important;
            transition: background-color 0.3s ease, transform 0.3s ease !important;
            transform: scale(1.1) !important;
        }

        table td {
            transition: background-color 0.3s ease !important;
        }

        table td:hover {
            background-color: #d6d8db !important;
            cursor: pointer !important;
        }

        td {
            position: relative !important;
        }

        .fc .fc-event-title-container {
            color: black !important;
            font-weight: 600 !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    {{-- <script src="{{ asset('js/script.js') }}"></script> --}}
    <script src="{{ asset('js/shifts.js') }}"></script>

    <script>
        let calendarInstance;

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('detalleTurnoModal');

            modal.addEventListener('shown.bs.modal', function(event) {
                const buttonInfo = $(event.relatedTarget);
                const nombre = buttonInfo.data('nombre');
                const documento = buttonInfo.data('documento');
                const photoWorker = buttonInfo.data('photo') || 'img/user.png';
                const phone = buttonInfo.data('celular');

                const button = event.relatedTarget;
                const personalId = button.getAttribute('data-id');
                const calendarEl = document.getElementById('calendarTurnoPersonal');
                const eventos = calendarTurnos[personalId] ?? [];

                const diasTrabajados = eventos.filter(event =>
                    event.title !== 'D'
                ).length;

                $('#modal-days-workers').text(`${diasTrabajados} días trabajados`);

                $('#modal-nombre').text(nombre);
                $('#modal-documento').text(documento);
                $('#modal-phone').text(phone);

                if (calendarInstance) {
                    calendarInstance.destroy();
                }

                calendarInstance = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    locale: 'es',
                    height: 500,
                    events: calendarTurnos[personalId] ?? [],
                });

                setTimeout(() => {
                    calendarInstance.render();
                }, 50);
            });
        });


        $(document).ready(function() {

            toastr.options = {
                showMethod: "show",
                hideMethod: "hide",
                showDuration: 250,
                hideDuration: 800,
                timeOut: 5000,
            };
        });
    </script>


@stop
