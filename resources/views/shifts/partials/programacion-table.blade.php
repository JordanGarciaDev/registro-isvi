<div class="row">
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

    <div class="col-md-12 col-sm-12 mb-3">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <div class="d-flex align-items-center">
                <div
                    style="width: 20px; height: 20px; background-color: #c6e5d2; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-sun" style="font-size: 12px; color: #28a745;"></i>
                </div>
                <span style="color: #28a745;">Turno de Día</span>
            </div>
            <div class="d-flex align-items-center">
                <div
                    style="width: 20px; height: 20px; background-color: #d0e4ff; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-moon" style="font-size: 12px; color: #0056b3;"></i>
                </div>
                <span style="color: #0056b3;">Turno de Noche</span>
            </div>
            <div class="d-flex align-items-center">
                <div
                    style="width: 20px; height: 20px; background-color: #f8d7da; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-bed" style="font-size: 12px; color: #842029;"></i>
                </div>
                <span class="text-danger">Descanso</span>
            </div>
            <div class="d-flex align-items-center">
                <div
                    style="width: 20px; height: 20px; background-color: #fff3cd; border-radius: 4px; margin-right: 8px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-house-user" style="font-size: 12px; color: #856404;"></i>
                </div>
                <span class="text-warning">Domingo</span>
            </div>
        </div>
    </div>
</div>
<div style="overflow-x: auto; max-width: 100%;">
    <table id="modal-schedule-table"
        class="table table-sm table-bordered table-hover align-middle text-center w-100 mb-0"
        style="font-size: 0.85rem;">
        <thead class="bg-blue text-dark">
            <tr>
                <th>Nombre</th>
                <th>Cédula</th>
                <th>Estado</th>
                @foreach ($dates as $date)
                    @php $isSunday = $date->dayOfWeekIso == 7; @endphp
                    <th @if ($isSunday) style="background-color: #fff3cd; color: #856404;" @endif>
                        {{ $date->isoFormat('MMM DD') }}<br>
                        <small>
                            {{ strtoupper($date->isoFormat('ddd')) }}
                            @if ($isSunday)
                                <i class="fas fa-house-user ms-1"></i>
                            @endif
                        </small>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($personals as $index => $personal)
                @php
                    $val1 = $zoneInfo->schedule->val1;
                    $val2 = $zoneInfo->schedule->val2;
                    $scheduleType = "{$val1}x{$val2}";
                    $personaTurnos = [];

                    if ($scheduleType === '2x2') {
                        $turnos = ['Día', 'Noche', 'Descanso'];
                        $startTurnIndex = $index % 3;

                        for ($i = 0; count($personaTurnos) < count($dates); $i++) {
                            $currentTurn = $turnos[($startTurnIndex + $i) % 3];
                            $count = $currentTurn === 'Descanso' ? $val2 : $val1;
                            $personaTurnos = array_merge($personaTurnos, array_fill(0, $count, $currentTurn));
                        }

                        $personaTurnos = array_slice($personaTurnos, 0, count($dates));
                    } elseif ($scheduleType === '5x2') {
                        $startWithDay = $index % 2 === 0;
                        $currentTurn = $startWithDay ? 'Día' : 'Noche';
                        $personaTurnos = [];
                        $turnoActualCount = 0;

                        foreach ($dates as $i => $date) {
                            if ($turnoActualCount < 5) {
                                $personaTurnos[] = $currentTurn;
                            } else {
                                $personaTurnos[] = 'Descanso';
                            }

                            $turnoActualCount++;
                            if ($turnoActualCount == 7) {
                                $currentTurn = $currentTurn === 'Día' ? 'Noche' : 'Día';
                                $turnoActualCount = 0;
                            }
                        }
                    }
                @endphp

                @if ($personal)
                    <tr>
                        <td>{{ $personal->name }}</td>
                        <td>{{ $personal->document }}</td>
                        <td>
                            @if ($personal->status === 1)
                                <span class="badge rounded-pill badge-success">Activo</span>
                            @else
                                <span class="badge rounded-pill badge-danger">Inactivo</span>
                            @endif
                        </td>
                        @foreach ($dates as $i => $date)
                            @php
                                $isSunday = $date->dayOfWeekIso == 7;
                                $turno = $personaTurnos[$i] ?? 'Descanso';
                
                                $bg = match ($turno) {
                                    'Día' => 'turno-dia',
                                    'Noche' => 'turno-noche',
                                    'Descanso' => 'turno-descanso',
                                    default => '',
                                };
                
                                $horario = match ($turno) {
                                    'Día' => $zoneInfo->schedule->day_hour_since . ' - ' . $zoneInfo->schedule->day_hour_until,
                                    'Noche' => $zoneInfo->schedule->night_hour_since . ' - ' . $zoneInfo->schedule->night_hour_until,
                                    'Descanso' => 'D',
                                    default => '',
                                };
                            @endphp
                
                            <td class="{{ $bg }}" @if ($isSunday) style="background-color: #fff3cd; color: #856404;" @endif>
                                {{ $horario }}
                            </td>
                        @endforeach
                    </tr>
                @else
                    <tr>
                        <td colspan="{{ count($dates) + 3 }}" class="text-center text-muted">
                            No hay personal operativo registrado.
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
