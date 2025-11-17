<?php

use Illuminate\Support\Facades\Schedule;
use App\Models\Appointment;
use Carbon\Carbon;

// Rotina: A cada hora, verifica agendamentos passados
Schedule::call(function () {
    // Atualiza para 'concluido' tudo que:
    // 1. Ainda está 'agendado'
    // 2. O horário já passou (menor que agora)
    $afetados = Appointment::where('status', 'agendado')
        ->where('scheduled_at', '<', Carbon::now())
        ->update(['status' => 'concluído']);
        
    // Opcional: Logar para você saber que rodou
    if ($afetados > 0) {
        logger()->info("$afetados agendamentos foram marcados como concluídos automaticamente.");
    }
})->hourly(); // Roda de hora em hora (pode mudar para everyMinute() para testar)