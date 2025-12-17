<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $eventos = [
                [
                    'title' => 'Evento de prueba',
                    'start' => Carbon::now()->toDateString(),
                    'end' => Carbon::now()->addDays(1)->toDateString(),
                    'backgroundColor' => '#007bff',
                    'borderColor' => '#007bff'
                ]
            ];

            return response()->json($eventos);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
