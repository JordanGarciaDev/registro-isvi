@extends('adminlte::page')

@section('title', 'Zonas o puestos')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-map-pin mr-2"></i>Registrar zona o puesto</h1>
    <br>
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
        <div class="col-md-12 text-right mb-3">


            <form action="{{ route('zonas.store') }}" method="POST" id="form-create" enctype="multipart/form-data">
                @csrf
                <a class="btn btn-outline-primary btn-rounded" data-mdb-ripple-init data-mdb-ripple-color="dark"
                    href="{{ route('zonas.index') }}">
                    <i class="fas fa-chevron-circle-left mr-2"></i>Regresar
                </a>
                <button type="submit" class="btn btn-success btn-rounded" data-mdb-ripple-init>
                    Guardar datos <i class="fas fa-check-circle ml-2"></i>
                </button>
        </div>
        <div class="card mdb-container">
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-lg-4 col-sm-12 col-12 d-flex justify-content-center mt-3">
                        <div class="card p-3 text-center" style="width: 250px;">
                            <div>
                                <img id="previewImage" src="{{ asset('img/zone.png') }}" alt="Foto"
                                    class="img-fluid mb-2 mx-auto d-block mb-3 rounded"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                <div class="d-flex justify-content-center">
                                    <label for="photoInput"
                                        class="btn btn-info btn-rounded d-flex align-items-center justify-content-center mx-1">
                                        <i class="fas fa-upload me-2"></i> Ubicación
                                    </label>

                                    <button id="clearPhoto" type="button"
                                        class="btn btn-danger btn-floating d-flex align-items-center justify-content-center mx-1"
                                        title="Limpiar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <span class="text-info" style="font-size: 13px;">Formatos permitidos: png, jpg y
                                    jpeg.</span>

                                <input type="file" id="photoInput" accept="image/jpeg, image/png" style="display: none;"
                                    name="photo">
                            </div>
                            <div>
                                <img id="previewImageLogo" src="{{ asset('img/logoo.png') }}" alt="Foto"
                                    class="img-fluid mb-2 mx-auto d-block mb-3 rounded"
                                    style="width: 120px; height: 120px; object-fit: contain;">
                                <div class="d-flex justify-content-center">
                                    <label for="logoInput"
                                        class="btn btn-info btn-rounded d-flex align-items-center justify-content-center mx-1">
                                        <i class="fas fa-upload me-2"></i> Logo
                                    </label>

                                    <button id="clearPhotoLogo" type="button"
                                        class="btn btn-danger btn-floating d-flex align-items-center justify-content-center mx-1"
                                        title="Limpiar">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                                <span class="text-info" style="font-size: 13px;">Formatos permitidos: png, jpg y
                                    jpeg.</span>

                                <input type="file" id="logoInput" accept="image/jpeg, image/png" style="display: none;"
                                    name="logoInput">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12 col-xs-12 mt-3">
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <div class="input-group mb-3 align-items-stretch">
                                    <span class="input-group-text" style="height: 38px;">
                                        <i class="fas fa-key"></i>
                                    </span>
                                    <div class="form-outline flex-grow-1" data-mdb-input-init>
                                        <input type="number" id="id_customer" class="form-control" name="id_customer" />
                                        <label class="form-label" for="id_customer">Código cliente</label>
                                    </div>
                                </div>
                                @error('id_customer')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="name" class="form-control" name="name" />
                                    <label class="form-label" for="name">Nombre</label>
                                </div>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="coordinates" class="form-control" name="coordinates" />
                                    <label class="form-label" for="coordinates">Coordenadas</label>
                                </div>
                                @error('coordinates')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="address" class="form-control" name="address" />
                                    <label class="form-label" for="address">Dirección</label>
                                </div>
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="phone" class="form-control" name="phone" />
                                    <label class="form-label" for="phone">Teléfono</label>
                                </div>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="email" id="email" class="form-control" name="email" />
                                    <label class="form-label" for="email">Correo electrónico</label>
                                </div>
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="salary" class="form-control" name="salary"
                                        placeholder="COP" />
                                    <label class="form-label" for="salary">Salario</label>
                                </div>
                                @error('salary')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <select class="form-select mb-3 select2" id="n_workers" name="n_workers">
                                    <option value="" disabled selected>Número de trabajadores</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                </select>
                                @error('n_workers')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="others_income" class="form-control" name="others_income"
                                        placeholder="COP" />
                                    <label class="form-label" for="others_income">Otros ingresos</label>
                                </div>
                                @error('others_income')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="contract_bonus" class="form-control" name="contract_bonus"
                                        placeholder="COP" />
                                    <label class="form-label" for="contract_bonus">Bono por contrato</label>
                                </div>
                                @error('contract_bonus')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 col-sm-12 mb-3">
                                <select class="form-select mb-3 select2" id="personal_worker" name="personal_worker[]"
                                    multiple>
                                    @foreach ($personals as $personal)
                                        <option value="{{ $personal->id }}">{{ $personal->custom_name }}</option>
                                    @endforeach
                                </select>
                                @error('personal_worker')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 col-sm-12 mb-3">
                                <select class="form-select mb-3 select2" id="schedule" name="schedule">
                                    <option value="" disabled selected>Tipo de programación</option>
                                    @foreach ($schedules as $schedule)
                                        <option value="{{ $schedule->id }}">{{ $schedule->custom_name }}</option>
                                    @endforeach
                                </select>
                                @error('schedule')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-outline" data-mdb-input-init>
                                    <textarea class="form-control" id="description" name="description" rows="3" maxlength="100"
                                        placeholder="Máximo 100 caracteres..."></textarea>
                                    <label for="descripcion" class="form-label">Descripción</label>
                                </div>
                                <small id="contador" class="text-muted">0/100 caracteres</small>
                            </div>
                            {{-- <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="aplicaProgramacion"
                                        name="is_shifts" value="1" {{ old('is_shifts') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="aplicaProgramacion">
                                        Aplica programación de turnos
                                    </label>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            Ten en cuenta de validar de que la información ingresada de la zona/región sea la correcta.
                        </div>
                    </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    </div>
    <div class="col-md-12">

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <style>
        .form-switch .form-check-input:checked {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
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
    <script>
        var defaultImage = "{{ asset('img/zone.png') }}";
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/zonesCreate.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.getElementById("logoInput");
            const preview = document.getElementById("previewImageLogo");
            const clearBtn = document.getElementById("clearPhotoLogo");
            const defaultLogo = "{{ asset('img/logoo.png') }}";

            toastr.options = {
                showMethod: "show",
                hideMethod: "hide",
                showDuration: 250,
                hideDuration: 800,
                timeOut: 5000,
                closeButton: true,
                progressBar: true,
            };

            input.addEventListener("change", function() {
                const file = this.files[0];
                if (file) {
                    const validTypes = ["image/png", "image/jpeg", "image/jpg"];
                    if (!validTypes.includes(file.type)) {
                        toastr.error("Formato no permitido. Solo se aceptan PNG, JPG y JPEG.");
                        input.value = "";
                        preview.src = defaultLogo;
                        return;
                    } else {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            preview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);

                        toastr.success("Bien!!!, El logo ha sido cargado.")
                    }
                }
            });

            clearBtn.addEventListener("click", function() {
                input.value = "";
                preview.src = defaultLogo;
            });
        });
    </script>

@stop
