<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Painel</h2>
                <p class="text-sm text-gray-600">Visão geral dos seus dados e encomendas.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('encomendas.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Encomendas</a>
                <a href="{{ route('boleto.reader') }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition">Ler Boleto</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total de encomendas</p>
                    <p class="mt-4 text-3xl font-semibold text-gray-900">{{ $totalEncomendas }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total pago</p>
                    <p class="mt-4 text-3xl font-semibold text-green-700">{{ $totalPago }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total pendente</p>
                    <p class="mt-4 text-3xl font-semibold text-red-700">{{ $totalPendente }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Entregas próximas</p>
                    <p class="mt-4 text-3xl font-semibold text-blue-700">{{ $entregasProximas->count() }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('clientes.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition font-medium">Criar Cliente</a>
                <a href="{{ route('produtos.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-green-600 text-white rounded-md hover:bg-green-700 transition font-medium">Criar Produto</a>
                <a href="{{ route('vendas.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition font-medium">Criar Venda</a>
                <a href="{{ route('encomendas.create') }}" class="inline-flex items-center justify-center px-6 py-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition font-medium">Criar Encomenda</a>
            </div>

            {{-- Entregas Próximas --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Entregas próximas</h3>
                        <a href="{{ route('encomendas.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Ver todas</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Entrega</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Valor</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pago</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Repassado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($entregasProximas as $encomenda)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $encomenda->cliente }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $encomenda->data_entrega->format('d/m/Y') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($encomenda->valor, 2, ',', '.') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $encomenda->pago ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $encomenda->pago ? 'Pago' : 'Pendente' }}</span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $encomenda->repassado_cliente ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">{{ $encomenda->repassado_cliente ? 'Sim' : 'Não' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">Nenhuma entrega próxima encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Últimos Clientes --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900">Últimos Clientes</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contato</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">CPF</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($clientes as $cliente)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cliente->nome }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cliente->contato_formatted }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cliente->cpf_formatted }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-sm text-gray-500">Nenhum cliente cadastrado</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('clientes.index') }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Ver todos os clientes</a>
                    </div>
                </div>
            </div>

            {{-- Últimas Vendas --}}
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Últimas Vendas</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="border border-gray-300 px-4 py-2">ID</th>
                                    <th class="border border-gray-300 px-4 py-2">Cliente</th>
                                    <th class="border border-gray-300 px-4 py-2">Produto</th>
                                    <th class="border border-gray-300 px-4 py-2">Quantidade</th>
                                    <th class="border border-gray-300 px-4 py-2">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($vendas as $venda)
                                    <tr>
                                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">{{ $venda->id }}</td>
                                        {{-- CORREÇÃO: ?-> evita "Attempt to read property on null"
                                             quando cliente ou produto foi deletado do banco --}}
                                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">{{ $venda->cliente?->nome ?? '—' }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">{{ $venda->produto?->nome ?? '—' }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">{{ $venda->quantidade }}</td>
                                        <td class="border border-gray-300 px-4 py-2 text-sm text-gray-900">R$ {{ number_format($venda->valor_total, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="border border-gray-300 px-4 py-2 text-center text-sm text-gray-500">Nenhuma venda cadastrada</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('vendas.index') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Ver todas as vendas</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>