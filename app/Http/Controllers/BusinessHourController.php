<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessHourController extends Controller
{
    public function index()
    {
        $horarios = BusinessHour::where('business_id', Auth::user()->business_id)->get();

        $diasDaSemana = [
            0 => 'Domingo',
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
        ];

        return view('business_hours.index', compact('horarios', 'diasDaSemana'));
    }

    public function update(Request $request)
    {
        // Validamos os dados que vêm do formulário
        $data = $request->validate([
            'hours' => 'required|array', // Recebemos um array de horários
            'hours.*.open_at' => 'nullable|date_format:H:i',
            'hours.*.close_at' => 'nullable|date_format:H:i',
            'hours.*.is_open' => 'nullable|boolean',
        ]);

        $businessId = Auth::user()->business_id;

        foreach ($data['hours'] as $day => $hourData) {
            BusinessHour::updateOrCreate(
                [
                    'business_id' => $businessId,
                    'day' => $day
                ],
                [
                    'open_at' => $hourData['open_at'] ?? null,
                    'close_at' => $hourData['close_at'] ?? null,
                    // Se o checkbox não vier marcado, assumimos false (fechado)
                    'is_open' => isset($hourData['is_open']) ? true : false,
                ]
            );
        }

        return back()->with('status', 'Horários atualizados com sucesso!');
    }
}