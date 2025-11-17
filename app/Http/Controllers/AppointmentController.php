<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        {
    // Pega todos os agendamentos do banco
    $appointments = Appointment::where('user_id', auth()->id())->get();

    // Manda eles para uma view (que vamos criar no próximo passo)
    return view('appointments.index', compact('appointments'));
}
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
    // 1. Validar (Garantir que não venha lixo)
    $validated = $request->validate([
    'customer_name' => 'required|string|max:255',
    
    // Regex para formato brasileiro: (XX) XXXXX-XXXX
    'phone' => ['required', 'string', 'regex:/^\(\d{2}\) \d{5}-\d{4}$/'], 
    
    'scheduled_at' => 'required|date|after:now', 
    // "after:now" impede agendamentos no passado,
    'notes' => 'nullable|string',
    ], [
    // Mensagem personalizada caso o erro aconteça
    'phone.regex' => 'O telefone deve estar no formato (99) 99999-9999',
    ]);;
    $validated['user_id'] = auth()->id();
    // 2. Criar no Banco

    
    // Como já configuramos o $fillable no Model, podemos usar create direto.
    Appointment::create($validated);

    // 3. Redirecionar de volta para a lista
    return redirect()->route('appointments.index');
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
        // Segurança
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'regex:/^\(\d{2}\) \d{5}-\d{4}$/'],
            'scheduled_at' => 'required|date|after:now',
            'status' => 'required|in:agendado,concluido,cancelado', // Validamos o status também
            'notes' => 'nullable|string',
        ]);

        // Atualiza no banco
        $appointment->update($validated);

        return redirect()->route('appointments.index');
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
}
