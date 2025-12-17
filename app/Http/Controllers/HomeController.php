<?php

namespace App\Http\Controllers;

use App\Models\Observation;
use App\Models\Schedule;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Worker;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $schedules = Schedule::where('status', 1)->get();
        $workers = Worker::where('status', 1)->get();
        $users = User::where('status', 1)->get();

        $totalAdministradores = User::role('Administrador')->where('status', 1)->count();
        $totalJefesOperadores = User::role('Jefe Operacion')->where('status', 1)->count();
        $totalOMT = User::role('OMT')->where('status', 1)->count();

        $observacionesPendientes = Observation::where('status', 1)->count();
        $observacionesRechazadas = Observation::where('status', 0)->count();
        $observacionesAprobadas = Observation::where('status', 2)->count();

        return view('home', compact(
            'schedules',
            'workers',
            'users',
            'totalAdministradores',
            'totalJefesOperadores',
            'totalOMT',
            'observacionesPendientes',
            'observacionesRechazadas',
            'observacionesAprobadas'
        ));
    }
}
