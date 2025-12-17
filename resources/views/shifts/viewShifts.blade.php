@extends('adminlte::page')

@section('title', 'Turnos')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-calendar-check mr-2"></i>Ver programaciones de turnos</h1>
    <div class="container mt-5">
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
                    @if (session('success'))
                        toastr.success("{{ session('success') }}");
                    @elseif (session('error'))
                        toastr.error("{{ session('error') }}");
                    @elseif ($errors->any())
                        @foreach ($errors->all() as $error)
                            toastr.error("{{ $error }}");
                        @endforeach
                    @endif
                });
            </script>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12 mb-3">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 20px; height: 20px; background-color: #d0e4ff; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-spinner" style="font-size: 12px; color: #0056b3;"></i>
                        </div>
                        <span style="color: #0056b3;">Programaciones en curso</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 20px; height: 20px; background-color: #f8d7da; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-flag-checkered" style="font-size: 12px; color: #842029;"></i>
                        </div>
                        <span class="text-danger">Programaciones terminadas</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 mb-3 text-right">
                @if (Auth::user()->hasRole('Administrador'))
                    <a class="btn btn-primary btn-rounded" data-mdb-ripple-init data-mdb-ripple-color="dark"
                        href="{{ route('turnos.index') }}">
                        Nueva programación de turnos<i class="fas fa-calendar-check ml-2"></i>
                    </a>
                @endif
            </div>
        </div>
        <div class="row">
            @if ($shifts->isNotEmpty())
                @foreach ($shifts as $shift)
                    @php
                        $hoy = \Carbon\Carbon::today();
                        $esActiva = \Carbon\Carbon::parse($shift->day_until)->gt($hoy);
                        $bgColor = $esActiva ? 'bg-primary' : 'bg-danger';
                        $statusShift = $esActiva ? 'En curso' : 'Terminada';
                        $programacion = [
                            'personals' => $shift->personals->unique('id')->values(),
                            'dates' => $shift->dates->values(),
                            'zoneInfo' => $shift->zoneInfo,
                            'val1' => $shift->zoneInfo->schedule->val1,
                            'val2' => $shift->zoneInfo->schedule->val2,
                        ];
                    @endphp
                    <div class="col-md-6 mb-3">
                        <div
                            class="card text-white {{ $bgColor }} shadow-sm d-flex flex-column h-100 position-relative overflow-hidden">
                            <i class="fas fa-calendar-alt fa-6x text-white-50 position-absolute"
                                style="right: 10px; top: 10px; z-index: 0; opacity: 0.2;"></i>

                            <div class="card-body text-left d-flex flex-column py-1 position-relative" style="z-index: 1;">
                                <div class="d-flex align-items-center mt-2">
                                    <h5 class="card-title text-white m-0">
                                        <strong>{{ $shift->zone_name . ' (' . $statusShift . ')' }}</strong>
                                    </h5>
                                </div>
                                <br>
                                <p class="card-text mb-1">
                                    <strong>Programación </strong><span>{{ $shift->val1 . 'X' . $shift->val2 }}</span>
                                </p>
                                <p class="card-text">
                                    Del <strong>{{ $shift->day_since }}</strong> hasta
                                    <strong>{{ $shift->day_until }}</strong>
                                </p>
                                <p class="card-text">
                                    Salario estipulado <strong>${{ $shift->salary }}</strong>
                                </p>
                            </div>

                            <div class="card-footer {{ $bgColor }} border-top-0 d-flex align-items-center justify-content-between position-relative"
                                style="z-index: 1;">
                                <div class="status-indicator"></div>
                                

                                {{-- Logo de la zona --}}
                                <div class="d-flex align-items-center">
                                    <img src="{{ ($shift->zoneInfo && $shift->zoneInfo->logo && file_exists($shift->zoneInfo->logo)) ? asset('storage/' . $shift->zoneInfo->logo) : asset('img/logoo.png') }}"
                                        alt="Logo zona" class="img-fluid rounded"
                                        style="width: 50px; height: 50px; object-fit: contain; margin-right: 10px;">
                                </div>

                                <div class="d-flex ml-auto">
                                    <button type="button"
                                        class="btn btn-sm btn-light btn-rounded mr-2 ver-programacion-btn"
                                        data-bs-toggle="modal" data-bs-target="#viewShiftModal"
                                        data-id="{{ $shift->id }}">
                                        Ver programación
                                        <i class="fas fa-calendar-check ml-2"></i>
                                    </button>

                                    <div id="tabla-programacion-{{ $shift->id }}" class="d-none">
                                        @php
                                            $dates = $shift->dates;
                                            $personals = $shift->personals->unique('id')->values();
                                            $zoneInfo = $shift->zoneInfo;
                                            $val1 = $zoneInfo->schedule->val1;
                                            $val2 = $zoneInfo->schedule->val2;
                                        @endphp

                                        @include(
                                            'shifts.partials.programacion-table',
                                            compact('dates', 'personals', 'zoneInfo', 'val1', 'val2'))
                                    </div>

                                    <button type="button"
                                        class="btn btn-sm btn-light btn-rounded mr-2 ver-programacion-invidivual-btn"
                                        data-bs-toggle="modal" data-bs-target="#viewStaffModal"
                                        data-id="{{ $shift->id }}">
                                        Ver personal
                                        <i class="fas fa-users ml-2"></i>
                                    </button>

                                    <div id="tabla-personal-{{ $shift->id }}" style="display: none;">
                                        @include('shifts.partials.programacion-individual-table', [
                                            'personals' => $shift->personals->unique('id')->values(),
                                        ])
                                    </div>

                                    {{-- JS --}}
                                    <script>
                                        window.shiftData = window.shiftData || {};
                                        window.shiftData[{{ $shift->id }}] = @json($shift);
                                        window.programaciones = @json($programacionPorTrabajador);
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center">
                    <h6 class="text-muted">No hay ninguna programación registrada.</h6>
                </div>
            @endif
            <div class="col-md-4">

            </div>
            <div class="col-md-4">

            </div>
        </div>

        {{-- modal programación --}}
        <div class="modal fade" id="viewShiftModal" tabindex="-1" aria-labelledby="viewShiftModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="viewShiftModalLabel">Ver turnos detallados</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        {{-- Se carga la tabla con la programación general --}}
                    </div>
                    <br>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal"><i
                                class="fas fa-times-circle mr-2"></i>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal personal --}}
        <div class="modal fade" id="viewStaffModal" tabindex="-1" aria-labelledby="viewStaffModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="viewStaffModalLabel">Ver personal asignado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        {{-- información del personal --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal"><i
                                class="fas fa-times-circle mr-2"></i>Cerrar</button>
                        {{-- <button type="button" class="btn btn-success btn-rounded">Guardar cambios</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">
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

        #modal-schedule-table td {
            min-width: 120px;
            white-space: nowrap;
        }

        .modal {
            z-index: 1050;
        }

        .modal-backdrop.show {
            z-index: 1040;
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
    <script>
        const Calendar = FullCalendar.Calendar;
        const esLocale = FullCalendar.esLocale;
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    {{-- <script src="{{ asset('js/shifts.js') }}"></script> --}}
    <script>
        $('.ver-programacion-btn').on('click', function() {
            const shiftId = $(this).data('id');
            const tablaHtml = $('#tabla-programacion-' + shiftId).html();

            $('#viewShiftModal .modal-body').html(tablaHtml);

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewShiftModal'));
            modal.show();
        });

        document.getElementById('viewStaffModal').addEventListener('hidden.bs.modal', function() {
            if (!window.tempCalendarData) return;

            // Limpiamos el contenedor
            $('#calendar').html('');

            // Crear el calendario
            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'es',
                events: window.tempCalendarData.map(p => ({
                    title: p.turno,
                    start: p.fecha,
                    allDay: true
                })),
                height: 'auto'
            });

            calendar.render();

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('calendarModal'));
            modal.show();

            // Limpiamos el dato temporal
            window.tempCalendarData = null;
        });


        $('.ver-programacion-invidivual-btn').on('click', function() {
            const shiftId = $(this).data('id');
            const tablaHtml = $('#tabla-personal-' + shiftId).html();

            $('#viewStaffModal .modal-body').html(tablaHtml);
        });

        let programacionTemporal = null;

        $(document).on('click', '.btn-ver-turnos', function(e) {
            e.preventDefault();

            const personalId = $(this).data('id');
            const programacion = window.programaciones[personalId];

            if (!programacion) {
                alert('No se encontró información de turnos.');
                return;
            }

            // Guardamos datos temporalmente
            programacionTemporal = programacion;

            // Cerramos modal actual
            const modalStaff = bootstrap.Modal.getInstance(document.getElementById('viewStaffModal'));
            if (modalStaff) modalStaff.hide();
        });

        document.getElementById('viewStaffModal').addEventListener('hidden.bs.modal', function() {
            // Limpiamos el contenido anterior
            $('#calendar').html('');

            // Verificamos que tengamos data
            if (!programacionTemporal) return;

            // Crear calendario
            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                locale: 'es',
                events: programacionTemporal.map(p => ({
                    title: p.turno,
                    start: p.fecha,
                    allDay: true
                })),
                height: 'auto'
            });

            calendar.render();

            // Asegurarnos de eliminar cualquier backdrop colgado
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');

            // Mostrar modal calendario
            const calendarModal = new bootstrap.Modal(document.getElementById('calendarModal'));
            calendarModal.show();

            // Limpiar la variable
            programacionTemporal = null;
        });
    </script>
@stop
