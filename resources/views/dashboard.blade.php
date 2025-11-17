<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Painel de Controle') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-blue-500">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm text-gray-500 mb-1">Agendamentos Hoje</div>
                        <div class="text-3xl font-bold text-blue-600">
                            {{ $agendamentosHoje }}
                        </div>
                        <div class="text-xs text-gray-400 mt-2">
                            {{ \Carbon\Carbon::today()->format('d/m/Y') }}
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-green-500">
    <div class="p-6 text-gray-900">
        <div class="text-sm text-gray-500 mb-1">Próximo Cliente</div>
        
        @if($proximoAgendamento)
            <div class="text-2xl font-bold text-gray-800 truncate uppercase">
                {{ $proximoAgendamento->customer_name }}
            </div>
            
            <div class="text-lg text-green-600 font-semibold mt-1 flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ $proximoAgendamento->scheduled_at->format('H:i') }} 
                
                <span class="text-xs text-gray-400 font-normal ml-2">
                    ({{ $proximoAgendamento->scheduled_at->format('d/m') }})
                </span>
            </div>
            
            <div class="text-sm text-gray-400 mt-2">
                {{ $proximoAgendamento->phone ?? 'Sem telefone' }}
            </div>

        @else
            <div class="text-lg text-gray-400 italic mt-2">
                Agenda livre por enquanto!
            </div>
        @endif
    </div>
</div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-purple-500">
                    <div class="p-6 text-gray-900">
                        <div class="text-sm text-gray-500 mb-1">Próximos 7 Dias</div>
                        <div class="text-3xl font-bold text-purple-600">
                            {{ $agendamentosSemana }}
                        </div>
                        <div class="text-xs text-gray-400 mt-2">
                            Volume da semana
                        </div>
                    </div>
                </div>

            </div> 
            </div>
    </div>
</x-app-layout>