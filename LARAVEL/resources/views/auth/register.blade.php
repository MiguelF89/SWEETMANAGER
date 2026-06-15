<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="Nome" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="E-mail" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="Senha" />
                
                <div class="flex items-center justify-between w-full mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 overflow-hidden">
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           class="w-full border-none focus:ring-0 focus:outline-none py-2 px-3 text-gray-900" />
                    
                    <button type="button" 
                            class="toggle-password-btn px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none bg-transparent h-full">
                        
                        <svg class="eye-open-icon h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        
                        <svg class="eye-closed-icon h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.4m3.045-1.182A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.4m-4.569-4.569a3 3 0 11-4.243-4.243M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="Confirmar Senha" />
                
                <div class="flex items-center justify-between w-full mt-1 bg-white border border-gray-300 rounded-md shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 overflow-hidden">
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           class="w-full border-none focus:ring-0 focus:outline-none py-2 px-3 text-gray-900" />
                    
                    <button type="button" 
                            class="toggle-password-btn px-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none bg-transparent h-full">
                        
                        <svg class="eye-open-icon h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        
                        <svg class="eye-closed-icon h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.025 10.025 0 014.132-5.4m3.045-1.182A10.05 10.05 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.4m-4.569-4.569a3 3 0 11-4.243-4.243M3 3l18 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    Já tem uma conta?
                </a>

                <x-button class="ms-4">
                    Cadastrar
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Seleciona os dois inputs de senha
            const passwordInputs = [
                document.getElementById('password'),
                document.getElementById('password_confirmation')
            ];
            
            // Seleciona todos os botões e ícones pelas classes
            const buttons = document.querySelectorAll('.toggle-password-btn');
            const openIcons = document.querySelectorAll('.eye-open-icon');
            const closedIcons = document.querySelectorAll('.eye-closed-icon');

            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    // Verifica o estado atual baseado no primeiro input
                    const isPassword = passwordInputs[0].type === 'password';

                    // Altera o tipo de AMBOS os campos ao mesmo tempo
                    passwordInputs.forEach(input => {
                        if (input) input.type = isPassword ? 'text' : 'password';
                    });

                    // Sincroniza visualmente todos os ícones de olho da tela
                    if (isPassword) {
                        openIcons.forEach(icon => icon.classList.add('hidden'));
                        closedIcons.forEach(icon => icon.classList.remove('hidden'));
                    } else {
                        openIcons.forEach(icon => icon.classList.remove('hidden'));
                        closedIcons.forEach(icon => icon.classList.add('hidden'));
                    }
                });
            });
        });
    </script>
</x-guest-layout>