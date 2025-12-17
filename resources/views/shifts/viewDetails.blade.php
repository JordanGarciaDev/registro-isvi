@extends('adminlte::page')

@section('title', 'Turnos del personal')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-calendar-check mr-2"></i>Ver turnos de
        {{ $personal->name . ' ' . $personal->lastname }}</h1>
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
                <a class="btn btn-outline-primary btn-rounded" data-mdb-ripple-init data-mdb-ripple-color="dark"
                    href="{{ route('turnos.getShifts') }}">
                    <i class="fas fa-chevron-circle-left mr-2"></i>Regresar
                </a>
            </div>
            <div class="col-12 text-right">
                <!-- Botón que abre el modal -->
                @if ($fechaFinal >= now()->toDateString())
                    <button type="button" class="btn btn-primary btn-rounded btn-nueva-novedad"
                        data-personal-id="{{ $personal->id }}" data-schedule-id="{{ $schedule_id ?? '' }}"
                        data-mdb-ripple-init data-bs-toggle="modal" data-bs-target="#modalNuevaNovedad">
                        <i class="fas fa-comments mr-2"></i> Nueva Novedad
                    </button>
                @endif
                {{-- <button type="button" class="btn btn-warning btn-rounded" data-bs-toggle="modal"
                    data-bs-target="#modalVerProgramada">
                    Ver programada <i class="fas fa-coins ms-2"></i>
                </button> --}}
            </div>
        </div>
        {{-- Calendario --}}
        <div id="calendar"></div>
    </div>

    <!-- Modal novedad -->
    <div class="modal fade" id="modalNuevaNovedad" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
                <form method="POST" action="{{ route('novedades.store') }}" id="form-modal" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header text-white">
                        <h5 class="modal-title text-primary" id="modalLabel">Registrar nueva novedad</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="personal_id" value="{{ $personal->id }}">
                        <input type="hidden" name="schedule_id" value="{{ $scheduleId }}">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Trabajador</label>
                                <div class="form-control bg-light">
                                    {{ $personal->document . ' - ' . $personal->name . ' ' . $personal->lastname }}
                                </div>
                                <input type="hidden" name="personal" value="{{ $personal->id }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Prioridad</label>
                                <select name="prioridad" class="form-select" required>
                                    <option value="" selected disabled>Selecciona la prioridad</option>
                                    <option value="Baja" class="text-success">🟢 Baja</option>
                                    <option value="Media" class="text-warning">🟡 Media</option>
                                    <option value="Alta" class="text-danger">🔴 Alta</option>
                                </select>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-3">
                                <div class="form-group">
                                    <input type="text" id="date_since" class="form-control" name="date_since"
                                        placeholder="Fecha inicio" autocomplete="off">
                                </div>
                                @error('date_since')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-sm-12 mb-3">
                                <div class="form-group">
                                    <input type="text" id="date_until" class="form-control" name="date_until"
                                        placeholder="Fecha final" autocomplete="off">
                                </div>
                                @error('date_until')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-outline mb-3">
                                    <textarea id="novedad" class="form-control" name="novedad" rows="4"></textarea>
                                    <label class="form-label" for="novedad">Escriba la novedad</label>
                                </div>
                                @error('novedad')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <input type="file" id="soporte" name="soporte" accept=".pdf"
                                    style="display: none;">

                                <button type="button" class="btn btn-outline-danger" id="custom-soporte-btn">
                                    <i class="fas fa-file-upload"></i> Cargar soporte (solo .PDF)
                                </button>
                                <br>
                                <span id="soporte-filename" class="ml-2 text-muted">Peso máximo permitido 2MB</span>

                                @error('soporte')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <select name="personal_releva" id="personal_releva" class="form-select" required>
                                    <option value="" selected disabled>Personal que releva</option>
                                    @foreach ($workers as $worker)
                                        <option value="{{ $worker->id }}">
                                            {{ $worker->name }} {{ $worker->lastname }} ({{ $worker->document }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                            Cancelar <i class="fas fa-times-circle ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success btn-rounded">
                            Guardar <i class="fas fa-check-circle ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Programada -->
    <div class="modal fade" id="modalVerProgramada" tabindex="-1" aria-labelledby="modalProgramadaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="max-height: 90vh; overflow-y: auto;">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalProgramadaLabel">Programación del trabajador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive mb-3">
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
                                <tr>
                                    <td><i class="fas fa-calendar-day mr-2"></i> Recargo nocturno ordinario
                                        (0,35)</td>
                                    <td>20 Horas</td>
                                    <td>$ 135.000 COP</td>
                                </tr>
                                <tr class="td-warning">
                                    <td><i class="fas fa-calendar-day mr-2"></i> Diurno dominical festivo
                                        sin descanso (1,75)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-day mr-2"></i> Dominical y/o festivo
                                        diurno (0,75)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                <tr class="td-warning">
                                    <td><i class="fas fa-calendar-day mr-2"></i> Recargo nocturno festivo
                                        sin descanso (2,10)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-day mr-2"></i> Recargo nocturno dominical
                                        y/o festivo (1,10)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra diurna ordinaria
                                        (1,25)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra nocturna ordinaria
                                        (1,75)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra diurna dominical
                                        festiva (2,00)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-day mr-2"></i> Extra nocturna dominical
                                        festiva (2,50)</td>
                                    <td>0 Horas</td>
                                    <td>$ 0 COP</td>
                                </tr>
                                {{-- 
                                                <tr>
                                                    <td><i class="fas fa-clock mr-2"></i> Horas totales trabajadas</td>
                                                    <td>{{ $totalProjected['trabajadas'] }} Horas</td>
                                                </tr>
                                                --}}
                                <tr>
                                    <td><i class="fas fa-coins mr-2"></i> Nómina proyectada

                                    </td>
                                    <td></td>
                                    <td class="text-center"><span class="badge badge-info fs-6">
                                            $ 130.000 COP</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn-rounded" data-bs-dismiss="modal">
                        Descargar Xlsx <i class="fas fa-file-excel ms-2"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        Cerrar <i class="fas fa-times-circle ms-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar la novedad -->
    <div class="modal fade" id="modalNovedadTexto" tabindex="-1" aria-labelledby="modalNovedadLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalNovedadLabel"><i class="fas fa-comment-dots me-2"></i>Detalle de
                        novedad</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="textoNovedad"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-rounded" data-bs-dismiss="modal">
                        Cerrar <i class="fas fa-times-circle ms-2"></i>
                    </button>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css">
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

        .fc-event-title {
            color: #000 !important;
        }

        .novedad-icon .fc-event-title {
            font-size: 1.2rem !important;
            font-weight: bold !important;
            color: #d63384 !important;
            /* Un fucsia llamativo */
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/es.js" defer></script>
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    {{-- <script src="{{ asset('js/shifts.js') }}"></script> --}}
    @php
        $startDate = array_key_first($turns->toArray());
    @endphp
    <script>
        $(document).ready(function() {
            const fechaMin = '{{ $fechaInicial }}';
            const fechaMax = '{{ $fechaFinal }}';

            $('#date_since, #date_until').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true,
                language: 'es'
            });

            // Select2 fix
            $('.select2').select2({
                dropdownParent: $('#modalNuevaNovedad')
            });

            // Soporte personalizado
            $('#custom-soporte-btn').on('click', function() {
                $('#soporte').click();
            });

            $('#soporte').on('change', function() {
                const filename = this.files.length ? this.files[0].name : 'Peso máximo permitido 2MB';
                $('#soporte-filename').text(filename);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                initialDate: '{{ $startDate }}',
                locale: 'es',
                height: 'auto',
                events: [
                    @foreach ($turns as $date => $dayTurns)
                        @php $turn = $dayTurns->first(); @endphp {
                            title: '{{ $turn->turn }}',
                            start: '{{ $turn->date_programation }}',
                            allDay: true,
                            classNames: [
                                @if ($turn->turn === '06:00 - 18:00')
                                    'turno-dia'
                                @elseif ($turn->turn === '18:00 - 06:00')
                                    'turno-noche'
                                @else
                                    'turno-descanso'
                                @endif
                            ]
                        },
                    @endforeach

                    @foreach ($observations as $obs)
                        {
                            title: '<i class="fas fa-comment-dots me-1"></i> Novedad',
                            start: '{{ $obs->date_since }}',
                            allDay: true,
                            classNames: ['novedad-icon'],
                            extendedProps: {
                                descripcion: @json($obs->observation)
                            }
                        },
                    @endforeach

                ],

                eventDidMount: function(info) {
                    if (info.event.extendedProps.descripcion) {
                        tippy(info.el, {
                            content: info.event.extendedProps.descripcion,
                            placement: 'top',
                            theme: 'light',
                        });
                    }
                },

                eventClick: function(info) {
                    if (info.event.classNames.includes('novedad-icon')) {
                        const descripcion = info.event.extendedProps.descripcion;

                        $('#textoNovedad').text(descripcion);
                        const modal = new bootstrap.Modal(document.getElementById('modalNovedadTexto'));
                        modal.show();
                    }
                },

                eventContent: function(arg) {
                    const isNovedad = arg.event.classNames.includes('novedad-icon');

                    if (isNovedad) {
                        return {
                            html: `
                                <div class="fc-novedad-event">
                                    <i class="fas fa-comment-dots me-1 ml-2"></i><b> Novedad</b>
                                </div>
                            `
                        };
                    }

                    return true;
                },

            });

            calendar.render();
        });
    </script>
@stop
