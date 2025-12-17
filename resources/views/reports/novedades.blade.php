<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Novedades</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        header .logo {
            width: 120px;
        }

        header .titulo {
            text-align: center;
            flex-grow: 1;
            font-size: 18px;
            font-weight: bold;
        }

        .fechas {
            margin-bottom: 20px;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        table,
        th,
        td {
            border: 1px solid #444;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }
    </style>
</head>

<body>
    <header>
        {{-- Logo de la empresa --}}
        <div>
            <img src="{{ public_path('img/login.png') }}" alt="Logo" class="logo">
        </div>
        {{-- Título centrado --}}
        <div class="titulo">
            Reporte de Novedades
        </div>
    </header>

    <div class="fechas">
        <p><strong>Generado:</strong> {{ $fecha_actual }}</p>
        <p><strong>Desde:</strong> {{ $fecha_desde }} &nbsp; | &nbsp; <strong>Hasta:</strong> {{ $fecha_hasta }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Usuario que registra</th>
                <th>Novedad</th>
                <th>Personal que releva</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($novedades as $novedad)
                <tr>
                    <td>{{ $novedad->usuario_registra ?? 'Sin usuario' }}</td>
                    <td>{{ $novedad->observation ?? '-' }}</td>
                    <td>{{ $novedad->usuario_releva ?? 'Sin usuario' }}</td>
                    <td>{{ \Carbon\Carbon::parse($novedad->created_at)->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align:center;">No hay novedades en este rango de fechas</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>
