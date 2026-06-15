<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Boletos</h2>
                <p class="text-sm text-gray-600">Gerencie seus boletos a pagar e pagos.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('boleto.reader') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Ler novo boleto</a>
                <a href="{{ route('relatorio.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">Relatório</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Cards resumo -->
            <div class="grid gap-4 sm:grid-cols-3">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">A Pagar</p>
                    <p class="mt-2 text-2xl font-semibold text-amber-600">R$ {{ number_format($totais['pendente'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Pago</p>
                    <p class="mt-2 text-2xl font-semibold text-green-700">R$ {{ number_format($totais['pago'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total Geral</p>
                    <p class="mt-2 text-2xl font-semibold text-blue-700">R$ {{ number_format($totais['total'], 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('boleto.index') }}" class="px-3 py-2 {{ $status === 'todos' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md text-sm font-medium hover:opacity-90 transition">Todos</a>
                <a href="{{ route('boleto.index', ['status' => 'pendente']) }}" class="px-3 py-2 {{ $status === 'pendente' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md text-sm font-medium hover:opacity-90 transition">Pendentes</a>
                <a href="{{ route('boleto.index', ['status' => 'pago']) }}" class="px-3 py-2 {{ $status === 'pago' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded-md text-sm font-medium hover:opacity-90 transition">Pagos</a>
            </div>

            <!-- Tabela -->
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                @if($boletos->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <p class="text-lg">Nenhum boleto encontrado.</p>
                        <a href="{{ route('boleto.reader') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Ler primeiro boleto</a>
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Descrição</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vencimento</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($boletos as $boleto)
                                <tr id="row-{{ $boleto->id }}" class="hover:bg-gray-50">
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        <div class="font-semibold text-gray-900">{{ $boleto->descricao ?: 'Sem descrição' }}</div>
                                        @if($boleto->banco)
                                            <div class="text-xs text-gray-500">Banco {{ $boleto->banco }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ $boleto->valor ? 'R$ ' . number_format($boleto->valor, 2, ',', '.') : '—' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $boleto->vencimento ? $boleto->vencimento->format('d/m/Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $boleto->status === 'pago' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($boleto->status) }}
                                        </span>
                                        @if($boleto->status === 'pago' && $boleto->data_pagamento)
                                            <div class="text-xs text-gray-500 mt-1">
                                                Pago em {{ $boleto->data_pagamento->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        @if($boleto->status !== 'pago')
                                            <button type="button" onclick="marcarPago({{ $boleto->id }})" class="text-green-600 hover:text-green-900">Marcar pago</button>
                                        @endif
                                        <button type="button" onclick="excluir({{ $boleto->id }})" class="text-red-600 hover:text-red-900">Excluir</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($boletos->hasPages())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    {{ $boletos->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        async function marcarPago(id) {
            if (!confirm('Marcar este boleto como pago?')) return;
            const res = await fetch(`/boleto/${id}/pagar`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            const json = await res.json();
            if (json.success) {
                showToast('Boleto marcado como pago!');
                setTimeout(() => location.reload(), 800);
            }
        }

        async function excluir(id) {
            if (!confirm('Excluir este boleto? Esta ação não pode ser desfeita.')) return;
            const res = await fetch(`/boleto/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            });
            const json = await res.json();
            if (json.success) {
                document.getElementById('row-' + id).remove();
                showToast('Boleto excluído.');
            }
        }

        function showToast(msg) {
            alert(msg);
        }
    </script>
</x-app-layout>