@extends('adminlte::page')

@section('title', 'Mi perfil')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-user-circle mr-2"></i>Mi perfil</h1>
    <br>
    Consulta y actualiza tu información personal para mantener tus datos siempre al día.
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

        <div class="col-md-12">
            <div class="card mdb-container">
                <div class="card-body">
                    <form method="POST" id="update-data">
                        @csrf
                        @method('PUT')

                        <input type="hidden" id="edit_user_id" name="user_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="text" id="edit_names" class="form-control" name="names"
                                        value="{{ $user->name }}" />
                                    <label class="form-label" for="edit_names">Nombres Completos</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="number" id="edit_document" class="form-control" name="document"
                                        value="{{ $user->document }}" />
                                    <label class="form-label" for="edit_document">Número de Documento</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <input type="text" id="edit_birthdate" class="form-control" name="birthdate"
                                    value="{{ $user->birthdate }}" placeholder="Fecha de nacimiento">
                            </div>

                            <div class="col-md-6 mb-3">
                                <select class="form-select" id="edit_gender" name="gender">
                                    <option value="" disabled selected>Selecciona el género</option>
                                    <option value="Masculino" {{ $user->gender === 'Masculino' ? 'selected' : '' }}>
                                        Masculino</option>
                                    <option value="Femenino" {{ $user->gender === 'Femenino' ? 'selected' : '' }}>Femenino
                                    </option>
                                    <option value="Otro" {{ $user->gender === 'Otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="tel" id="edit_phone" class="form-control" name="phone"
                                        value="{{ $user->phone }}" />
                                    <label class="form-label" for="edit_phone">Número de Celular</label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                @if ($user->status === 1)
                                    <i class="mr-2">Estado:</i> <span
                                        class="badge rounded-pill badge-success mt-2">Activo</span>
                                @else
                                    <i class="mr-2">Estado:</i> <span
                                        class="badge rounded-pill badge-danger mt-2">Inactivo</span>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-outline">
                                    <input type="password" id="edit_password" class="form-control" name="password"
                                        placeholder="••••••••" />
                                    <label class="form-label" for="edit_password">Nueva contraseña (Opcional)</label>
                                </div>

                                <small id="passwordHelp" class="form-text text-muted mt-1"></small>
                            </div>

                        </div>

                        <div class="modal-footer mt-3">
                            <a class="btn btn-secondary btn-rounded btn-sm mr-2" href="{{ route('home') }}"
                                data-dismiss="modal">
                                <i class="fas fa-arrow-circle-left mr-2 fa-lg"></i> Ir a panel
                            </a>
                            <button type="submit" id="submitBtn" class="btn btn-warning btn-rounded btn-sm">
                                Actualizar <i class="fas fa-check-circle ml-2 fa-lg"></i>
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
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pass = document.getElementById("edit_password");
            const help = document.getElementById("passwordHelp");

            // Regex unificada 👇 (incluye el # y todos los símbolos permitidos)
            const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._\-#]).{8,}$/;

            if (pass) {
                pass.addEventListener("input", function() {
                    const value = pass.value;

                    // Si está vacío → limpiar mensaje
                    if (value.trim() === "") {
                        help.innerHTML = "";
                        pass.classList.remove("is-invalid");
                        return;
                    }

                    // Mostrar requisitos
                    help.innerHTML = `
                    <span class="text-muted">
                        La contraseña debe incluir:
                        <br>• Mínimo 8 caracteres
                        <br>• Una letra mayúscula
                        <br>• Una letra minúscula
                        <br>• Un número
                        <br>• Un símbolo (@$!%*?&._-#)
                    </span>
                `;

                    // Validación en tiempo real
                    if (!strongRegex.test(value)) {
                        pass.classList.add("is-invalid");
                    } else {
                        pass.classList.remove("is-invalid");
                    }
                });
            }
        });

        $(function() {
            $('#edit_birthdate').datepicker({
                format: 'yyyy-mm-dd',
                language: 'es',
                autoclose: true,
                todayHighlight: true,
                orientation: "bottom auto"
            });
        });

        // 💠 Validación del formulario
        document.getElementById("update-data").addEventListener("submit", function(e) {
            e.preventDefault();

            const campos = [
                "edit_names",
                "edit_document",
                "edit_birthdate",
                "edit_gender",
                "edit_phone"
            ];

            let hayVacios = false;

            campos.forEach(id => {
                const input = document.getElementById(id);
                input.classList.remove("is-invalid");

                if (!input.value || input.value.trim() === "" || input.value === "Selecciona el género") {
                    hayVacios = true;
                    input.classList.add("is-invalid");
                }
            });

            // validar password si el usuario la escribió
            const pass = document.getElementById("edit_password");
            const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&._\-#]).{8,}$/; // misma regex

            if (pass && pass.value.trim() !== "") {

                if (!strongRegex.test(pass.value)) {
                    toastr.error("La contraseña no cumple los requisitos mínimos.");
                    pass.classList.add("is-invalid");
                    return;
                }
            }

            if (hayVacios) {
                toastr.error("Por favor, completa todos los campos");
                return;
            }

            Swal.fire({
                title: '¿Estás seguro?',
                text: "Revisa bien tu información personal.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#e2eaf7",
                confirmButtonText: "Sí, Actualizar",
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: "btn btn-success btn-rounded",
                    cancelButton: "btn btn-secondary btn-rounded shadow",
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    toastr.success("Datos validados correctamente");
                    document.getElementById("update-data").submit();
                }
            });
        });
    </script>
@stop
