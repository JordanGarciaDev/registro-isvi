@extends('adminlte::page')

@section('title', 'Parametrizacion')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-cogs mr-2"></i>Parametrización</h1>
    <br>
    <p>En esta parte puedes establecer los parametros necesarios para armar el turnos de las programaciones en el modulo
        correspondiente.</p>
@stop

@section('content')
    <div class="row justify-content-center">
        <br>

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

        <div class="col-md-12 mb-3 text-end">
            <a class="btn btn-primary btn-rounded btn-sm text-right" data-mdb-ripple-init data-bs-toggle="modal"
                data-bs-target="#ModalCreate">
                Establecer nueva parametrización <i class="fas fa-plus-circle ml-2"></i>
            </a>
        </div>
        <div class="col-md-12">
            <div class="card mdb-container">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-hover align-middle text-center">
                            <thead class="bg-blue text-dark">
                                <tr>
                                    <th>Nombre quien registró</th>
                                    <th>Horas totales semanales</th>
                                    <th>Registro horas</th>
                                    <th>Fecha creación</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($parametrization as $item)
                                    <tr>
                                        <td>{{ $item['user_name'] }}</td>
                                        <td>{{ $item['n_horas_semanales'] }} Horas</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm btn-floating btn-show-hours"
                                                title="Registro de horas"
                                                data-inicio="{{ $item->rango_hora_inicio_nocturno }}"
                                                data-fin="{{ $item->rango_hora_final_nocturno }}">
                                                <i class="fas fa-clock"></i>
                                            </a>
                                        </td>
                                        <td>{{ $item['created_at']->format('Y-m-d') }}</td>
                                        <td>
                                            @if ($item['status'] === 1)
                                                <span class="badge rounded-pill badge-success">Activo</span>
                                            @else
                                                <span class="badge rounded-pill badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item['status'] === 1)
                                                <button type="button" class="btn btn-floating btn-sm btn-warning btn-edit"
                                                    data-id="{{ $item->id }}"
                                                    data-horas="{{ $item->n_horas_semanales }}"
                                                    data-inicio="{{ $item->rango_hora_inicio_nocturno }}"
                                                    data-fin="{{ $item->rango_hora_final_nocturno }}">
                                                    <i class="fas fa-edit" title="Editar"></i>
                                                </button>
                                            @else
                                                <span class="text-muted">No disponible</span>
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

        <!-- Modal -->
        <div class="modal fade" id="ModalCreate" tabindex="-1" aria-labelledby="ModalCreateLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('parametrizacion.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="ModalCreateLabel">Nueva Parametrización</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <!-- Número de horas semanales -->
                                <div class="col-md-6 mb-3">
                                    <label for="n_horas_semanales" class="form-label">Horas Semanales</label>
                                    <input type="number" name="n_horas_semanales" id="n_horas_semanales" placeholder="Hrs"
                                        class="form-control" required>
                                </div>

                                <!-- Inicio rango nocturno -->
                                <div class="col-md-6 mb-3">
                                    <label for="rango_hora_inicio_nocturno" class="form-label">Inicio Rango Nocturno</label>
                                    <input type="time" name="rango_hora_inicio_nocturno" id="rango_hora_inicio_nocturno"
                                        class="form-control" required>
                                </div>

                                <!-- Fin rango nocturno -->
                                <div class="col-md-6 mb-3">
                                    <label for="rango_hora_final_nocturno" class="form-label">Fin Rango Nocturno</label>
                                    <input type="time" name="rango_hora_final_nocturno" id="rango_hora_final_nocturno"
                                        class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-rounded"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success btn-rounded">Guardar Parametrización</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal de Horas Nocturnas -->
        <div class="modal fade" id="modalHorasNocturnas" tabindex="-1" aria-labelledby="modalHorasNocturnasLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="modalHorasNocturnasLabel">
                            <i class="fas fa-clock me-2"></i> Rangos de Horas
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-moon fa-2x text-primary mb-2"></i>
                            <h6 class="fw-bold">Horas Nocturnas</h6>
                            <p><strong>Inicio:</strong> <span id="horaInicioNocturna"></span></p>
                            <p><strong>Fin:</strong> <span id="horaFinNocturna"></span></p>
                        </div>
                        <hr>
                        <div>
                            <i class="fas fa-sun fa-2x text-warning mb-2"></i>
                            <h6 class="fw-bold">Horas Diurnas</h6>
                            <p><strong>Inicio:</strong> <span id="horaInicioDiurna"></span></p>
                            <p><strong>Fin:</strong> <span id="horaFinDiurna"></span></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-rounded"
                            data-bs-dismiss="modal">Cerrar</button>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('modalHorasNocturnas');
            if (!modalEl) return; // Si no existe ese modal, no ejecuta nada

            const modal = new bootstrap.Modal(modalEl);

            document.querySelectorAll('.btn-show-hours').forEach(button => {
                button.addEventListener('click', function() {
                    const inicioNocturno = this.getAttribute('data-inicio') || '09:00 PM';
                    const finNocturno = this.getAttribute('data-fin') || '06:00 AM';

                    document.getElementById('horaInicioNocturna').innerHTML = inicioNocturno;
                    document.getElementById('horaFinNocturna').innerHTML = finNocturno;

                    const diurnaInicio = calcularDiurnaInicio(finNocturno);
                    const diurnaFin = calcularDiurnaFin(inicioNocturno);

                    document.getElementById('horaInicioDiurna').innerHTML = diurnaInicio;
                    document.getElementById('horaFinDiurna').innerHTML = diurnaFin;

                    modal.show();
                });
            });

            function parseHora(hora12) {
                if (!hora12 || typeof hora12 !== 'string') {
                    // valor por defecto si no hay dato
                    return {
                        hours: 0,
                        minutes: 0
                    };
                }

                const parts = hora12.trim().split(' ');
                const time = parts[0];
                const modifier = parts[1] ? parts[1].toUpperCase() : 'AM'; 

                let [hours, minutes] = time.split(':').map(Number);

                if (modifier === 'PM' && hours < 12) hours += 12;
                if (modifier === 'AM' && hours === 12) hours = 0;

                return {
                    hours,
                    minutes
                };
            }


            function to12HourFormat(hours, minutes) {
                const suffix = hours >= 12 ? 'PM' : 'AM';
                const h12 = hours % 12 || 12;
                return `${h12.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')} ${suffix}`;
            }

            function calcularDiurnaInicio(horaFinNocturna) {
                const {
                    hours,
                    minutes
                } = parseHora(horaFinNocturna);
                let totalMin = hours * 60 + minutes + 1;
                totalMin %= 24 * 60;
                const h = Math.floor(totalMin / 60);
                const m = totalMin % 60;
                return to12HourFormat(h, m);
            }

            function calcularDiurnaFin(horaInicioNocturna) {
                const {
                    hours,
                    minutes
                } = parseHora(horaInicioNocturna);
                let totalMin = hours * 60 + minutes - 1;
                if (totalMin < 0) totalMin += 24 * 60;
                const h = Math.floor(totalMin / 60);
                const m = totalMin % 60;
                return to12HourFormat(h, m);
            }
        });

        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.dataset.id;
                let horas = this.dataset.horas;
                let inicio = this.dataset.inicio;
                let fin = this.dataset.fin;

                // Función que convierte "09:00 PM" → "21:00"
                function to24HourFormat(time12) {
                    if (!time12) return '';
                    const [time, modifier] = time12.trim().split(' ');
                    let [hours, minutes] = time.split(':').map(Number);
                    if (modifier?.toUpperCase() === 'PM' && hours < 12) hours += 12;
                    if (modifier?.toUpperCase() === 'AM' && hours === 12) hours = 0;
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
                }

                // Convertir si vienen en formato 12h
                const inicio24 = to24HourFormat(inicio);
                const fin24 = to24HourFormat(fin);

                // Llenar los inputs
                document.getElementById('n_horas_semanales').value = horas;
                document.getElementById('rango_hora_inicio_nocturno').value = inicio24;
                document.getElementById('rango_hora_final_nocturno').value = fin24;

                // Cambiar el action del form
                let form = document.querySelector('#ModalCreate form');
                form.action = "/parametrizacion/" + id;
                let methodInput = document.createElement("input");
                methodInput.type = "hidden";
                methodInput.name = "_method";
                methodInput.value = "PUT";
                form.appendChild(methodInput);

                new bootstrap.Modal(document.getElementById('ModalCreate')).show();
            });
        });
    </script>
@stop
