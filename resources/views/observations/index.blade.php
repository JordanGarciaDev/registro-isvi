@extends('adminlte::page')

@section('title', 'Novedades')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-comment-dots mr-2"></i>Registro de Novedades</h1>
    <br>
    Acá puedes ver todo el registro de novedades de todas la programaciones.
@stop

@section('content')
    <div class="row justify-content-center">
        <br>
        <div class="col-md-12 text-right mb-3">
            <a class="btn btn-primary btn-rounded btn-sm mt-2 shadow" data-bs-toggle="modal"
                data-bs-target="#modalPlantillaPdf">
                Generar reporte <i class="fas fa-file-signature ml-2"></i>
            </a>
        </div>
        <div class="col-md-12 mb-3">
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

        <div class="col-md-12">
            <div class="card mdb-container">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover align-middle text-center">
                            <thead class="bg-blue text-dark">
                                <tr>
                                    <th>Prioridad</th>
                                    <th>Documento</th>
                                    <th>Personal</th>
                                    <th>Creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($observations as $observation)
                                    <tr>
                                        <td>
                                            @php
                                                $prioridad = strtolower($observation['prioridad'] ?? 'n/a');
                                            @endphp

                                            @if ($prioridad === 'baja')
                                                <span class="text-primary">
                                                    <i class="fas fa-circle text-primary me-1"></i> Baja
                                                </span>
                                            @elseif ($prioridad === 'media')
                                                <span class="text-warning">
                                                    <i class="fas fa-circle text-warning me-1"></i> Media
                                                </span>
                                            @elseif ($prioridad === 'alta')
                                                <span class="text-danger">
                                                    <i class="fas fa-circle text-danger me-1"></i> Alta
                                                </span>
                                            @else
                                                <em class="text-muted">N/A</em>
                                            @endif
                                        </td>
                                        <td class="text-primary">C.C {{ $observation['user_document'] }}</td>
                                        <td>{{ $observation['personal_names'] }}</td>
                                        <td>{{ $observation['created_at']->format('Y-m-d') }}</td>
                                        <td>
                                            {{-- 0:rechazado, 1:pendiente, 2:aprobado --}}
                                            @if ($observation->status === 0)
                                                <span class="badge rounded-pill badge-danger">Rechazado</span>
                                            @elseif ($observation->status === 1)
                                                <span class="badge rounded-pill badge-primary">Pendiente</span>
                                            @else
                                                <span class="badge rounded-pill badge-success">Aprobado</span>
                                            @endif
                                        </td>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-floating btn-sm btn-secondary" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-bars"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item text-info" href="#"
                                                            data-bs-toggle="modal" data-bs-target="#viewWorkerModal"
                                                            data-created="{{ \Carbon\Carbon::parse($observation->created_at)->translatedFormat('j \d\e F \d\e Y \a \l\a\s h:i A') }}"
                                                            data-worker-id="{{ $observation->id }}"
                                                            data-user-register="{{ $observation->user_register }}"
                                                            data-user-releva="{{ $observation->name_personal_releva }}"
                                                            data-user-prioridad="{{ $observation->prioridad }}"
                                                            data-novedad="{{ $observation->observation }}">
                                                            <i class="fas fa-eye me-2"></i> Ver novedad
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-primary btn-descargar-soporte"
                                                            href="{{ asset('storage/' . $observation->path_document) }}"
                                                            data-bs-toggle="tooltip" title="Descargar soporte"
                                                            target="_blank" data-loading="false">
                                                            <i class="fas fa-download me-2"></i> Descargar soporte
                                                            <span class="spinner-border spinner-border-sm ms-2 d-none"
                                                                role="status" aria-hidden="true"></span>
                                                        </a>
                                                    </li>
                                                    @if (Auth::user()->hasRole('Administrador'))
                                                        @if ($observation->status !== 2)
                                                            <li>
                                                                <form class="form-accion"
                                                                    action="{{ route('novedades.update', $observation->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="action" value="aprobar">
                                                                    <button type="submit"
                                                                        class="dropdown-item text-success">
                                                                        <i class="fas fa-check-circle me-2"></i> Aprobar
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                        @if ($observation->status !== 0)
                                                            <li>
                                                                <form class="form-accion"
                                                                    action="{{ route('novedades.update', $observation->id) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <input type="hidden" name="action" value="rechazar">
                                                                    <button type="submit"
                                                                        class="dropdown-item text-danger">
                                                                        <i class="fas fa-times-circle me-2"></i> Rechazar
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="viewWorkerModal" tabindex="-1" aria-labelledby="viewWorkerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="viewWorkerModalLabel">Detalle de Novedad</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Registrado el:</strong> <span id="modal-created-at"></span></p>
                        <p><strong>Persona que registra:</strong> <span id="modal-user-register-text"></span></p>
                        <p><strong>Personal que releva:</strong> <span id="modal-user-releva-text"></span></p>
                        <p><strong>Prioridad:</strong> <strong id="modal-user-prioridad"></strong></p>
                        <p><strong>Novedad:</strong></p>
                        <div class="alert alert-secondary" id="modal-novedad-text"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal para generar reportes --}}
        <div class="modal fade" id="modalPlantillaPdf" tabindex="-1" aria-labelledby="modalPlantillaPdfLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPlantillaPdfLabel">Generar reporte de novedades</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <form method="POST" action="{{ route('novedades.reportes') }}" target="_blank">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <!-- Fecha desde -->
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_desde" class="form-label fw-bold">Fecha desde</label>
                                    <input type="date" class="form-control" name="fecha_desde" id="fecha_desde"
                                        required>
                                </div>

                                <!-- Fecha hasta -->
                                <div class="col-md-6 mb-3">
                                    <label for="fecha_hasta" class="form-label fw-bold">Fecha hasta</label>
                                    <input type="date" class="form-control" name="fecha_hasta" id="fecha_hasta"
                                        required>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="usuarios" class="form-label fw-bold">Seleccionar personas</label>
                                    <select name="usuarios[]" id="usuarios" class="form-select" multiple>
                                        <option value="all">Todos los usuarios</option>
                                        @foreach ($workers as $worker)
                                            <option value="{{ $worker->id }}">
                                                {{ $worker->name }} (C.C {{ $worker->document }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Selecciona el personal que quieres ver o elige "Todos los
                                        usuarios"</small>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-rounded shadow"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger btn-rounded shadow">
                                <i class="fas fa-file-pdf me-2"></i> Generar PDF
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
            <a href="https://isvi.com/" class="text-blue">ISVI Ltda.</a>
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

        .flatpickr-calendar {
            font-size: 12px !important;
        }

        .toast {
            opacity: 1 !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/locales/bootstrap-datepicker.es.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!-- Tu archivo de script personalizado -->
    <script src="{{ asset('js/script.js') }}"></script>
    {{-- <script src="{{ asset('js/users.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            const viewModal = document.getElementById('viewWorkerModal');

            viewModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;

                function safeValue(value) {
                    return value && value !== 'null' && value.trim() !== '' ? value : 'N/A';
                }

                const fields = {
                    '#modal-created-at': safeValue(button.getAttribute('data-created')),
                    '#modal-user-register-text': safeValue(button.getAttribute('data-user-register')),
                    '#modal-user-releva-text': safeValue(button.getAttribute('data-user-releva')),
                    '#modal-user-prioridad': safeValue(button.getAttribute('data-user-prioridad')),
                    '#modal-novedad-text': safeValue(button.getAttribute('data-novedad')),
                };

                for (const [selector, value] of Object.entries(fields)) {
                    const element = viewModal.querySelector(selector);
                    if (element) {
                        element.textContent = value;

                        if (selector === '#modal-user-prioridad') {
                            element.classList.remove('text-danger', 'text-warning', 'text-success',
                                'text-secondary');

                            switch (value.toLowerCase()) {
                                case 'alta':
                                    element.classList.add('text-danger');
                                    break;
                                case 'media':
                                    element.classList.add('text-warning');
                                    break;
                                case 'baja':
                                    element.classList.add('text-success');
                                    break;
                                default:
                                    element.classList.add('text-secondary');
                                    break;
                            }
                        }
                    }
                }
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('.btn-descargar-soporte');
            const forms = document.querySelectorAll(".form-accion");

            $('#usuarios').select2({
                placeholder: "Selecciona personas...",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#modalPlantillaPdf')
            }).on('select2:select', function(e) {
                if (e.params.data.id === "all") {
                    $(this).val("all").trigger("change");
                }
            });


            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    const spinner = this.querySelector('.spinner-border');

                    // Muestra el spinner
                    spinner.classList.remove('d-none');

                    // Opcional: oculta el spinner después de unos segundos por si no descarga automáticamente
                    setTimeout(() => {
                        spinner.classList.add('d-none');
                    }, 3000); // 3 segundos
                });
            });

            forms.forEach(form => {
                form.addEventListener("submit", function(e) {
                    e.preventDefault(); // evita enviar directo

                    let action = form.querySelector("input[name='action']").value;
                    let mensaje = (action === "aprobar") ?
                        "¿Seguro que deseas aprobar esta observación?" :
                        "¿Seguro que deseas rechazar esta observación?";

                    Swal.fire({
                        title: 'Confirmación',
                        text: mensaje,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: (action === "aprobar") ? '#198754' : '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: (action === "aprobar") ? 'Sí, aprobar' :
                            'Sí, rechazar',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            confirmButton: "btn-rounded shadow",
                            cancelButton: "btn-rounded shadow",
                        },
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // ahora sí envía
                        }
                    });
                });
            });
        });
    </script>
@stop
