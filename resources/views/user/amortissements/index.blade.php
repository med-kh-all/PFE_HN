@extends('user.layouts.app')

@section('title', 'Liste des Amortissements')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-12">
                <h2>Liste des Années d'Amortissement</h2>
            </div>
        </div>
        <div class="content-body">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <a href="#" class="btn btn-outline-primary" data-toggle="modal" data-target="#yearModal">
                        <i data-feather="plus"></i> Ajouter Amortissement
                    </a>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Année</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($years as $year)
                                <tr>
                                    <td>{{ $year->year }}</td>
                                    <td>
  <a href="{{ route('user.amortissements.show', ['year' => $year->year]) }}"
     class="btn btn-sm btn-info">Voir</a>

  <form action="{{ route('user.amortissements.destroy', $year->year) }}"
        method="POST" style="display:inline-block">
      @csrf
      @method('DELETE')
      <button class="btn btn-sm btn-danger"
              onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
  </form>
</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal année -->
<div class="modal fade" id="yearModal" tabindex="-1" role="dialog" aria-labelledby="yearModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="yearModalLabel">Ajouter une période</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="startYear">Année de début</label>
                    <input type="number" id="startYear" class="form-control" value="{{ date('Y') }}">
                </div>
                <div class="form-group">
                    <label for="endYear">Année de fin</label>
                    <input type="number" id="endYear" class="form-control" value="{{ date('Y') + 5 }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="submitYears">Continuer</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('submitYears').addEventListener('click', function () {
        const startYear = parseInt(document.getElementById('startYear').value);
        const endYear = parseInt(document.getElementById('endYear').value);
        const numOfYears = endYear - startYear;

        if (startYear <= endYear) {
            window.location.href = "{{ route('user.amortissements.create') }}" + `?start=${startYear}&end=${endYear}&years=${numOfYears}#year-${startYear}`;
        } else {
            alert('Années invalides');
        }
    });
</script>
@endsection
