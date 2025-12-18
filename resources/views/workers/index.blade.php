@extends('adminlte::page')

@section('title', 'Personal Operativo')

@section('content_header')
    <h1 class="mdb-container">
        <i class="fas fa-users mr-2"></i>Personal Operativo
    </h1>
    <br>
@stop

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="row justify-content-center">
        <br>
        <div class="col-md-12 d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                {{-- <a class="btn btn-outline-info btn-rounded btn-sm" data-mdb-ripple-init data-mdb-ripple-color="dark">
                    Filtrar <i class="fas fa-search ml-2"></i>
                </a> --}}
                @php
                    $status = request('status', 1);
                    $isInactive = $status == 0;
                @endphp
                @if ($inactiveCount !== 0)
                    <a href="{{ route('personal.index', ['status' => $isInactive ? 1 : 0]) }}"
                       class="btn btn-outline-{{ $isInactive ? 'secondary' : 'danger' }} btn-rounded btn-sm"
                       data-mdb-ripple-init data-mdb-ripple-color="dark">
                        {{ $isInactive ? 'Personal activo' : 'Personal inactivo' }}
                        <i class="fas {{ $isInactive ? 'fa-users' : 'fa-users-slash' }} ml-2"></i>
                    </a>
                @endif
            </div>

            <div>
                @if (Auth::user()->hasRole('Administrador'))
                    <a class="btn btn-success btn-rounded btn-sm mt-2" data-bs-toggle="modal"
                       data-bs-target="#modalPlantillaExcel">
                        Importar personal <i class="fas fa-file-excel ml-2"></i>
                    </a>
                    <a class="btn btn-primary btn-rounded btn-sm" data-mdb-ripple-init
                       href="{{ route('personal.create') }}">
                        Nuevo <i class="fas fa-plus-circle ml-2"></i>
                    </a>
                @endif
            </div>
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

                        <table id="datatable" class="table table-sm table-hover align-middle text-center mb-0 table-soft">
                            <thead class="bg-blue text-dark fw-bold">
                            <tr>
                                <th class="fw-bold">Foto</th>
                                <th class="fw-bold">Documento</th>
                                <th class="fw-bold">Nombres</th>
                                <th class="fw-bold">Télefono</th>
                                <th class="fw-bold">Cargo</th>
                                <th class="fw-bold">Área</th>
                                <th class="fw-bold">Estado</th>
                                <th class="fw-bold">Acciones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($workers as $worker)

                                <tr>
                                    <td>
                                        <img src="{{ $worker['photo']
                                                ? asset('storage/' . $worker['photo'])
                                                : asset('img/user.png') }}"
                                             alt="Foto de {{ $worker['name'] }}"
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%; display: block;">
                                    </td>
                                    <td><span class="badge rounded-pill badge-primary">C.C
                                                {{ $worker['document'] }}</span>
                                    <td>{{ $worker['name'] . ' ' . $worker['lastname'] }}</td>
                                    <td>
                                        @if ($worker['phone'])
                                            <span>{{ $worker['phone'] }}</span>
                                        @else
                                            <i class="text-muted fs-9">No disponible</i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($worker['type'])
                                            <span>{{ $worker['type'] }}</span>
                                        @else
                                            <i class="text-muted fs-9">No disponible</i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($worker['nombre_area'])
                                            <span>{{ $worker['nombre_area'] }}</span>
                                        @else
                                            <i class="text-muted fs-9">No disponible</i>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($worker['status'] === 1)
                                            <span class="badge rounded-pill badge-success">Activo</span>
                                        @else
                                            <span class="badge rounded-pill badge-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-floating btn-sm btn-secondary" type="button"
                                                    id="dropdownMenuButton{{ $worker['id'] }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="fas fa-bars"></i>
                                            </button>
                                            <ul class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton{{ $worker['id'] }}">
                                                <li>
                                                    <a class="dropdown-item text-info" href="#"
                                                       data-bs-toggle="modal" data-bs-target="#viewWorkerModal"
                                                       data-name="{{ $worker['name'] . ' ' . $worker['lastname'] }}"
                                                       data-photo="{{ asset('storage/' . $worker['photo']) }}"
                                                       data-document="{{ $worker['document'] }}"
                                                       data-type="{{ $worker['type'] }}"
                                                       data-phone="{{ $worker['phone'] }}"
                                                       data-cost-center="{{ $worker['cost_center'] }}"
                                                       data-area="{{ $worker['nombre_area'] }}"
                                                       data-proyecto="{{ $worker['proyecto'] }}"
                                                       data-email="{{ $worker['email'] }}"
                                                       data-created="{{ $worker->created_at->timezone('America/Bogota')->translatedFormat('j \d\e F \d\e Y \a \l\a\s h:i A') }}"
                                                       data-worker-id="{{ $worker->id }}"
                                                       data-status="{{ $worker['status'] === 1 ? 'Activo' : 'Inactivo' }}">
                                                        <i class="fas fa-eye"></i> Ver Detalles
                                                    </a>
                                                </li>
                                                @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
                                                    <li>
                                                        <a class="dropdown-item text-warning"
                                                           href="{{ route('personal.edit', $worker['id']) }}">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @if ($worker['status'] === 1)
                                                            <form id="inactivateForm-{{ $worker['id'] }}"
                                                                  action="{{ route('personal.destroy', $worker['id']) }}"
                                                                  method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="dropdown-item text-danger"
                                                                        onclick="confirmInactivation({{ $worker['id'] }})">
                                                                    <i class="fas fa-user-slash"></i> Inactivar
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form id="activateForm-{{ $worker['id'] }}"
                                                                  action="{{ route('personal.destroy', $worker['id']) }}"
                                                                  method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button"
                                                                        class="dropdown-item text-primary"
                                                                        onclick="confirmActivation({{ $worker['id'] }})">
                                                                    <i class="fas fa-user-slash"></i> Activar
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </li>
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

            {{-- modal plantilla xlsx --}}
            <div class="modal fade" id="modalPlantillaExcel" tabindex="-1" aria-labelledby="modalPlantillaExcelLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="modalPlantillaExcelLabel">
                                Plantilla de Carga Masiva
                            </h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body text-center">
                            <p>
                                Descarga la plantilla Excel para registrar datos masivamente. Luego vuelvela a cargar por
                                medio de la opción "Cargar archivo".
                            </p>

                            <a href="{{ asset('plantillas/plantilla_personal.xlsx') }}"
                               class="btn btn-outline-success mb-3 btn-rounded" download>
                                <i class="fas fa-download me-2"></i> Descargar plantilla
                            </a>

                            <hr>

                            <form method="POST" action="{{ route('worker.import') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <input type="file" name="archivo_excel" class="form-control" accept=".xlsx, .xls"
                                           required>
                                </div>
                                <br>
                                <button type="submit" class="btn btn-primary btn-rounded">
                                    <i class="fas fa-upload me-2"></i> Importar y guardar
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal para ver las zonas -->
            <div class="modal fade" id="zonasModal" tabindex="-1" aria-labelledby="zonasModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Zonas asignadas</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="alert alert-info" role="alert">
                                        <i class="fas fa-pen-square mr-2"></i>
                                        Ultima fecha de edición: <strong id="lastUpdated"></strong>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <ul id="zonasList" class="list-group">
                                </div>
                            </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal ver -->
            <div class="modal fade" id="viewWorkerModal" tabindex="-1" aria-labelledby="viewWorkerModalLabel">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewWorkerModalLabel">Detalles del personal operativo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center mb-3">
                                <img id="workerPhoto" src="" class="img-fluid"
                                     style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    @if (Auth::user()->hasRole('Administrador'))
                                        <button type="button"
                                                class="btn btn-floating btn-sm btn-warning edit-btn mb-2 mt-2" title="Editar"
                                                id="editWorkerBtn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        {{-- Inactive --}}
                                        <button type="button"
                                                class="btn btn-floating btn-sm btn-danger delete-btn mb-2 mt-2 d-none"
                                                title="Inactivar" id="inactivateWorkerBtn">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                        {{-- Active --}}
                                        <button type="button"
                                                class="btn btn-floating btn-sm btn-primary delete-btn mb-2 mt-2 d-none"
                                                title="Activar" id="activateWorkerBtn">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    @endif
                                </div>
                                <h5 id="workerName" class="mt-2"></h5>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="text-start fs-9">
                                        <p><strong>Documento:</strong> <span id="workerDocument"></span></p>
                                        <p><strong>Teléfono:</strong> <span id="workerPhone"></span></p>
                                        <p><strong>Decripción del cargo:</strong> <span id="workerType"></span></p>
                                        <p><strong>Área:</strong> <span id="workerArea"></span></p>
                                        <p><strong>Estado:</strong> <span id="workerStatus" class="badge rounded-pill"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <p><strong>Centro de costo:</strong> <span id="workerCostCenter"></span></p>
                                    <p><strong>Descripción del proyecto:</strong> <span id="workerProyecto"></span></p>
                                    <p><strong>Correo electrónico:</strong> <span id="workerEmail"></span></p>
                                    </p>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <div class="alert alert-info" role="alert"
                                         style="display: flex; align-items: center;">
                                        <i class="fas fa-file-signature mr-2"></i>
                                        <p style="margin: 0;">Fecha de registro:  <strong
                                                    id="workerCreated"></strong></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-rounded btn-sm"
                                    data-bs-dismiss="modal">Cancelar
                                <i class="fas fa-times-circle ml-2 fa-lg ml-2"></i>
                            </button>
                        </div>
                    </div>
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

        .modal-body p {
            font-size: 0.85rem !important;
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
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/workersIndex.js') }}"></script>
@stop
