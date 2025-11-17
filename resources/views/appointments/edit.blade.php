<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Agendamento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                
                    <form action="{{ route('appointments.update', $appointment) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="mb-4">
                            <label for="customer_name" class="block text-sm font-medium text-gray-700">Nome do Cliente</label>
                            <input type="text" name="customer_name" id="customer_name" required
                                value="{{ old('customer_name', $appointment->customer_name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-gray-700">Telefone / WhatsApp</label>
                            <input type="text" name="phone" id="phone" 
                                value="{{ old('phone', $appointment->phone) }}"
                                placeholder="(99) 99999-9999" maxlength="15" onkeyup="handlePhone(event)"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                            <div class="mb-4">
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Data e Hora</label>
                                
                                <input type="datetime-local" 
                                    name="scheduled_at" 
                                    id="scheduled_at" 
                                    required
                                    value="{{ old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i')) }}"
                                    min="{{ now()->format('Y-m-d\TH:i') }}" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                                <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                            </div>

                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                    
                                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="agendado" {{ old('status', $appointment->status) == 'agendado' ? 'selected' : '' }}>Agendado</option>
                                        <option value="concluido" {{ old('status', $appointment->status) == 'concluido' ? 'selected' : '' }}>Concluído</option>
                                        <option value="cancelado" {{ old('status', $appointment->status) == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>

                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Observações</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $appointment->notes) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-4">
                            <a href="{{ route('appointments.index') }}" class="text-gray-600 hover:text-gray-900 underline">Cancelar</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Atualizar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const handlePhone = (event) => {
            let input = event.target
            input.value = phoneMask(input.value)
        }
        const phoneMask = (value) => {
            if (!value) return ""
            value = value.replace(/\D/g,'')
            value = value.replace(/(\d{2})(\d)/,"($1) $2")
            value = value.replace(/(\d)(\d{4})$/,"$1-$2")
            return value
        }
    </script>
</x-app-layout>