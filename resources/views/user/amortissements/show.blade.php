@extends('user.layouts.app')
@section('title', "Amortissements - Année $year")

@section('content')
<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-12">
        <h2>Amortissements pour l'année {{ $year }}</h2>
     <a href="{{ route('user.amortissements.create', [
        'start' => $years->min(),  {{-- première année existante --}}
        'end'   => $years->max()   {{-- dernière année existante --}}
    ]) }}#year-{{ $year }}"
   class="btn btn-outline-primary mb-2">
   Modifier cette année
</a>


      </div>
    </div>

    <div class="content-body">
      @if ($amortissements->isEmpty())
        <div class="alert alert-info">
          Aucune ligne enregistrée pour {{ $year }}.
          <a href="{{ route('user.amortissements.create', ['start' => $year, 'end' => $year]) }}#year-{{ $year }}">
            Créer/Générer cette année
          </a>.
        </div>
      @else
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped">
                <thead class="thead-dark">
                  <tr>
                    <th>Poste</th>
                    <th>Coût</th>
                    <th>Amort. cumulé préc.</th>
                    <th>Valeur nette préc.</th>
                    <th>Acquisition</th>
                    <th>Amort. année</th>
                    <th>Amort. mensuel</th>
                    <th>Taux %</th>
                    <th>Méthode</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($amortissements as $a)
                    <tr>
                      <td>{{ $a->poste }}</td>
                      <td>{{ number_format($a->cout, 2, ',', ' ') }}</td>
                      <td>{{ number_format($a->amort_cumule_anterieur, 2, ',', ' ') }}</td>
                      <td>{{ number_format($a->valeur_nette_anterieure, 2, ',', ' ') }}</td>
                      <td>{{ number_format($a->acquisition_annee, 2, ',', ' ') }}</td>
                      <td>{{ number_format($a->amortissement_annee, 2, ',', ' ') }}</td>
                      <td>{{ number_format($a->amortissement_mensuel, 2, ',', ' ') }}</td>
                      <td>{{ rtrim(rtrim(number_format($a->taux, 2, ',', ' '), '0'), ',') }}%</td>
                      <td>{{ $a->type_amortissement === 'L' ? 'L (Linéaire)' : 'D (Dégressif)' }}</td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr style="font-weight:700;background:#f8f9fa">
                    <td>Total</td>
                    <td>{{ number_format($amortissements->sum('cout'), 2, ',', ' ') }}</td>
                    <td>{{ number_format($amortissements->sum('amort_cumule_anterieur'), 2, ',', ' ') }}</td>
                    <td>{{ number_format($amortissements->sum('valeur_nette_anterieure'), 2, ',', ' ') }}</td>
                    <td>{{ number_format($amortissements->sum('acquisition_annee'), 2, ',', ' ') }}</td>
                    <td>{{ number_format($amortissements->sum('amortissement_annee'), 2, ',', ' ') }}</td>
                    <td>{{ number_format($amortissements->sum('amortissement_mensuel'), 2, ',', ' ') }}</td>
                    <td colspan="2"></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
