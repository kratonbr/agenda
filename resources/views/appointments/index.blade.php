<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Agendamentos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold">Próximos Clientes</h3>
                        <a href="{{ route('appointments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            + Novo Agendamento
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Cliente</th>
                                    <th scope="col" class="px-6 py-3">Data e Hora</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($appointments as $appointment)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                            {{ $appointment->customer_name }}
                                            <div class="text-xs text-gray-400">{{ $appointment->phone }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ \Carbon\Carbon::parse($appointment->scheduled_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                                {{ $appointment->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('appointments.edit', $appointment) }}" class="font-medium text-blue-600 hover:underline">
                                                Editar
                                            </a>
                                            <form action="{{ route('appointments.destroy', $appointment) }}" method="POST" 
                                                    onsubmit="return confirm('Tem certeza que deseja apagar este agendamento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="font-medium text-red-600 hover:underline">
                                                        Excluir
                                                    </button>
                                             </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                            Nenhum agendamento encontrado. Comece criando um!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>