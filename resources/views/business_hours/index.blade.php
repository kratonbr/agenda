<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Horários de Atendimento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('status'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('business_hours.update') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-4">
                            @foreach($diasDaSemana as $dayIndex => $dayName)
                                @php
                                    // Tenta achar o horário salvo para este dia, ou cria um vazio
                                    $horario = $horarios->where('day', $dayIndex)->first();
                                    $isOpen = $horario ? $horario->is_open : true; // Padrão aberto
                                    $openAt = $horario ? ($horario->open_at ? $horario->open_at->format('H:i') : '09:00') : '09:00';
                                    $closeAt = $horario ? ($horario->close_at ? $horario->close_at->format('H:i') : '18:00') : '18:00';
                                @endphp

                                <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50 transition">
                                    
                                    <div class="w-1/3 flex items-center">
                                        <input type="checkbox" 
                                               name="hours[{{ $dayIndex }}][is_open]" 
                                               value="1" 
                                               {{ $isOpen ? 'checked' : '' }}
                                               class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-gray-300">
                                        <span class="ml-3 font-medium text-gray-700">{{ $dayName }}</span>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <input type="time" 
                                               name="hours[{{ $dayIndex }}][open_at]" 
                                               value="{{ $openAt }}"
                                               class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                        <span class="text-gray-500">até</span>
                                        <input type="time" 
                                               name="hours[{{ $dayIndex }}][close_at]" 
                                               value="{{ $closeAt }}"
                                               class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow">
                                Salvar Horários
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>