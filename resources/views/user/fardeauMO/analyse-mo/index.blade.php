@extends('user.layouts.app')

@section('title', 'Analyse  Masse salariale')

@push('css')
<style>
  .white-panel{
    background:#fff; border:1px solid #eee; border-radius:14px;
    box-shadow:0 2px 10px rgba(76,87,125,.08); padding:16px 18px; margin-bottom:18px;
  }
  .panel-title{ font-weight:600; font-size:1rem; margin:2px 0 10px; }
  .table-tight td, .table-tight th{ padding:.5rem .6rem; vertical-align:middle; }
  .thead-soft{ background:#f3f2f7; }
</style>
@endpush

@section('content')
<div class="app-content content">
  <div class="content-wrapper">

    <div class="content-header row mb-2">
      <div class="col-12">
        <h2 class="content-header-title">Analyse Masse salariale</h2>
        <div class="breadcrumb-wrapper">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Analyse Masse salariale</li>
          </ol>
        </div>
      </div>
    </div>

    {{-- Tableau d’analyse --}}
    <div class="white-panel">
      <h5 class="panel-title">Analyse Masse Salariale</h5>

      <div class="table-responsive">
        <table class="table table-tight">
          <thead class="thead-soft">
            <tr>
              <th style="min-width:220px">Poste</th>
              @foreach($columns as $col)
                <th class="text-end">{{ $col['label'] }}</th>
              @endforeach
              <th class="text-end">Total</th>
            </tr>
          </thead>
          <tbody>
            @php
              // 2 décimales partout (heures en entier)
              $fmt2 = fn($n) => number_format((float)$n, 2, ',', ' ');
              $fmt0 = fn($n) => number_format((float)$n, 0, ',', ' ');
              $rowTotals = array_fill_keys(array_keys($rowsMap), 0.0);
            @endphp

            {{-- Lignes : Salaires, Vacances, Avantages, CCQ, CSST, BONI, Assurance --}}
@foreach($rowsMap as $key => $field)
  @php
    // sauter la ligne CCQ si on ne veut l’afficher que quand utile
    if ($key === 'ccq' && empty($showCCQRow)) { continue; }
  @endphp
  <tr>
    <td class="fw-600">
      @switch($key)
        @case('salaires')   Salaires @break
        @case('vacances')   Vacances @break
        @case('avantages')  Avantages Sociaux @break
        @case('ccq')        CCQ @break       {{-- ← NEW --}}
        @case('csst')       CSST @break
        @case('boni')       BONI @break
        @case('assurance')  Assurance - Groupe @break
      @endswitch
    </td>

    @foreach($columns as $col)
      @php
        $val = (float) ($col['rows'][$key] ?? 0);
        $rowTotals[$key] += $val;
      @endphp
      <td class="text-end">{{ $fmt2($val) }} $</td>
    @endforeach

    <td class="text-end bg-light">{{ $fmt2($rowTotals[$key]) }} $</td>
  </tr>
@endforeach


            {{-- Ligne Total par activité --}}
            <tr class="table-active fw-600">
              <td>Total</td>
              @foreach($columns as $col)
                <td class="text-end">{{ $fmt2($col['total']) }} $</td>
              @endforeach
              <td class="text-end">{{ $fmt2($grand['total']) }} $</td>
            </tr>

            {{-- Ligne % par activité (sous "Total") --}}
            <tr class="small text-muted">
              <td></td>
              @foreach($percentages as $p)
                <td class="text-end">{{ number_format($p, 2, ',', ' ') }}%</td>
              @endforeach
              <td></td>
            </tr>

            {{-- Nombre total d'heures annuel (entier) --}}
            <tr>
              <td>Nombre Total Heures Annuel</td>
              @foreach($columns as $col)
                <td class="text-end">{{ $fmt0($col['hours']) }}</td>
              @endforeach
              <td class="text-end bg-light">{{ $fmt0($grand['hours']) }}</td>
            </tr>

            {{-- Coût des salaires à l'heure (2 décimales) --}}
            <tr>
              <td>Coût des salaires à l'heure</td>
              @foreach($columns as $col)
                <td class="text-end">{{ $fmt2($col['rate_per_hour']) }} $</td>
              @endforeach
              <td class="text-end bg-light">{{ $fmt2($grand['rate_per_hour']) }} $</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    {{-- Diagramme --}}
    <div class="white-panel">
      <h5 class="panel-title">Répartition Masse salariale</h5>
      <canvas id="masseChart" height="120"></canvas>
    </div>

  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
  const labels = @json($chart['labels']);
  const values = @json($chart['values']);

  // Palette (6 couleurs, tourne si + d'activités)
  const bgColors = [
    'rgba(77,171,247,0.55)',   // bleu
    'rgba(81,207,102,0.55)',   // vert
    'rgba(252,196,25,0.55)',   // jaune
    'rgba(255,107,107,0.55)',  // rouge
    'rgba(151,117,250,0.55)',  // violet
    'rgba(34,184,207,0.55)'    // turquoise
  ];
  const borderColors = [
    'rgba(77,171,247,1)',
    'rgba(81,207,102,1)',
    'rgba(252,196,25,1)',
    'rgba(255,107,107,1)',
    'rgba(151,117,250,1)',
    'rgba(34,184,207,1)'
  ];

  const ctx = document.getElementById('masseChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        data: values,
        backgroundColor: labels.map((_, i) => bgColors[i % bgColors.length]),
        borderColor:     labels.map((_, i) => borderColors[i % borderColors.length]),
        borderWidth: 1,
        borderRadius: 8,
        // ↓ Réduit la largeur des barres
        categoryPercentage: 0.55,   // espace occupé par catégorie
        barPercentage: 0.6,         // largeur réelle de la barre dans la catégorie
        maxBarThickness: 44         // borne max (au cas où)
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => new Intl.NumberFormat('fr-CA').format(ctx.raw) + ' $'
          }
        }
      },
      scales: {
        y: {
          // ↓ Axe vertical demandé
          min: 10000,
          max: 100000,
          beginAtZero: false,
          ticks: {
            stepSize: 10000,
            callback: (v) => new Intl.NumberFormat('fr-CA').format(v) + ' $'
          }
        }
      }
    }
  });

  // 💡 Astuce: si tes valeurs actuelles sont < 50 000, elles ne s’afficheront pas.
  // Tu peux passer en dynamique si besoin:
  // const minVal = Math.min(...values);
  // chart.options.scales.y.min = Math.min(50000, Math.floor(minVal/10000)*10000);
})();
</script>

@endpush
