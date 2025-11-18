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
    $appointments = Appointment::where('business_id', auth()->user()->business_id)
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
        $business = auth()->user()->business;
        $settings = $business->settings;

        $rules = [
            'customer_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^\(\d{2}\) \d{5}-\d{4}$/'],
            'scheduled_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ];

        if ($settings->requires_cpf) {
            $rules['cpf'] = ['required', 'string', 'size:11'];
        } else {
            $rules['cpf'] = ['nullable', 'string', 'size:11'];
        }

        $validated = $request->validate($rules);

        $erroHorario = $this->validarHorarioComercial($request->scheduled_at, $business->id);
        
        if ($erroHorario) {
            return back()->withErrors(['scheduled_at' => $erroHorario])->withInput();
        }

        $customer = $business->customers()->updateOrCreate(
            ['phone' => $validated['phone']],
            [
                'name' => $validated['customer_name'],
                'cpf' => $validated['cpf'] ?? null,
            ]
        );

        $appointmentData = [
            'business_id' => $business->id,
            'customer_id' => $customer->id,
            'scheduled_by' => auth()->id(),
            'scheduled_at' => $validated['scheduled_at'],
            'notes' => $validated['notes'],
        ];

        Appointment::create($appointmentData);

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
        // Segurança: Verifica se o agendamento pertence ao estabelecimento do usuário logado
        if ($appointment->business_id !== auth()->user()->business_id) {
            abort(403); // Acesso Proibido
        }

        return view('appointments.edit', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        if ($appointment->business_id !== auth()->user()->business_id) {
            abort(403);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^\(\d{2}\) \d{5}-\d{4}$/'],
            'scheduled_at' => 'required|date',
            'status' => 'required|in:agendado,concluido,cancelado',
            'notes' => 'nullable|string',
        ], [
            'phone.regex' => 'O telefone deve estar no formato (99) 99999-9999',
        ]);

        if ($request->status === 'concluido') {
            $dataAlvo = \Carbon\Carbon::parse($request->scheduled_at);
            if ($dataAlvo->isFuture()) {
                return back()->withErrors(['status' => 'Você não pode concluir um agendamento futuro!'])->withInput();
            }
        }

        if ($request->status === 'agendado') {
            $dataNova = \Carbon\Carbon::parse($request->scheduled_at);
            if ($dataNova->isPast()) {
                return back()->withErrors(['scheduled_at' => 'Para manter como Agendado, a data precisa ser no futuro.'])->withInput();
            }
            $erroHorario = $this->validarHorarioComercial($request->scheduled_at, auth()->user()->business_id);
            if ($erroHorario) {
                return back()->withErrors(['scheduled_at' => $erroHorario])->withInput();
            }
        }

        // Separar os dados do cliente e do agendamento
        $customerData = [
            'name' => $validated['customer_name'],
            'phone' => $validated['phone'],
        ];

        $appointmentData = [
            'scheduled_at' => $validated['scheduled_at'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ];

        // Atualizar o cliente
        $appointment->customer()->update($customerData);

        // Atualizar o agendamento
        $appointment->update($appointmentData);

        return redirect()->route('appointments.index')->with('status', 'Agendamento atualizado!');
    }

    
    public function destroy(Appointment $appointment)
    {
        if ($appointment->business_id !== auth()->user()->business_id) {
            abort(403);
        }

        $appointment->delete();

        return redirect()->route('appointments.index');
    }
    /**
     * Verifica se o horário é válido baseados nas regras de negócio.
     * Retorna string com erro ou null se estiver tudo ok.
     */
    private function validarHorarioComercial($dataString, $businessId)
        {
            $dataAgendamento = Carbon::parse($dataString);
            $diaSemana = $dataAgendamento->dayOfWeek;

            $regra = BusinessHour::where('business_id', $businessId)
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

            // --- CORREÇÃO DO BUG 00:00 ---
            // Se o horário de fechamento for meia-noite (00:00:00), 
            // transformamos para o último segundo do dia (23:59:59)
            if ($horaFechamento === '00:00:00') {
                $horaFechamento = '23:59:59';
            }
            // -----------------------------

            if ($horaAgendamento < $horaAbertura || $horaAgendamento > $horaFechamento) {
                // Ajuste visual na mensagem: Se for 23:59:59, mostramos 00:00 ou 24:00 para ficar bonito
                $horaVisual = $regra->close_at->format('H:i'); 
                
                return "Horário inválido. Neste dia atendemos das {$regra->open_at->format('H:i')} às {$horaVisual}.";
            }

            return null; // Tudo certo!
        }
}
