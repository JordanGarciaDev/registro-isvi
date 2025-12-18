@extends('adminlte::page')

@section('title', 'Zonas o puestos')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-map-pin mr-2"></i>Zonas / Puestos</h1>
    <br>
@stop

@section('content')
    <div class="row justify-content-center">
        <br>
        <div class="col-md-12 d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                {{--  --}}
            </div>
            <div class="d-flex align-items-center gap-2">
                @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
                    <button id="exportButton" class="btn btn-success btn-rounded btn-sm">
                        Descargar Excel <i class="fas fa-file-excel ml-2"></i>
                    </button>

                    <a href="{{ route('zonas.create') }}" class="btn btn-primary btn-rounded btn-sm">
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
                        <table id="datatable" class="table table-hover align-middle text-center">
                            <thead class="bg-blue text-dark">
                                <tr>
                                    <th>Logo</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Personal asignado</th>
                                    <th>Dirección</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($zones as $zone)
                                    <tr>
                                        <td>
                                            <img src="{{ $zone->logo ? asset('storage/' . $zone->logo) : asset('img/logoo.png') }}"
                                                alt="Logo de {{ $zone->name }}" width="70" height="70"
                                                style="object-fit: contain; border-radius: 8px;">
                                        </td>
                                        <td><span class="badge rounded-pill badge-primary">{{ $zone['id_customer'] }}</span>
                                        </td>
                                        <td>{{ $zone['name'] }}</td>
                                        @php
                                            $ids = json_decode($zone->id_workers, true) ?? [];
                                            $zonePersonals = $personals->whereIn('id', $ids)->values();
                                        @endphp
                                        <td>
                                            <button type="button" class="btn btn-floating btn-sm btn-info btn-personal"
                                                data-bs-toggle="modal" data-bs-target="#zonePersonalsModal"
                                                data-personals='@json($zonePersonals)'>
                                                <i class="fas fa-eye" title="Ver Personal"></i>
                                            </button>
                                        </td>
                                        <td>{{ $zone['address'] }}</td>
                                        <td>
                                            @if ($zone['status'] === 1)
                                                <span class="badge rounded-pill badge-success">Activo</span>
                                            @else
                                                <span class="badge rounded-pill badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-floating btn-sm btn-secondary" type="button"
                                                    id="dropdownMenuButton{{ $zone['id'] }}" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-bars"></i>
                                                </button>
                                                <ul class="dropdown-menu"
                                                    aria-labelledby="dropdownMenuButton{{ $zone['id'] }}">
                                                    <li>
                                                        <a class="dropdown-item text-info btn-details d-inline-block"
                                                            style="cursor: pointer;" data-toggle="modal"
                                                            data-target="#zoneDetailsModal" data-id="{{ $zone['id'] }}"
                                                            data-id_customer="{{ $zone['id_customer'] }}"
                                                            data-name="{{ $zone['name'] }}"
                                                            data-schedule="{{ $zone['schedule']->name ?? 'Ninguna' }}"
                                                            data-address="{{ $zone['address'] }}"
                                                            data-phone="{{ $zone['phone'] }}"
                                                            data-email="{{ $zone['email'] }}"
                                                            data-is_shifts="{{ $zone['is_shifts'] }}"
                                                            data-region="{{ $zone['region'] }}"
                                                            data-descriptions="{{ $zone['descriptions'] }}"
                                                            data-user="{{ $zone['user']->name ?? 'No registrado' }}"
                                                            data-status="{{ $zone['status'] }}"
                                                            data-image="{{ asset('storage/' . $zone['photo']) }}"
                                                            data-created="{{ \Carbon\Carbon::parse($zone['created_at'])->translatedFormat('j \d\e F \d\e Y \a \l\a\s h:i A') }}"
                                                            data-salary="{{ $zone['salary'] }}"
                                                            data-income="{{ $zone['others_income'] }}"
                                                            data-contracts="{{ $zone['contract_bonus'] }}">
                                                            <i class="fas fa-eye"></i> Ver Detalles
                                                        </a>
                                                    </li>
                                                    @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
                                                    <li>
                                                        <a class="dropdown-item text-warning change-schedule-btn"
                                                            href="#" data-bs-toggle="modal"
                                                            data-bs-target="#changeScheduleModal"
                                                            data-id="{{ $zone['id'] }}"
                                                            data-current="{{ $zone['schedule']->name ?? 'Ninguna' }}"
                                                            data-current-id="{{ $zone['schedule']->id ?? '' }}">
                                                            <i class="fas fa-calendar-alt"></i> Cambiar Programación
                                                        </a>
                                                    </li>
                                                    <li>
                                                        @if ($zone['status'] === 1)
                                                            <form id="inactiveForm-{{ $zone['id'] }}"
                                                                action="{{ route('zonas.destroy', $zone['id']) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="dropdown-item text-danger"
                                                                    onclick="event.preventDefault(); confirmInactive(event, {{ $zone['id'] }})">
                                                                    <i class="fas fa-ban"></i> Inactivar
                                                                </a>
                                                            </form>
                                                        @else
                                                            <form id="activeForm-{{ $zone['id'] }}"
                                                                action="{{ route('zonas.destroy', $zone['id']) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <a href="#" class="dropdown-item text-success"
                                                                    onclick="event.preventDefault(); confirmActive(event, {{ $zone['id'] }})">
                                                                    <i class="fas fa-check-circle"></i> Activar
                                                                </a>
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
        </div>

        <!-- Modal para mostrar trabajadores -->
        <div class="modal fade" id="zonePersonalsModal" tabindex="-1" aria-labelledby="zonePersonalsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Trabajadores de la zona</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <ul id="personalsList" class="list-group">
                            <!-- Trabajadores se insertan aquí -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para Cambiar Programación -->
        <div class="modal fade" id="changeScheduleModal" tabindex="-1" aria-labelledby="changeScheduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <form id="changeScheduleForm" method="POST" action="">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="zone_id" id="zoneIdInput">

                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="changeScheduleModalLabel">Cambiar Programación</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="scheduleSelect" class="form-label">Nueva Programación</label>
                                <div class="dropdown">
                                    <input type="hidden" name="schedule_id" id="selectedScheduleId">

                                    <button class="btn btn-outline-primary dropdown-toggle w-100" type="button"
                                        id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                        Selecciona una programación
                                    </button>
                                    <ul class="dropdown-menu w-100" aria-labelledby="dropdownMenuButton">
                                        @foreach ($schedules as $schedule)
                                            <li>
                                                <a class="dropdown-item schedule-option" href="#"
                                                    data-id="{{ $schedule->id }}" data-name="{{ $schedule->name }}">
                                                    <div>
                                                        <strong>{{ $schedule->name }}</strong><br>
                                                        <span class="text-muted">
                                                            <strong>{{ \Carbon\Carbon::parse($schedule->day_since)->format('d/m/Y') }}</strong>
                                                            a
                                                            <strong>{{ \Carbon\Carbon::parse($schedule->day_until)->format('d/m/Y') }}</strong>
                                                        </span><br>
                                                        <span class="text-success">{{ $schedule->val1 }} x
                                                            {{ $schedule->val2 }}</span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-rounded">Guardar cambios
                                <i class="fas fa-check-circle ml-2 fa-lg ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Ver Detalles --}}
        <div class="modal fade" id="zoneDetailsModal" tabindex="-1" aria-labelledby="zoneDetailsLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="zoneDetailsLabel">Detalles de la Zona</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Cerrar">
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 d-flex flex-column align-items-center text-center">
                                <img id="zoneImage" src="" class="img-fluid rounded shadow mb-3"
                                    alt="Imagen de la zona"
                                    style="max-width: 100%; max-height: 300px; object-fit: cover;">

                                <div class="d-flex justify-content-center gap-2">
                                    <!-- Inactivo -->
                                    <button type="button" class="btn btn-floating btn-sm btn-danger delete-btn mb-3"
                                        title="Inactivar">
                                        <i class="fas fa-ban"></i>
                                    </button>

                                    <!-- Activo -->
                                    <button type="button" class="btn btn-floating btn-sm btn-primary delete-btn mb-3"
                                        title="Activar">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </div>
                                <div class="alert alert-warning d-flex align-items-center text-center mt-2"
                                    role="alert">
                                    <i class="fas fa-user me-2"></i>
                                    <p class="small m-0">Usuario que registró:
                                        <strong id="zoneUser"></strong>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6 small">
                                <h5 id="zoneName" class="fw-bold"></h5>
                                <br>
                                <p><strong>Código zona:</strong> <span class="text-primary" id="zoneIdCustomer"></span>
                                </p>
                                <p><strong>Dirección:</strong> <span id="zoneAddress"></span></p>
                                <p><strong>Teléfono:</strong> <span id="zonePhone"></span></p>
                                <p><strong>Email:</strong> <span id="zoneEmail"></span></p>
                                <p><strong>Programación:</strong> <span id="zoneSchedule"></span></p>
                                <p><strong>Región:</strong> <span id="zoneRegion"></span></p>
                                <p><strong>Salario:</strong> <span id="zoneSalary"></span></p>
                                <p><strong>Otros ingresos:</strong> <span id="zoneIncome"></span></p>
                                <p><strong>Bono x contrato:</strong> <span id="zoneContracts"></span></p>
                                <p><strong class="mr-2">Estado:</strong><span id="zoneStatus"></span></p>
                                <p><strong>Descripción:</strong> <span id="zoneDescriptions"></span></p>
                                <p><span id="zoneIsShifts" class="text-primary"></span></p>
                            </div>
                            <div class="col-md-12 small">
                                <div class="alert alert-info d-flex align-items-center text-center mt-2" role="alert">
                                    <i class="fas fa-file-signature me-2"></i>
                                    <p class="m-0">Fecha de registro:
                                        <strong id="zoneCreated"></strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div id="loadingOverlay"
        style="
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
">
        <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;"></div>
        <div style="color: white; font-size: 1.2rem; margin-top: 15px;">Generando Excel...</div>
    </div>
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

        #zoneImage {
            max-width: 100%;
            max-height: 300px;
            object-fit: cover;
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

    <!-- Tu archivo de script personalizado -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/zones.js') }}"></script>
    <script>
        document.querySelectorAll('.btn-personal').forEach(button => {
            button.addEventListener('click', () => {
                const personals = JSON.parse(button.getAttribute('data-personals'));
                console.log(personals)
                const list = document.getElementById('personalsList');
                list.innerHTML = '';

                if (personals.length === 0) {
                    list.innerHTML =
                        '<li class="list-group-item text-muted">No hay trabajadores asignados.</li>';
                } else {
                    personals.forEach(personal => {
                        list.innerHTML +=
                            `<li class="list-group-item">${personal.custom_name}</li>`;
                    });
                }
            });
        });

        const exportButton = document.getElementById('exportButton');
        const overlay = document.getElementById('loadingOverlay');

        exportButton.addEventListener('click', function() {
            overlay.style.display = 'flex';

            fetch("{{ route('zonas.export') }}", {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'zonas.xlsx';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);

                    overlay.style.display = 'none';
                })
                .catch(err => {
                    console.error(err);
                    overlay.style.display = 'none';
                    alert('Ocurrió un error al generar el Excel.');
                });
        });
    </script>
@stop
