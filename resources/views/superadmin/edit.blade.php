<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Estabelecimento') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('superadmin.update', $business) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nome do Estabelecimento')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $business->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="type" :value="__('Tipo')" />
                                <select name="type" id="type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="clinica" @selected($business->type == 'clinica')>Clínica</option>
                                    <option value="salao" @selected($business->type == 'salao')>Salão</option>
                                    <option value="consultorio" @selected($business->type == 'consultorio')>Consultório</option>
                                    <option value="outro" @selected($business->type == 'outro')>Outro</option>
                                </select>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Telefone')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $business->phone)" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Endereço')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $business->address)" required />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="active" :value="__('Status')" />
                                <select name="active" id="active" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="1" @selected($business->active)>Ativo</option>
                                    <option value="0" @selected(!$business->active)>Inativo</option>
                                </select>
                                <x-input-error :messages="$errors->get('active')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button>
                                {{ __('Salvar Alterações') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
