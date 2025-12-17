@extends('adminlte::page')

@section('title', 'Regiones')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-map mr-2"></i>Regiones/Sucursales</h1>
    <br>
@stop

@section('content')
    <div class="row justify-content-center">
        <br>
        <div class="col-md-12 d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                {{--  --}}
            </div>
            @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
                <div>
                    <a class="btn btn-success btn-rounded btn-sm" data-mdb-ripple-init>
                        Descargar Excel <i class="fas fa-file-excel ml-2"></i>
                    </a>
                    <a class="btn btn-primary btn-rounded btn-sm" data-mdb-ripple-init data-bs-toggle="modal"
                        data-bs-target="#ModalCreate">
                        Nuevo <i class="fas fa-plus-circle ml-2"></i>
                    </a>
                </div>
            @endif
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
                                    <th>Nombre</th>
                                    <th>Zonas</th>
                                    <th>Persona Registro</th>
                                    <th>Fecha creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($branches as $branche)
                                    <tr>
                                        <td>{{ $branche['name'] }}</td>
                                        @if (!empty($branche->zones) && count(json_decode($branche->zones)) > 0)
                                            <td>
                                                @php
                                                    $zoneIDs = json_decode($branche->zones, true);
                                                    $zoneNames = collect($zoneIDs)->map(
                                                        fn($id) => $zones[$id] ?? 'Zona desconocida',
                                                    );
                                                @endphp
                                                @if ($zoneNames->count() && $branche['status'] === 1)
                                                    <button type="button"
                                                        class="btn btn-floating btn-sm btn-info btn-details"
                                                        data-zone-names='@json($zoneNames)'
                                                        data-bs-toggle="modal" data-bs-target="#zonasModal"
                                                        data-updated-at="{{ \Carbon\Carbon::parse($branche->updated_at)->format('Y-m-d h:i A') }}">
                                                        <i class="fas fa-eye" title="Ver Zonas"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        @endif
                                        <td>{{ $branche['user']->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($branche->created_at)->format('Y-m-d h:i A') }}</td>
                                        <td>
                                            @if ($branche['status'] === 1)
                                                <span class="badge rounded-pill badge-success">Activo</span>
                                            @else
                                                <span class="badge rounded-pill badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-floating btn-sm btn-warning btn-edit"
                                                        href="{{ route('regiones.edit', $branche['id']) }}">
                                                        <i class="fas fa-edit" title="Editar"></i>
                                                    </a>
                                                    @if ($branche['status'] === 1)
                                                        <form id="inactiveForm-{{ $branche['id'] }}"
                                                            action="{{ route('regiones.destroy', $branche['id']) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-floating btn-sm btn-danger inactive-btn"
                                                                onclick="confirmInactive(event, {{ $branche['id'] }})"
                                                                title="Inactivar">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form id="activeForm-{{ $branche['id'] }}"
                                                            action="{{ route('regiones.destroy', $branche['id']) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                class="btn btn-floating btn-sm btn-primary active-btn"
                                                                onclick="confirmActive(event, {{ $branche['id'] }})"
                                                                title="Activar">
                                                                <i class="fas fa-check-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @else
                                                <i class="text-muted">No disponible</i>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para ver las zonas -->
        <div class="modal fade" id="zonasModal" tabindex="-1" aria-labelledby="zonasModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Zonas asignadas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
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

        <!-- Modal de creación -->
        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="nuevoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nuevoModalLabel">Agregar Nueva Región/Sucursal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('regiones.store') }}" id="nuevoForm">
                            @csrf
                            <div class="mb-5">
                                <div class="form-outline" data-mdb-input-init>
                                    <input type="text" id="name" class="form-control" name="name" />
                                    <label class="form-label" for="name">Nombre</label>
                                </div>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="opciones" class="form-label">Zonas vinculadas:</label>
                                <select class="form-select select2" id="zones" name="zones[]" multiple required>
                                    @foreach ($zones as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-rounded btn-sm"
                                    data-bs-dismiss="modal">Cancelar
                                    <i class="fas fa-times-circle ml-2 fa-lg ml-2"></i>
                                </button>
                                <button type="submit" class="btn btn-success btn-rounded btn-sm" id="btnGuardar">
                                    Guardar
                                    <i class="fas fa-check-circle ml-2 fa-lg ml-2"></i>
                                </button>

                            </div>
                        </form>
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
            <b>Versión</b> Demo
        </div>
    </div>
@endsection


@section('css')
    <!-- MDB CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.0/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
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


        .select2-container--default .select2-results__option {
            color: #000 !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: #000 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000 !important;
        }
    </style>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.2/mdb.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/branches.js') }}"></script>
@stop
