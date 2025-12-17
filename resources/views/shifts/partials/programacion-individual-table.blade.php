@php
    use Carbon\Carbon;

    $hoy = Carbon::now();
    $fechaFinal = Carbon::parse($shift->day_until);
@endphp

@if ($hoy->lte($fechaFinal))
    <div class="alert alert-warning" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <span>Esta programación solo podrá ser editada hasta el día
            <strong>{{ $fechaFinal->format('d/m/Y') }}</strong></span>
    </div>
@else
    <div class="alert alert-danger" role="alert">
        <i class="fas fa-ban mr-2"></i>
        <span>La programación culminó el día <strong>{{ $fechaFinal->format('d/m/Y') }}</strong> y por lo tanto ya
            no puede ser editada.</span>
    </div>
@endif
<table class="table table-sm table-bordered text-center">
    <thead class="bg-blue text-dark">
        <tr>
            <th>Foto</th>
            <th>Cédula</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($personals as $personal)
            @php
                $photoPath = asset('img/user.png');
    
                if ($personal && !empty($personal->photo)) {
                    $storageFile = storage_path('app/public/' . $personal->photo);
                    if (file_exists($storageFile)) {
                        $photoPath = asset('storage/' . $personal->photo);
                    }
                }
            @endphp
    
            <tr>
                <td class="text-center">
                    <img src="{{ $photoPath }}"
                        alt="Foto de {{ $personal->name ?? 'Personal sin nombre' }}"
                        class="rounded-circle"
                        style="width: 60px; height: 60px; object-fit: cover;">
                </td>
                <td class="text-primary">{{ $personal->document ?? 'Sin documento' }}</td>
                <td>{{ ($personal->name ?? 'Sin nombre') . ' ' . ($personal->lastname ?? '') }}</td>
                <td>{{ $personal->phone ?? 'No disponible' }}</td>
                <td class="text-center">
                    @if ($personal)
                        <a href="{{ route('personal.detalles', ['id' => $personal->id]) }}"
                            class="btn btn-primary btn-rounded btn-sm">
                            <i class="fas fa-calendar-alt me-2"></i> Ver Detalles
                        </a>
                    @else
                        <span class="text-muted">Sin datos</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted py-4">
                    No hay personal operativo registrado.
                </td>
            </tr>
        @endforelse
    </tbody>

</table>

<!-- Modal para ver los turnos del personal -->
<div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="calendarModalLabel">Turnos del trabajador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div id="calendar"></div>
            </div>

        </div>
    </div>
</div>
