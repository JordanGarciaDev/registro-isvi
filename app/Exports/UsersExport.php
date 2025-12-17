<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return User::all();
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->document,
            $user->birthdate,
            $user->gender,
            $user->phone,
            $user->email,
            $user->getRoleNames()->implode(', '),
            $user->status ? 'Activo' : 'Inactivo',
            $user->created_at->format('Y-m-d H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'Nombres',
            'Documento',
            'Fecha Nacimiento',
            'Género',
            'Celular',
            'Correo electronico',
            'rol',
            'Estado',
            'Fecha de Creación',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $status = $sheet->getCell('H' . $row)->getValue(); // Columna H = Estado
            if ($status === 'Activo') {
                $sheet->getStyle('H' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('28A745'); // verde
            } else {
                $sheet->getStyle('H' . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('DC3545'); // rojo
            }
        }
    }
}
