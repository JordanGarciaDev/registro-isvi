<?php

namespace App\Exports;

use App\Models\Zone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ZonasExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Zone::all(); 
    }

    public function map($zona): array
    {
        return [
            $zona->name,
            $zona->coordinates,
            $zona->address,
            $zona->phone,
            $zona->email,
            $zona->descriptions,
            $zona->created_at->format('Y-m-d H:i'), 
            $zona->status ? 'Activo' : 'Inactivo',
        ];
    }

    public function headings(): array
    {
        return [
            'Nombres',
            'Coordenadas',
            'Dirección',
            'Teléfono',
            'Correo electronico',
            'Descripción',
            'Fecha de Creación',
            'Estado',
        ];
    }
}
