@extends('layouts.app')

@section('title', 'Dashboard Financier - ECAR')
@section('page_title', 'Accueil')

@section('content')

@php
    $financeLabelsSafe = $financeLabels ?? [];

    $financeBNI = $financeBNI ?? [];
    $financeBFV = $financeBFV ?? [];
    $financeCaisse = $financeCaisse ?? [];

    $labels2Safe = $labels2 ?? [];
    $recettesSafe = $recettes ?? [];
    $depensesSafe = $depenses ?? [];

    $byFaritraSafe = ($byFaritra ?? collect())->toArray();
@endphp

<div class="space-y-6 bg-slate-50 dark:bg-slate-950 p-4">

    <!-- HEADER -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
                Tableau de Bord
            </h1>
            <p class="text-sm text-slate-500">
                
            </p>
        </div>
    </div>

    <!-- KPI CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="rounded-2xl p-5 bg-white dark:bg-slate-900 shadow-lg border-l-4 border-blue-500">
            <p class="text-sm text-slate-500">Total Fidèles</p>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_fideles'] ?? 0 }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white dark:bg-slate-900 shadow-lg border-l-4 border-emerald-500">
            <p class="text-sm text-slate-500">Hommes</p>
            <p class="text-3xl font-bold text-emerald-600">{{ $stats['hommes'] ?? 0 }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white dark:bg-slate-900 shadow-lg border-l-4 border-pink-500">
            <p class="text-sm text-slate-500">Femmes</p>
            <p class="text-3xl font-bold text-pink-600">{{ $stats['femmes'] ?? 0 }}</p>
        </div>

        <div class="rounded-2xl p-5 bg-white dark:bg-slate-900 shadow-lg border-l-4 border-orange-500">
            <p class="text-sm text-slate-500">Actifs</p>
            <p class="text-3xl font-bold text-orange-600">{{ $stats['actifs'] ?? 0 }}</p>
        </div>

    </div>

    <!-- GRID GRAPHIQUES -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">

        <!-- BNI / BFV -->
        <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-5">
            <h3 class="font-semibold mb-4">BANQUES</h3>
            <div class="h-[320px]">
                <canvas id="chartBNIBFV"></canvas>
            </div>
        </div>

        <!-- STATUT -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-5">
            <h3 class="font-semibold mb-4">Répartition Fidèles</h3>
            <div class="h-[320px]">
                <canvas id="chartStatut"></canvas>
            </div>
        </div>

    </div>

    

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
    <!-- CAISSE -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-5">
            <h3 class="font-semibold mb-4">CAISSE</h3>
            <div class="h-[300px]">
                <canvas id="chartCaisse"></canvas>
            </div>
        </div>

        <!-- RECETTES / DEPENSES -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-5">
            <h3 class="font-semibold mb-4">Recettes / Dépenses</h3>
            <div class="h-[300px]">
                <canvas id="chartRecDep"></canvas>
            </div>
        </div>
    </div>

</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    const labels = @js($financeLabelsSafe);

    const bni = @js($financeBNI);
    const bfv = @js($financeBFV);
    const caisse = @js($financeCaisse);

    const labels2 = @js($labels2Safe);
    const recettes = @js($recettesSafe);
    const depenses = @js($depensesSafe);

    const statutLabels = @json(array_keys($byFaritraSafe));
    const statutData = @json(array_values($byFaritraSafe));

    // ======================
    // TOOLTIP FORMAT (K/M)
    // ======================
    function formatValue(value) {
        if (value >= 1000000) return (value / 1000000).toFixed(2) + ' M';
        if (value >= 1000) return (value / 1000).toFixed(1) + ' K';
        return value;
    }

    // ======================
    // BNI / BFV
    // ======================
    new Chart(document.getElementById('chartBNIBFV'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'BNI',
                    data: bni,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37,99,235,0.10)',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: true
                },
                {
                    label: 'BRED',
                    data: bfv,
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22,163,74,0.10)',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: true
                }
            ]
        },

        options: {
            responsive: true,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,

                        // 🔥 LIGNE ÉPAISSE TYPE FINANCE
                        generateLabels: function (chart) {
                            const datasets = chart.data.datasets;

                            return datasets.map((ds, i) => ({
                                text: ds.label,
                                fillStyle: ds.borderColor,
                                strokeStyle: ds.borderColor,
                                lineWidth: 6, // 👉 ÉPAISSEUR AUGMENTÉE
                                hidden: !chart.isDatasetVisible(i),
                                datasetIndex: i,
                                pointStyle: 'line'
                            }));
                        },

                        padding: 20,
                        color: '#64748b'
                    }
                },

                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': ' + formatValue(ctx.raw);
                        }
                    }
                }
            },

            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },

                y: {
                    grid: {
                        color: 'rgba(148,163,184,0.15)'
                    },
                    ticks: {
                        callback: function (value) {
                            return formatValue(value);
                        }
                    }
                }
            }
        }
    });

    // ======================
    // CAISSE
    // ======================
    new Chart(document.getElementById('chartCaisse'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'CAISSE',
                    data: caisse,
                    backgroundColor: 'rgba(245,158,11,0.65)',
                    borderRadius: 6,
                    borderSkipped: false
                }
            ]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        padding: 15,
                        color: '#64748b'
                    }
                },

                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': ' + formatValue(ctx.raw);
                        }
                    }
                }
            },

            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    grid: {
                        color: 'rgba(148,163,184,0.15)'
                    },
                    ticks: {
                        callback: value => formatValue(value)
                    }
                }
            }
        }
    });

    // ======================
    // RECETTES / DEPENSES
    // ======================
    new Chart(document.getElementById('chartRecDep'), {
        type: 'line',
        data: {
            labels: labels2,
            datasets: [
                {
                    label: 'Recettes',
                    data: recettes,
                    borderColor: '#1f8f5f',
                    backgroundColor: 'rgba(31,143,95,0.10)',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: true
                },
                {
                    label: 'Dépenses',
                    data: depenses,
                    borderColor: '#e11d48',
                    backgroundColor: 'rgba(225,29,72,0.10)',
                    borderWidth: 3,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                    tension: 0.45,
                    fill: true
                }
            ]
        },

        options: {
            responsive: true,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,

                        // 🔥 LIGNE ÉPAISSE STYLE FINANCE
                        generateLabels: function (chart) {
                            const datasets = chart.data.datasets;

                            return datasets.map((ds, i) => ({
                                text: ds.label,
                                fillStyle: ds.borderColor,
                                strokeStyle: ds.borderColor,
                                lineWidth: 6,
                                hidden: !chart.isDatasetVisible(i),
                                datasetIndex: i,
                                pointStyle: 'line'
                            }));
                        },

                        padding: 20,
                        color: '#64748b'
                    }
                },

                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    callbacks: {
                        label: function (ctx) {
                            return ctx.dataset.label + ': ' + formatValue(ctx.raw);
                        }
                    }
                }
            },

            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    grid: {
                        color: 'rgba(148,163,184,0.15)'
                    },
                    ticks: {
                        callback: value => formatValue(value)
                    }
                }
            }
        }
    });

    // ======================
    // STATUT (DONUT PRO)
    // ======================
    new Chart(document.getElementById('chartStatut'), {
        type: 'doughnut',
        data: {
            labels: statutLabels,
            datasets: [{
                data: statutData,
                backgroundColor: [
                    '#2563eb',
                    '#16a34a',
                    '#f59e0b',
                    '#e11d48',
                    '#8b5cf6'
                ],
                borderWidth: 2,
                hoverOffset: 10
            }]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20,
                        color: '#64748b'
                    }
                },

                tooltip: {
                    backgroundColor: '#0f172a',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    callbacks: {
                        label: function (ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const value = ctx.raw;
                            const percent = ((value / total) * 100).toFixed(1);

                            return `${ctx.label}: ${value} (${percent}%)`;
                        }
                    }
                }
            },

            cutout: '60%' // effet donut plus pro
        }
    });

});
</script>
