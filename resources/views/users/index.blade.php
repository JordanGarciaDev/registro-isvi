@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-user mr-2"></i>Administración de usuarios</h1>
    <br>
@stop

@section('content')
    <div class="row justify-content-center">
        <br>
        <div class="col-md-12 text-right mb-3">
            <button type="submit" class="btn btn-success btn-rounded btn-sm" id="downloadExcel">
                Descargar Excel <i class="fas fa-file-excel ml-2"></i>
            </button>

            <a class="btn btn-primary btn-rounded btn-sm" data-mdb-ripple-init data-toggle="modal" data-target="#miModal"
                href="{{ route('usuarios.create') }}">
                Nuevo <i class="fas fa-plus-circle ml-2"></i>
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
                                    <th>Nombres</th>
                                    <th>Teléfono</th>
                                    <th>Correo electrónico</th>
                                    <th>Género</th>
                                    <th>Creación</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usuarios as $usuario)
                                    <tr>
                                        <td>{{ $usuario['name'] }}</td>
                                        <td>{{ $usuario['phone'] }}</td>
                                        <td>{{ $usuario['email'] }}</td>
                                        <td>{{ $usuario['gender'] }}</td>
                                        <td>{{ $usuario['created_at']->format('Y-m-d') }}</td>
                                        <td>
                                            @php
                                                $roles = $usuario->getRoleNames();
                                                $roleColor = 'badge-secondary';
                                                $roleName = 'Sin definir';

                                                if ($roles->isNotEmpty()) {
                                                    $roleName = $roles->first();
                                                    if ($roleName === 'Administrador') {
                                                        $roleColor = 'badge-danger';
                                                    } elseif ($roleName === 'OMT') {
                                                        $roleColor = 'badge-info';
                                                    } elseif ($roleName === 'Jefe Operacion') {
                                                        $roleColor = 'badge-warning';
                                                    } else {
                                                        $roleColor = 'badge-secondary';
                                                    }
                                                }
                                            @endphp
                                            <span class="badge rounded-pill {{ $roleColor }}">{{ $roleName }}</span>
                                        </td>
                                        <td>
                                            @if ($usuario['status'] === 1)
                                                <span class="badge rounded-pill badge-success">Activo</span>
                                            @else
                                                <span class="badge rounded-pill badge-danger">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($usuario['id'] === Auth::user()->id)
                                                <span class="badge rounded-pill badge-dark">Logueado</span>
                                            @else
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button"
                                                        class="btn btn-floating btn-sm btn-warning btn-edit"
                                                        data-id="{{ $usuario->id }}" data-name="{{ $usuario->name }}"
                                                        data-document="{{ $usuario->document }}"
                                                        data-birthdate="{{ $usuario->birthdate }}"
                                                        data-gender="{{ $usuario->gender }}"
                                                        data-phone="{{ $usuario->phone }}"
                                                        data-email="{{ $usuario->email }}"
                                                        data-role="{{ $usuario->roles->first()->name ?? '' }}"
                                                        data-toggle="modal" data-target="#editUserModal">
                                                        <i class="fas fa-edit" title="Editar"></i>
                                                    </button>
                                                    <form id="deleteForm-{{ $usuario['id'] }}"
                                                        action="{{ route('usuarios.destroy', $usuario['id']) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button"
                                                            class="btn btn-floating btn-sm btn-danger delete-btn"
                                                            onclick="confirmDelete(event, {{ $usuario['id'] }})"
                                                            title="Eliminar">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
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

        {{-- Modal Create Users --}}
        <div class="modal fade" id="miModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel">Crear Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('usuarios.store') }}" id="form-modal">
                        @csrf
                        <div class="modal-body">
                            <div class="form-outline mb-3" data-mdb-input-init>
                                <input type="text" id="names" class="form-control" name="names" />
                                <label class="form-label" for="names">Nombres Completos</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="number" id="document" class="form-control" name="document" />
                                <label class="form-label" for="document">Número de Documento</label>
                            </div>

                            <div class="mb-3">
                                <input type="text" id="fechaNacimiento" class="form-control" autocomplete="off"
                                    name="birthdate" placeholder="Selecciona la fecha de nacimiento">
                            </div>

                            <div class="mb-3">
                                <select class="form-select" id="genero" name="gender">
                                    <option value="" disabled selected>Selecciona el género</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="tel" id="phone" class="form-control" placeholder="+57"
                                    name="phone" />
                                <label class="form-label" for="phone">Número de Celular</label>
                            </div>

                            <!-- Email -->
                            <div class="form-outline mb-3">
                                <input type="email" id="email" class="form-control" name="email"
                                    placeholder="alguien@example.com" />
                                <label class="form-label" for="email">Email</label>
                            </div>

                            <!-- Verificación de Email -->
                            <div class="form-outline mb-3">
                                <input type="email" id="verify_email" class="form-control" name="verify_email" />
                                <label class="form-label" for="verify_email">Verificar Email</label>
                            </div>

                            <!-- Contraseña -->
                            <div class="form-outline mb-3">
                                <input type="password" id="password" class="form-control sm" name="password" />
                                <label class="form-label" for="password">Contraseña</label>
                            </div>

                            <!-- Rol -->
                            <div class="mb-3">
                                <select class="form-select" id="role" name="role_name">
                                    <option value="" disabled selected>Selecciona el rol</option>
                                    @foreach ($hasRoles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Cancelar
                                <i class="fas fa-times-circle ml-2 fa-lg ml-2"></i>
                            </button>
                            <button type="submit" class="btn btn-success btn-rounded">Guardar
                                <i class="fas fa-check-circle ml-2 fa-lg ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Editar Usuario --}}
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Editar Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" id="edit-form-modal">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <input type="hidden" id="edit_user_id" name="user_id">

                            <div class="form-outline mb-3">
                                <input type="text" id="edit_names" class="form-control" name="names" />
                                <label class="form-label" for="edit_names">Nombres Completos</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="number" id="edit_document" class="form-control" name="document" />
                                <label class="form-label" for="edit_document">Número de Documento</label>
                            </div>

                            <div class="mb-3">
                                <input type="text" id="edit_birthdate" class="form-control" name="birthdate"
                                    placeholder="Fecha de nacimiento">
                            </div>

                            <div class="mb-3">
                                <select class="form-select" id="edit_gender" name="gender">
                                    <option value="" disabled selected>Selecciona el género</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="tel" id="edit_phone" class="form-control" name="phone" />
                                <label class="form-label" for="edit_phone">Número de Celular</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="email" id="edit_email" class="form-control" name="email" />
                                <label class="form-label" for="edit_email">Email</label>
                            </div>

                            <div class="form-outline mb-3">
                                <input type="password" id="edit_password" class="form-control sm" name="password" />
                                <label class="form-label" for="password" required>Contraseña</label>
                            </div>

                            <div class="mb-3">
                                <select class="form-select" id="edit_role" name="role_name">
                                    <option value="" disabled selected>Selecciona el rol</option>
                                    @foreach ($hasRoles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-rounded" data-dismiss="modal">Cancelar
                                <i class="fas fa-times-circle ml-2 fa-lg ml-2"></i>
                            </button>
                            <button type="submit" id="submitBtn" class="btn btn-warning btn-rounded">Actualizar
                                <i class="fas fa-check-circle ml-2 fa-lg ml-2"></i>
                            </button>
                        </div>
                    </form>
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
    <script src="{{ asset('js/users.js') }}"></script>
    <script>
        const btn = document.getElementById('downloadExcel');
        const loading = document.getElementById('loadingOverlay');

        btn.addEventListener('click', () => {
            loading.style.display = 'flex';
            fetch("{{ route('usuarios.export') }}")
                .then(response => response.blob())
                .then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = "usuarios_export.xlsx";
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                    loading.style.display = 'none';
                })
                .catch(() => loading.style.display = 'none');
        });
    </script>
@stop
