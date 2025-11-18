<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('customers.update', $customer) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="name" :value="__('Nome')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $customer->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="phone" :value="__('Telefone')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $customer->phone)" required />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                            @if (auth()->user()->business->settings->requires_cpf)
                                <div>
                                    <x-input-label for="cpf" :value="__('CPF')" />
                                    <x-text-input id="cpf" class="block mt-1 w-full" type="text" name="cpf" :value="old('cpf', $customer->cpf)" required />
                                    <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                                </div>
                            @else
                                <div>
                                    <x-input-label for="cpf" :value="__('CPF (Opcional)')" />
                                    <x-text-input id="cpf" class="block mt-1 w-full" type="text" name="cpf" :value="old('cpf', $customer->cpf)" />
                                    <x-input-error :messages="$errors->get('cpf')" class="mt-2" />
                                </div>
                            @endif
                            <div>
                                <x-input-label for="email" :value="__('Email (Opcional)')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $customer->email)" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Endereço (Opcional)')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address', $customer->address)" />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
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
