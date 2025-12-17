@extends('adminlte::page')

@section('title', 'Personal Operativo')

@section('content_header')
    <h1 class="mdb-container">Registro de Personal operativo</h1>
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
            <form action="{{ route('personal.update', $worker['id']) }}" method="POST" id="form-edit"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <a class="btn btn-outline-primary btn-rounded" data-mdb-ripple-init data-mdb-ripple-color="dark"
                    href="{{ route('personal.index') }}">
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
                            <img id="previewImage"
                                src="{{ $worker->photo ? asset('storage/' . $worker->photo) : asset('img/user.png') }}"
                                alt="Foto" class="img-fluid rounded-circle mb-2 mx-auto d-block mb-3"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="d-flex justify-content-center">
                                <label for="photoInput"
                                    class="btn btn-info btn-rounded d-flex align-items-center justify-content-center mx-1">
                                    <i class="fas fa-upload me-2"></i> Subir
                                </label>

                                <button id="clearPhoto" type="button"
                                    class="btn btn-danger btn-floating d-flex align-items-center justify-content-center mx-1"
                                    title="Limpiar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                            <span class="text-info" style="font-size: 13px;">Formatos permitidos: png, jpg y jpeg.</span>

                            <input type="file" id="photoInput" accept="image/jpeg, image/png" style="display: none;"
                                name="photo">
                        </div>
                    </div>
                    <div class="col-md-8 col-sm-12 col-xs-12 mt-3">
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="name" class="form-control" name="name"
                                        value="{{ $worker['name'] }}" />
                                    <label class="form-label" for="name">Nombres</label>
                                </div>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="lastname" class="form-control" name="lastname"
                                        value="{{ $worker['lastname'] }}" />
                                    <label class="form-label" for="lastname">Apellidos</label>
                                </div>
                                @error('lastname')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="number" id="document" class="form-control" name="document"
                                        value="{{ $worker['document'] }}" />
                                    <label class="form-label" for="document">Documento</label>
                                </div>
                                @error('document')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="phone" class="form-control" name="phone"
                                        value="{{ $worker['phone'] }}" />
                                    <label class="form-label" for="phone">Teléfono</label>
                                </div>
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 col-sm-12 mb-3">
                                <select class="form-select mb-3" id="bonding" name="bonding">
                                    <option value="" disabled selected>Seleccione el Tipo de vinculación</option>
                                    <option value="Fijo" {{ $worker['bonding'] === 'Fijo' ? 'selected' : '' }}>Fijo
                                    </option>
                                    <option value="Disponible" {{ $worker['bonding'] === 'Disponible' ? 'selected' : '' }}>
                                        Disponible</option>
                                    <option value="Disponible_dia_labor"
                                        {{ $worker['bonding'] === 'Disponible_dia_labor' ? 'selected' : '' }}>Disponible
                                        día labor</option>
                                </select>
                                @error('bonding')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 col-sm-12">
                                <div class="form-outline mb-3" data-mdb-input-init>
                                    <input type="text" id="cost_center" value="{{ $worker['cost_center'] }}"
                                        class="form-control" name="cost_center" />
                                    <label class="form-label" for="cost_center">Centro de costos</label>
                                </div>
                                @error('cost_center')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <select name="cargo" id="cargo" class="form-control" required>
                                    <option value="">Seleccione un cargo para el trabajador</option>
                                    <option value="Vigilante" {{ $worker['cargo'] === 'Vigilante' ? 'selected' : '' }}>Vigilante</option>
                                    <option value="Escolta" {{ $worker['cargo'] === 'Escolta' ? 'selected' : '' }}>Escolta</option>
                                    <option value="OMT" {{ $worker['cargo'] === 'OMT' ? 'selected' : '' }}>OMT</option>
                                    <option value="Supervisor" {{ $worker['cargo'] === 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                    <option value="Coordinador" {{ $worker['cargo'] === 'Coordinador' ? 'selected' : '' }}>Coordinador</option>
                                    <option value="Analista" {{ $worker['cargo'] === 'Analista' ? 'selected' : '' }}>Analista</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                @if ($worker['status'] === 1)
                                    <strong>Estado: </strong> <span
                                        class="badge rounded-pill badge-success fs-7 mt-3">Activo</span>
                                @elseif ($worker['status'] === 0)
                                    <strong>Estado: </strong> <span
                                        class="badge rounded-pill badge-danger fs-7 mt-3">Inactivo</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle mr-2"></i>
                            Ten en cuenta de validar de que la información ingresada del personal operativo sea la correcta.
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
            <b>Versión</b> {{ env('APP_VERSION') }}Demo
        </div>
    </div>
@endsection


@section('css')
    <!-- MDB CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.0/mdb.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
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

    <!-- scripts personalizados -->
    <script>
        var defaultImage = "{{ asset('img/user.png') }}";
    </script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/workersEdit.js') }}"></script>
@stop
