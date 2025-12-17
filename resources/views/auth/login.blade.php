@extends('layouts.app')

@section('content')
    <div id="particles-js"></div>
    <div class="container">
        <br><br>
        <div class="row justify-content-center">
            <div class="col-lg-4 col-sm-8 col-md-6">
                <div class="text-center">
                    <img src="{{ asset('img/login.png') }}" alt="Fondo" class="img-fluid" width="150">
                </div>
                <div class="card shadow-lg">
                    <div class="card-body">
                        <br>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="input-group">
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email"
                                    placeholder="Correo Electronico" autofocus>
                                <span class="input-group-text">
                                    <i class="material-icons">mail</i>
                                </span>
                            </div>
                            @error('email')
                                <div class="text-danger mb-3" style="font-size: 12px !important;">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="input-group mt-3">
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password" required
                                    placeholder="Contraseña" autocomplete="current-password">
                                <span class="input-group-text">
                                    <i class="material-icons">lock</i>
                                </span>
                            </div>
                            @error('password')
                                <div class="text-danger mb-3" style="font-size: 12px !important;">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-check mb-3 mt-3">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Recuerdame') }}
                                </label>
                            </div>
                            <br>
                            <div class="d-flex justify-content-center">
                                <button type="submit"
                                    class="btn btn-isvi d-inline-flex align-items-center justify-content-center w-auto">
                                    <span style="font-size: 13px !important;">ingresar</span>
                                    <span class="material-icons ms-2">login</span>
                                </button>
                            </div>

                            {{-- @if (Route::has('password.request'))
                                <div class="text-center mt-3">
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                </div>
                            @endif --}}
                        </form>
                    </div>
                    <div class="text-center">
                        <span style="font-size: 0.8rem; color: #6c757d;">ISVI Ltda.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow: hidden !important;
        }

        .btn-isvi {
            background-color: #56929f !important;
            color: black;
        }

        button {
            background: #0066FF !important;
        }

        .card {
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.5) !important;
        }

        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #00b09b;
            background: -webkit-linear-gradient(to right, #96c93d, #00b09b);
            background: linear-gradient(to right, #96c93d, #00b09b);
            z-index: -1;
        }

        img {
            max-width: 100% !important;
            height: auto !important;
            display: block !important;
            margin: 0 auto !important;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <script>
        window.onload = function() {
            particlesJS("particles-js", {
                particles: {
                    number: {
                        value: 120,
                        density: {
                            enable: true,
                            value_area: 800
                        }
                    },
                    color: {
                        value: "#ffffff"
                    },
                    shape: {
                        type: "edge"
                    },
                    opacity: {
                        value: 0.5,
                        random: true
                    },
                    size: {
                        value: 3,
                        random: true
                    },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: "#ffffff",
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        attract: {
                            enable: true,
                            rotateX: 600,
                            rotateY: 1200
                        }
                    }
                },
                interactivity: {
                    detect_on: "window",
                    events: {
                        onhover: {
                            enable: true,
                            mode: "grab"
                        },
                        onclick: {
                            enable: true,
                            mode: "repulse"
                        }
                    },
                    modes: {
                        grab: {
                            distance: 140,
                            line_linked: {
                                opacity: 1
                            }
                        },
                        push: {
                            particles_nb: 4
                        }
                    }
                },
                retina_detect: true
            });
        };
    </script>
@endsection
