@extends('adminlte::page')

@section('title', 'Regiones')

@section('content_header')
    <h1 class="mdb-container"><i class="fas fa-map mr-2"></i>Regiones/Sucursales</h1>
@stop

@section('content')
    <br>
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
        <div class="col-md-12 d-flex justify-content-between mb-3">
            <div class="d-flex gap-2">
                {{--  --}}
            </div>
            <div>
                <form method="POST" action="{{ route('regiones.update', $branche['id']) }}" id="formEdit">
                    @csrf
                    @method('PUT')
                    <a class="btn btn-outline-primary btn-rounded" href="{{ route('regiones.index') }}" data-mdb-ripple-init
                        data-mdb-ripple-color="dark">
                        <i class="fas fa-chevron-circle-left mr-2"></i>Regresar
                    </a>
                    <button type="submit" class="btn btn-success btn-rounded">Guardar
                        <i class="fas fa-check-circle ml-2 fa-lg ml-2"></i>
                    </button>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mdb-container">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-sm-12">
                            <div class="form-outline mb-3" data-mdb-input-init>
                                <input type="text" id="name" class="form-control" name="name"
                                    value="{{ $branche['name'] }}" />
                                <label class="form-label" for="names">Nombres Completos</label>
                            </div>
                        </div>

                        <div class="col-md-8 col-sm-12">
                            <div class="mb-3">
                                <select class="form-select select2" id="zones" name="zones[]" multiple required>
                                    @foreach ($zones as $id => $name)
                                        <option value="{{ $id }}"
                                            @if (in_array($id, json_decode($branche->zones))) selected @endif>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
            <b>Versión</b> Demo
        </div>
    </div>
@endsection


@section('css')
    <!-- MDB CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.3.0/mdb.min.css" rel="stylesheet">
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
    <script src="{{ asset('js/branchesEdit.js') }}"></script>
@stop
