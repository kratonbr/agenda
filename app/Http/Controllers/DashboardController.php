<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment; // Certifique-se que seu Model está aqui
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function index()
{
    $userId = Auth::id();

    // --- CARD 1: HOJE ---
    // Só contamos quem está agendado ou concluído (Cancelados não contam pro volume de trabalho)
    $agendamentosHoje = Appointment::query()
        ->where('user_id', $userId)
        ->whereDate('scheduled_at', Carbon::today())
        ->where('status', '!=', 'cancelado') // <--- NOVO FILTRO
        ->count();

    // --- CARD 2: PRÓXIMO CLIENTE ---
    // O próximo cliente TEM que ser alguém com status 'agendado'
    $proximoAgendamento = Appointment::query()
        ->where('user_id', $userId)
        ->where('scheduled_at', '>', Carbon::now())
        ->where('status', 'agendado') // <--- IMPORTANTÍSSIMO
        ->orderBy('scheduled_at', 'asc')
        ->first();

    // --- CARD 3: SEMANA ---
    $agendamentosSemana = Appointment::query()
        ->where('user_id', $userId)
        ->whereBetween('scheduled_at', [
            Carbon::today(), 
            Carbon::today()->addDays(7)
        ])
        ->where('status', '!=', 'cancelado') // <--- NOVO FILTRO
        ->count();

    return view('dashboard', compact('agendamentosHoje', 'proximoAgendamento', 'agendamentosSemana'));
}
}