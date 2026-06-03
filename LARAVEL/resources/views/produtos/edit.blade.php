<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Produto</h2>
                <p class="text-sm text-gray-600">Atualize os dados do produto.</p>
            </div>
            <a href="{{ route('produtos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition">
                Voltar para lista
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('produtos.update', $produto->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                        <input type="text" name="nome" value="{{ old('nome', $produto->nome) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required />
                        @error('nome') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Preço</label>
                        <div class="mt-1 flex items-center">
                            <span class="text-gray-500 mr-2">R$</span>
                            <input type="number" name="preco" value="{{ old('preco', $produto->preco) }}" step="0.01" min="0" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" oninput="atualizarPreview(this.value)" required />
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Valor formatado: <span id="preco-preview" class="font-semibold text-green-600">R$ 0,00</span>
                        </p>
                        @error('preco') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('produtos.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancelar</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Atualizar Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function atualizarPreview(valor) {
            const num = parseFloat(valor);
            const el = document.getElementById('preco-preview');
            if (isNaN(num) || valor === '') {
                el.textContent = 'R$ 0,00';
            } else {
                el.textContent = num.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('input[name="preco"]');
            if (input.value) atualizarPreview(input.value);
        });
    </script>
</x-app-layout>
