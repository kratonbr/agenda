<?php

use Illuminate\Support\Facades\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; // <--- IMPORTE O DB

Schedule::call(function () {
    
    $agora = Carbon::now();

    // VAMOS USAR O QUERY BUILDER (ignora Models e Mutators)
    $afetados = DB::table('appointments')
        ->where('status', 'agendado')
        ->where('scheduled_at', '<', $agora)
        ->update([
            'status' => 'concluído', // <--- Verifique o acento
            'updated_at' => $agora // update manual não atualiza o timestamp
        ]);

    if ($afetados > 0) {
        Log::info("ROBÔ (DB): $afetados agendamentos foram atualizados para CONCLUÍDO via DB.");
    }

})->everyMinute();