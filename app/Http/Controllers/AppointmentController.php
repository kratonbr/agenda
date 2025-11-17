<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use App\Models\BusinessHour;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $appointments = Appointment::where('user_id', auth()->id())
        // Ordena primeiro pelo status (para agrupar), depois pela data
        // Truque de SQL: Podemos ordenar por campo específico se quisermos, 
        // mas por enquanto vamos ordenar pela DATA, trazendo os mais recentes primeiro.
        ->orderBy('scheduled_at', 'asc') 
        ->paginate(10);

    return view('appointments.index', compact('appointments'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('appointments.create');
    }

    /**
     * Store a newly created resource in storage.
     */



public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^\(\d{2}\) \d{5}-\d{4}$/'], 
            'scheduled_at' => 'required|date|after:now', 
            'notes' => 'nullable|string',
        ]);

        // --- NOVA CHAMADA DE VALIDAÇÃO ---
        $erroHorario = $this->validarHorarioComercial($request->scheduled_at, auth()->id());
        
        if ($erroHorario) {
            return back()->withErrors(['scheduled_at' => $erroHorario])->withInput();
        }
        // ---------------------------------

        $validated['user_id'] = auth()->id();
        Appointment::create($validated);

        return redirect()->route('appointments.index')->with('status', 'Agendamento criado!');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    // Método que ABRE a tela de edição
    public function edit(Appointment $appointment)
    {
        // Segurança: Verifica se o agendamento pertence ao usuário logado
        if ($appointment->user_id !== auth()->id()) {
            abort(403); // Acesso Proibido
        }

        return view('appointments.edit', compact('appointment'));
    }

    // Método que SALVA as alterações
    public function update(Request $request, Appointment $appointment)
    {
        // 1. Segurança básica
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        // 2. Validação dos campos
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^\(\d{2}\) \d{5}-\d{4}$/'],
            
            // REMOVI O 'after:now' PARA VOCÊ PODER EDITAR AGENDAMENTOS PASSADOS/CONCLUÍDOS
            'scheduled_at' => 'required|date', 
            
            'status' => 'required|in:agendado,concluido,cancelado', 
            'notes' => 'nullable|string',
        ], [
            'phone.regex' => 'O telefone deve estar no formato (99) 99999-9999',
        ]);

        // === 3. BLOCO NOVO: Proteção do Status Concluído ===
        if ($request->status === 'concluido') {
            // Verifica a data que o usuário enviou
            $dataAlvo = \Carbon\Carbon::parse($request->scheduled_at);
            
            // Se for futuro, não deixa concluir
            if ($dataAlvo->isFuture()) {
                return back()
                    ->withErrors(['status' => 'Você não pode concluir um agendamento futuro!'])
                    ->withInput();
            }
        }
        // ===================================================

        // === 4. BLOCO NOVO: Validação de Horário (Fase 2) ===
        // Só validamos horário se o status for 'agendado' (para não travar edições antigas)
        if ($request->status === 'agendado') {
            // Chama aquela função privada que criamos antes
            $erroHorario = $this->validarHorarioComercial($request->scheduled_at, auth()->id());
            
            if ($erroHorario) {
                return back()->withErrors(['scheduled_at' => $erroHorario])->withInput();
            }
        }
        // ====================================================

        // 5. Atualiza no banco
        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('status', 'Agendamento atualizado!');
    }

    
    public function destroy(Appointment $appointment)
    {
        // 1. Segurança: Só o dono pode apagar
    if ($appointment->user_id !== auth()->id()) {
        abort(403);
    }

    // 2. Apaga do banco
    $appointment->delete();

    // 3. Volta para a lista
    return redirect()->route('appointments.index');
    }
    /**
     * Verifica se o horário é válido baseados nas regras de negócio.
     * Retorna string com erro ou null se estiver tudo ok.
     */
    private function validarHorarioComercial($dataString, $userId)
    {
        $dataAgendamento = Carbon::parse($dataString);
        $diaSemana = $dataAgendamento->dayOfWeek;

        $regra = BusinessHour::where('user_id', $userId)
            ->where('day', $diaSemana)
            ->first();

        // Regra 1: Dia Fechado
        if (!$regra || !$regra->is_open) {
            return 'Desculpe, estamos fechados neste dia da semana.';
        }

        // Regra 2: Horário
        $horaAgendamento = $dataAgendamento->format('H:i:s');
        $horaAbertura = $regra->open_at->format('H:i:s');
        $horaFechamento = $regra->close_at->format('H:i:s');

        if ($horaAgendamento < $horaAbertura || $horaAgendamento > $horaFechamento) {
            return "Horário inválido. Neste dia atendemos das {$regra->open_at->format('H:i')} às {$regra->close_at->format('H:i')}.";
        }

        return null; // Tudo certo!
    }
}
