<div class="white-panel">
<div class="table-responsive">
    <table id="enteteTable" class="table table text-center align-middle">
        <thead class="thead-light">
            <tr>
                <th>Année</th>
                <th>Jours fériés</th>
                <th>Minutes Pause</th>
                <th>Minutes Temps Mort</th>
                <th>% Pause</th>
                <th>% Temps Mort</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entetes as $entete)
                <tr data-id="{{ $entete->id }}">
                    <td><input type="number" class="form-control form-control-sm" name="annee" value="{{ $entete->annee }}"></td>
                    <td><input type="number" class="form-control form-control-sm" name="jours_feries" value="{{ $entete->jours_feries }}"></td>
                    <td><input type="number" class="form-control form-control-sm" name="minutes_pause" value="{{ $entete->minutes_pause }}" onchange="calculateEntetePercentages(this)"></td>
                    <td><input type="number" class="form-control form-control-sm" name="minutes_temps_mort" value="{{ $entete->minutes_temps_mort }}" onchange="calculateEntetePercentages(this)"></td>
                    <td><input type="number" class="form-control form-control-sm" name="pourcentage_pause" value="{{ $entete->pourcentage_pause }}" readonly></td>
                    <td><input type="number" class="form-control form-control-sm" name="pourcentage_temps_mort" value="{{ $entete->pourcentage_temps_mort }}" readonly></td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <button id="btn-save-entete" class="btn btn-success btn-sm btn-save-entete" title="Enregistrer"><i class="fas fa-save"></i></button> <!--  Here !!  -->
                            <button class="btn btn-danger btn-sm btn-delete-entete" title="Supprimer"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr data-id="new">
                    <td><input type="number" class="form-control form-control-sm" name="annee"></td>
                    <td><input type="number" class="form-control form-control-sm" name="jours_feries"></td>
                    <td><input type="number" class="form-control form-control-sm" name="minutes_pause" onchange="calculateEntetePercentages(this)"></td>
                    <td><input type="number" class="form-control form-control-sm" name="minutes_temps_mort" onchange="calculateEntetePercentages(this)"></td>
                    <td><input type="number" class="form-control form-control-sm" name="pourcentage_pause" readonly></td>
                    <td><input type="number" class="form-control form-control-sm" name="pourcentage_temps_mort" readonly></td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-success btn-sm btn-save-entete" title="Enregistrer"><i class="fas fa-save"></i></button>
                            <button class="btn btn-danger btn-sm btn-delete-entete" title="Supprimer"><i class="fas fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-light">
                <td><strong>Total</strong></td>
                <td><span id="totalJoursFeries">0</span></td>
                <td><span id="totalMinutesPause">0</span></td>
                <td><span id="totalMinutesTempsMort">0</span></td>
                <td><span id="totalPourcentagePause">0</span>%</td>
                <td><span id="totalPourcentageTempsMort">0</span>%</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
</div>
