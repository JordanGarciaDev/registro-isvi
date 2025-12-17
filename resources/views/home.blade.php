@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')
    <h1><i class="fas fa-home mr-2"></i>Panel Informativo</h1>
@stop

@section('content')

    <br>
    <div class="row">
        <div class="col-md-4 col-sm-12 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ count($schedules) }}</h3>
                    <p><b>Programaciones totales</b></p>
                </div>
                <div class="icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <a href="{{ route('turnos.getShifts') }}" class="small-box-footer">Más información <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ count($workers) }}</h3>
                    <p><b>Trabajadores totales</b></p>
                </div>
                <div class="icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <a href="{{ route('personal.index') }}" class="small-box-footer">Más información <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
            <div class="col-md-4 col-sm-12 col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ count($users) }}</h3>
                        <p><b>Usuarios Totales</b></p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <a href="{{ route('usuarios.index') }}" class="small-box-footer">Más información <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        @endif
    </div>
    <br>
    <div class="row">
        @if (Auth::user()->hasRole('Administrador') || Auth::user()->hasRole('Jefe_operacion'))
            <!-- Gráfico de dona -->
            <div class="col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-pie mr-2"></i>
                            <h3 class="card-title mb-0">Roles totales de usuarios (Dona)</h3>
                        </div>
                        <div class="card-tools d-flex align-items-center ms-auto">
                            <button type="button" class="btn btn-secondary btn-sm mr-2" data-card-widget="collapse">
                                <i class="fas fa-minus fa-sm"></i>
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" data-card-widget="remove">
                                <i class="fas fa-times fa-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart2"></canvas>
                    </div>
                </div>
            </div>
        @endif

        <!-- Gráfico de barras -->
        <div class="col-md-6 col-sm-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        <h3 class="card-title mb-0">Estados de las novedades (Barras)</h3>
                    </div>
                    <div class="card-tools d-flex align-items-center ms-auto">
                        <button type="button" class="btn btn-secondary btn-sm mr-2" data-card-widget="collapse">
                            <i class="fas fa-minus fa-sm"></i>
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" data-card-widget="remove">
                            <i class="fas fa-times fa-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="myChart3"></canvas>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <br>
@stop

@section('footer')
    <div class="text-footer">
        <strong>Copyright &copy; {{ date('Y') }}
            <a href="#" class="text-blue">ISVI Ltda.</a>
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
    <style>
        @media (max-width: 576px) {
            .small-box .inner h3 {
                font-size: 18px;
            }

            .small-box .inner p {
                font-size: 14px;
            }

            .small-box {
                padding: 10px;
            }

            .small-box-footer {
                font-size: 14px;
            }

            .small-box .icon {
                font-size: 40px;
                top: 10px;
                right: 10px;
            }
        }

        .small-box .icon {
            font-size: 60px;
            position: absolute;
            top: 15px;
            right: 10px;
            z-index: 0;
            opacity: 0.3;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var canvas2 = document.getElementById("myChart2");
            if (canvas2) {
                var ctx2 = canvas2.getContext("2d");

                var totalAdministradores = {{ $totalAdministradores }};
                var totalJefesOperadores = {{ $totalJefesOperadores }};
                var totalOMT = {{ $totalOMT }};

                new Chart(ctx2, {
                    type: "doughnut",
                    data: {
                        labels: ["Administradores", "Jefes Operacion", "OMT"],
                        datasets: [{
                            label: "Usuarios por Rol",
                            data: [totalAdministradores, totalJefesOperadores, totalOMT],
                            backgroundColor: [
                                "rgba(255, 99, 132, 0.7)",
                                "rgba(255, 206, 86, 0.7)",
                                "rgba(54, 162, 235, 0.7)",
                            ],
                            borderColor: "white",
                            borderWidth: 2,
                        }, ],
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: "bottom",
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return context.label + ": " + context.parsed + " usuarios";
                                    },
                                },
                            },
                        },
                    },
                });
            } else {
                console.error("El canvas 'myChart2' no existe en el DOM.");
            }
        });

        // Tercer gráfico: BARRAS
        var canvas3 = document.getElementById("myChart3");
        if (canvas3) {

            var ctx3 = canvas3.getContext("2d");
            var observacionesPendientes = {{ $observacionesPendientes }};
            var observacionesAprobadas = {{ $observacionesAprobadas }};
            var observacionesRechazadas = {{ $observacionesRechazadas }};

            new Chart(ctx3, {
                type: "bar",
                data: {
                    labels: ["Pendientes", "Aprobadas", "Rechazadas"],
                    datasets: [{
                        label: "Total de novedades",
                        data: [observacionesPendientes, observacionesAprobadas,
                            observacionesRechazadas
                        ], // 🔹 Aquí luego pones tus datos reales
                        backgroundColor: [
                            "rgba(54, 162, 235, 0.7)", // Azul (Pendientes)
                            "rgba(75, 192, 192, 0.7)", // Verde (Aprobadas)
                            "rgba(255, 99, 132, 0.7)", // Rojo (Rechazadas)
                        ],
                        borderColor: [
                            "rgba(54, 162, 235, 1)",
                            "rgba(75, 192, 192, 1)",
                            "rgba(255, 99, 132, 1)",
                        ],
                        borderWidth: 2,
                        borderRadius: 5,
                    }, ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        title: {
                            display: true,
                            text: "Novedades por estado",
                            font: {
                                size: 14
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: "Número total de novedades",
                            },
                        },
                        x: {
                            title: {
                                display: true,
                                text: "Estados",
                            },
                        },
                    },
                },
            });
        } else {
            console.error("El canvas 'myChart3' no existe en el DOM.");
        }
    </script>
@stop
