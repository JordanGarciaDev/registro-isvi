@extends('adminlte::page')

@section('title', 'Programaciones')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-clock mr-2"></i>Tipos de programación</h1>
    <br>
@stop

@section('content')
    <div class="row">
        <br>
        <div class="col-md-12 text-right mb-4">
            @if (Auth::user()->hasRole('Administrador'))
                {{-- <div>
                    <a class="btn btn-success btn-rounded btn-sm" data-mdb-ripple-init>
                        Descargar Excel <i class="fas fa-file-excel ml-2"></i>
                    </a>
                </div> --}}
            @endif
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    toastr.options = {
                        showMethod: "show",
                        hideMethod: "hide",
                        showDuration: 250,
                        hideDuration: 800,
                        timeOut: 5000,
                    }
                    @if (session('success'))
                        toastr.success("{{ session('success') }}");
                    @endif

                    @if (session('error'))
                        toastr.error("{{ session('error') }}");
                    @endif
                });
            </script>
        </div>

        @foreach ($schedules as $schedule)
            <div class="col-md-4 mb-5">
                <div class="card text-center shadow-sm d-flex flex-column h-100">
                    <div class="card-body text-left d-flex flex-column py-1">
                        <div class="d-flex align-items-center mt-2">
                            <h5 class="card-title {{ $schedule->status ? 'text-primary' : 'text-secondary' }} m-0">
                                <strong>{{ $schedule->name }}</strong>
                            </h5>
                        </div>
                        <br>
                        <p class="card-text small">
                            <strong>Programación :</strong> {{ $schedule->val1 . ' x ' . $schedule->val2 }}
                        </p>
                        <p class="card-text small">
                            <strong>Del</strong>
                            {{ \Carbon\Carbon::parse($schedule->day_since)->translatedFormat('j \d\e F') }}
                            <strong>hasta</strong>
                            {{ \Carbon\Carbon::parse($schedule->day_until)->translatedFormat('j \d\e F \d\e Y') }}
                        </p>
                    </div>
                    <div class="card-footer bg-white d-flex align-items-center">
                        <div class="status-indicator {{ $schedule->status ? 'active' : 'inactive' }}"></div>

                        <div class="d-flex ml-auto">
                            @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
                                @if ($schedule->status === 1)
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning btn-rounded mr-2 text-warning"
                                        onclick="openModal({{ $schedule->id }})">
                                        <span class="text-warning">Reprogramar</span>
                                        <i class="fas fa-sync ml-2 text-warning"></i>
                                    </button>
                                @endif

                                <form id="deleteForm-{{ $schedule->id }}"
                                    action="{{ route('programaciones.destroy', $schedule->id) }}" method="POST"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    @if ($schedule->status === 1)
                                        <button type="button" class="btn btn-floating btn-sm btn-info delete-btn"
                                            onclick="confirmDelete(event, {{ $schedule->id }})"
                                            title="Inactivar programación">
                                            <i class="fas fa-toggle-on fa-lg"></i>
                                        </button>
                                    @endif
                                </form>
                                <form id="activateForm-{{ $schedule->id }}"
                                    action="{{ route('programaciones.destroy', $schedule->id) }}" method="POST"
                                    class="m-0">
                                    @csrf
                                    @method('DELETE')
                                    @if ($schedule->status === 0)
                                        <button type="button" class="btn btn-floating btn-sm btn-light delete-btn"
                                            onclick="confirmActivate(event, {{ $schedule->id }})"
                                            title="Activar programación">
                                            <i class="fas fa-toggle-on fa-lg"></i>
                                        </button>
                                    @endif
                                </form>
                            @else
                                <i class="text-muted mr-2">No Disponible</i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal de Reprogramación --}}
            <div class="modal fade" id="editScheduleModal-{{ $schedule->id }}" tabindex="-1"
                aria-labelledby="editScheduleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editScheduleModalLabel">Reprogramar Horario</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <form method="POST" action="{{ route('programaciones.update', $schedule->id) }}"
                            id="editScheduleForm-{{ $schedule->id }}">
                            @csrf
                            @method('PATCH')

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="name-{{ $schedule->id }}">Nombre</label>
                                            <input type="text" id="name-{{ $schedule->id }}" class="form-control"
                                                name="name" value="{{ $schedule->name }}" />
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="schedule_format">Horario</label>
                                            <select id="schedule_format" name="schedule_format" class="form-control">
                                                <option value="12"
                                                    {{ $schedule->schedule_format == 12 ? 'selected' : '' }}>12:00 horas
                                                </option>
                                                <option value="24"
                                                    {{ $schedule->schedule_format == 24 ? 'selected' : '' }}>24:00 horas
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-5 col-sm-12">
                                        <div class="form-group">
                                            <select id="schedule-type-{{ $schedule->id }}" class="form-control"
                                                name="schedule_type">
                                                <option value="2x2" {{ $schedule->schedule_type == '2x2' ? 'selected' : '' }}>2 x
                                                    2 x 2
                                                </option>
                                                <option value="5x2" {{ $schedule->schedule_type == '5x2' ? 'selected' : '' }}>5 x
                                                    2
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-sm-12">
                                        <p class="text-blue">Establece el tipo de programación, por ejemplo: 2x2 o 5x2</p>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" id="fechaDesde-{{ $schedule->id }}" class="form-control"
                                                autocomplete="off" name="day_since" value="{{ $schedule->day_since }}"
                                                placeholder="Fecha desde">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <input type="text" id="fechaHasta-{{ $schedule->id }}"
                                                class="form-control" autocomplete="off" name="day_until"
                                                value="{{ $schedule->day_until }}" placeholder="Fecha hasta">
                                        </div>
                                    </div>
                                </div>
                                <div class="card p-4">
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    style="width: 20px; height: 20px; background-color: #28a745; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-sun" style="font-size: 12px; color: white;"></i>
                                                </div>
                                                <span class="text-muted">Horario diurno</span>
                                            </div>
                                            <input type="text" id="horaDesdeDiurno-{{ $schedule->id }}"
                                                class="form-control border-success" name="day_hour_since"
                                                placeholder="Hora inicio"
                                                value="{{ $schedule->day_hour_since ?? '00:00' }}">
                                        </div>
                                        <div class="col-6 d-flex align-items-end">
                                            <input type="text" id="horaHastaDiurno-{{ $schedule->id }}"
                                                class="form-control border-success" name="day_hour_until"
                                                placeholder="Hora fin"
                                                value="{{ $schedule->day_hour_until ?? '00:00' }}">
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    style="width: 20px; height: 20px; background-color: #0d6efd; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-moon" style="font-size: 12px; color: white;"></i>
                                                </div>
                                                <span class="text-muted">Horario nocturno</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" id="horaDesdeNocturno-{{ $schedule->id }}"
                                                class="form-control border-primary" name="night_hour_since"
                                                placeholder="Hora inicio"
                                                value="{{ $schedule->night_hour_since ?? '00:00' }}">

                                        </div>
                                        <div class="col-6">
                                            <input type="text" id="horaHastaNocturno-{{ $schedule->id }}"
                                                class="form-control border-primary" name="night_hour_until"
                                                placeholder="Hora fin"
                                                value="{{ $schedule->night_hour_until ?? '00:00' }}">
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center mb-2">
                                                <div
                                                    style="width: 20px; height: 20px; background-color: #adb5bd; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-bed" style="font-size: 12px; color: white;"></i>
                                                </div>
                                                <span class="text-muted">Horario descanso</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" id="horaDesdeDescanso-{{ $schedule->id }}"
                                                class="form-control" value="{{ $schedule->break_hour_since ?? '00:00' }}"
                                                name="break_hour_since" placeholder="Hora inicio">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" id="horaHastaDescanso-{{ $schedule->id }}"
                                                class="form-control" value="{{ $schedule->break_hour_until ?? '00:00' }}"
                                                name="break_hour_until" placeholder="Hora fin">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                                    Cancelar <i class="fas fa-times-circle ml-2 fa-lg"></i>
                                </button>
                                <button type="submit" class="btn btn-success btn-rounded" id="updateBtn">
                                    Actualizar <i class="fas fa-check-circle ml-2 fa-lg"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @if (Auth::user()->hasRole('Administrador'))
            <div class="col-md-4">
                <div class="card text-center border-dashed shadow-sm new-card" style="cursor: pointer;"
                    data-toggle="modal" data-target="#newSchedule">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center"
                        style="height: 160px;">
                        <i class="fas fa-plus-circle text-primary icono" style="font-size: 50px;"></i>
                        <h6 class="mt-2 text-muted">Nueva programación</h6>
                    </div>
                </div>
            </div>
        @endif

        {{-- Modal Create schedules --}}
        <div class="modal fade" id="newSchedule" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Crear Nueva Programación</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('programaciones.store') }}" id="form-modal">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="name">Nombre</label>
                                        <input type="text" id="name" class="form-control" name="name" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="schedule_format">Horario</label>
                                        <select id="schedule_format" name="schedule_format" class="form-control">
                                            <option value="12">12:00 horas</option>
                                            <option value="24">24:00 horas</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row d-flex align-items-center">
                                <div class="col-md-5 col-sm-12">
                                    <div class="form-group">
                                        <label for="schedule_type">Tipo de programación</label>
                                        <select id="schedule_type" class="form-control"
                                            name="schedule_type">
                                            <option value="2x2" {{ $schedule->schedule_type == '2x2' ? 'selected' : '' }}>2
                                                x 2 x 2
                                            </option>
                                            <option value="5x2" {{ $schedule->schedule_type == '5x2' ? 'selected' : '' }}>5
                                                x 2
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-7 col-sm-12">
                                    <p class="text-blue">Establece el tipo de programación, por ejemplo: 2x2 o 5x2</p>
                                </div>
                            </div>

                            <div class="row mt-3 mb-3">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="fecha_desde">Fecha inicio</label>
                                        <input type="text" id="fechaDesde" class="form-control" autocomplete="off"
                                            name="day_since" placeholder="Fecha desde">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="fecha_hasta">Fecha final</label>
                                        <input type="text" id="fechaHasta" class="form-control" autocomplete="off"
                                            name="day_until" placeholder="Fecha hasta">
                                    </div>
                                </div>
                            </div>

                            <div class="card p-4">
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center mb-2">
                                            <div
                                                style="width: 20px; height: 20px; background-color: #28a745; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-sun" style="font-size: 12px; color: white;"></i>
                                            </div>
                                            <span class="text-muted">Horario diurno</span>
                                        </div>
                                        <input type="text" id="horaDesdeDiurno" class="form-control border-success"
                                            name="day_hour_since" placeholder="Hora inicio" value="06:00">
                                    </div>
                                    <div class="col-6 d-flex align-items-end">
                                        <input type="text" id="horaHastaDiurno" class="form-control border-success"
                                            name="day_hour_until" placeholder="Hora fin" value="18:00">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center mb-2">
                                            <div
                                                style="width: 20px; height: 20px; background-color: #0d6efd; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-moon" style="font-size: 12px; color: white;"></i>
                                            </div>
                                            <span class="text-muted">Horario nocturno</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" id="horaDesdeNocturno" class="form-control border-primary"
                                            name="night_hour_since" placeholder="Hora inicio" value="18:00">

                                    </div>
                                    <div class="col-6">
                                        <input type="text" id="horaHastaNocturno" class="form-control border-primary"
                                            name="night_hour_until" placeholder="Hora fin" value="06:00">
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center mb-2">
                                            <div
                                                style="width: 20px; height: 20px; background-color: #adb5bd; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-bed" style="font-size: 12px; color: white;"></i>
                                            </div>
                                            <span class="text-muted">Horario descanso</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <input type="text" id="horaDesdeDescanso" class="form-control" value="06:00"
                                            name="break_hour_since" placeholder="Hora inicio">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" id="horaHastaDescanso" class="form-control" value="18:00"
                                            name="break_hour_until" placeholder="Hora fin">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">
                                Cancelar <i class="fas fa-times-circle ml-2 fa-lg"></i>
                            </button>
                            <button type="submit" class="btn btn-success btn-rounded" id="saveBtn">
                                Guardar <i class="fas fa-check-circle ml-2 fa-lg"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
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
    <!-- MDB CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.0/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timedropper@1.0.0/timedropper.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('css/schedule.css') }}">
@stop

@section('js')

    <!-- Datatables -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/timedropper@1.0.0/timedropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>

    <!-- Tu archivo de script personalizado -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/schedule.js') }}"></script>

@stop
