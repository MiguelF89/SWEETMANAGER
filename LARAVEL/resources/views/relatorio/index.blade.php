<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Relatório Financeiro</h2>
                <p class="text-sm text-gray-600">Visão geral de vendas, custos e boletos — {{ $ano }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="GET" action="{{ route('relatorio.index') }}" class="inline">
                    <select name="ano" class="px-3 py-2 border border-gray-300 rounded-md text-sm font-medium focus:border-blue-500 focus:ring-blue-500" onchange="this.form.submit()">
                        @foreach($anos as $a)
                            <option value="{{ $a }}" {{ $a == $ano ? 'selected' : '' }}>{{ $a }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('boleto.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-700 transition">Boletos</a>
                <a href="{{ route('boleto.reader') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Ler boleto</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- KPIs -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-600">
                    <p class="text-sm font-medium text-gray-500">Vendas {{ $ano }}</p>
                    <p class="mt-2 text-2xl font-bold text-blue-700">R$ {{ number_format($totais['vendas_ano'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-red-600">
                    <p class="text-sm font-medium text-gray-500">Custos pagos {{ $ano }}</p>
                    <p class="mt-2 text-2xl font-bold text-red-700">R$ {{ number_format($totais['custos_ano'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-600">
                    <p class="text-sm font-medium text-gray-500">Boletos pendentes</p>
                    <p class="mt-2 text-2xl font-bold text-amber-700">R$ {{ number_format($totais['boletos_pendentes'], 2, ',', '.') }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6 border-l-4 border-green-600">
                    <p class="text-sm font-medium text-gray-500">Lucro estimado {{ $ano }}</p>
                    <p class="mt-2 text-2xl font-bold text-green-700">R$ {{ number_format($totais['lucro_estimado'], 2, ',', '.') }}</p>
                </div>
            </div>

            <!-- Próximos vencimentos -->
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Próximos vencimentos</h3>
                @if($proximosVencimentos->isEmpty())
                    <p class="text-gray-500 text-sm">Nenhum boleto pendente com vencimento cadastrado.</p>
                @else
                    <div class="space-y-3">
                        @foreach($proximosVencimentos as $b)
                            <div class="flex justify-between items-center py-3 border-b border-gray-200 last:border-b-0">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $b->descricao ?: 'Sem descrição' }}</p>
                                    <p class="text-sm text-gray-500">
                                        Vence em {{ $b->vencimento->format('d/m/Y') }}
                                        @if($b->vencimento->isPast())
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">Vencido</span>
                                        @else
                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">em {{ $b->vencimento->diffForHumans() }}</span>
                                        @endif
                                    </p>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $b->valor ? 'R$ ' . number_format($b->valor, 2, ',', '.') : '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Gráficos -->
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Vendas vs Custos por mês</h3>
                    <canvas id="chartVendasCustos" height="100"></canvas>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribuição do ano</h3>
                    <canvas id="chartPizza" height="200"></canvas>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Custos mensais</h3>
                <canvas id="chartCustos" height="100"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const MESES = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
        const dados  = @json($meses->values());

        const vendas    = dados.map(d => d.vendas);
        const custos    = dados.map(d => d.custos);
        const pendentes = dados.map(d => d.pendentes);

        // ── Vendas vs Custos ──────────────────────────────────────────
        new Chart(document.getElementById('chartVendasCustos'), {
            type: 'bar',
            data: {
                labels: MESES,
                datasets: [
                    {
                        label: 'Vendas',
                        data: vendas,
                        backgroundColor: 'rgba(79,70,229,.75)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Custos pagos',
                        data: custos,
                        backgroundColor: 'rgba(220,38,38,.65)',
                        borderRadius: 6,
                    },
                    {
                        label: 'Boletos pendentes',
                        data: pendentes,
                        backgroundColor: 'rgba(217,119,6,.55)',
                        borderRadius: 6,
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        ticks: {
                            callback: v => 'R$ ' + v.toLocaleString('pt-BR')
                        }
                    }
                }
            }
        });

        // ── Pizza ─────────────────────────────────────────────────────
        const totalVendas  = vendas.reduce((a,b)  => a+b, 0);
        const totalCustos  = custos.reduce((a,b)  => a+b, 0);
        const totalPend    = pendentes.reduce((a,b)=> a+b, 0);

        new Chart(document.getElementById('chartPizza'), {
            type: 'doughnut',
            data: {
                labels: ['Vendas', 'Custos pagos', 'Pendentes'],
                datasets: [{
                    data: [totalVendas, totalCustos, totalPend],
                    backgroundColor: [
                        'rgba(79,70,229,.8)',
                        'rgba(220,38,38,.8)',
                        'rgba(217,119,6,.8)',
                    ],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' R$ ' + ctx.parsed.toLocaleString('pt-BR', { minimumFractionDigits: 2 })
                        }
                    }
                }
            }
        });

        // ── Custos mensal (linha) ─────────────────────────────────────
        new Chart(document.getElementById('chartCustos'), {
            type: 'line',
            data: {
                labels: MESES,
                datasets: [{
                    label: 'Custos (R$)',
                    data: custos,
                    borderColor: 'rgba(220,38,38,.9)',
                    backgroundColor: 'rgba(220,38,38,.1)',
                    fill: true,
                    tension: .35,
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: { callback: v => 'R$' + v.toLocaleString('pt-BR') }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
