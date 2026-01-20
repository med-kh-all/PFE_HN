<tr data-row-poste="{{ $line['poste'] ?? '' }}">
    <td style="width:36px;">
        <input type="checkbox" class="row-check" data-poste="{{ $line['poste'] ?? '' }}">
    </td>

    <td><input type="text" name="amortissements[{{ $i }}][{{ $key }}][poste]" class="form-control input-calc" value="{{ $line['poste'] ?? '' }}"></td>
    <td><input type="number" step="0.01" name="amortissements[{{ $i }}][{{ $key }}][cout]" class="form-control input-calc" value="{{ $line['cout'] ?? '' }}" @if($i > $start) readonly @endif></td>
    <td><input type="number" step="0.01" name="amortissements[{{ $i }}][{{ $key }}][amort_cumule_anterieur]" class="form-control input-calc" value="{{ $line['amort_cumule_anterieur'] ?? '' }}" @if($i > $start) readonly @endif></td>
    <td><input type="text" class="form-control valeur-nette" name="amortissements[{{ $i }}][{{ $key }}][valeur_nette_anterieure]" readonly value="{{ $line['valeur_nette_anterieure'] ?? '' }}"></td>
    <td><input type="number" step="0.01" name="amortissements[{{ $i }}][{{ $key }}][acquisition_annee]" class="form-control input-calc" value="{{ $line['acquisition_annee'] ?? '' }}"></td>
    <td><input type="text" class="form-control amort-annee" name="amortissements[{{ $i }}][{{ $key }}][amortissement_annee]" readonly value="{{ $line['amortissement_annee'] ?? '' }}"></td>
    <td><input type="text" class="form-control amort-mois" name="amortissements[{{ $i }}][{{ $key }}][amortissement_mensuel]" readonly value="{{ $line['amortissement_mensuel'] ?? '' }}"></td>
    <td><input type="number" step="0.01" name="amortissements[{{ $i }}][{{ $key }}][taux]" class="form-control input-calc" value="{{ $line['taux'] ?? '' }}"></td>
    <td>
        <select name="amortissements[{{ $i }}][{{ $key }}][type_amortissement]" class="form-control input-calc">
            <option value="L" @if(($line['type_amortissement'] ?? '') === 'L') selected @endif>L (Linéaire)</option>
            <option value="D" @if(($line['type_amortissement'] ?? '') === 'D') selected @endif>D (Dégressif)</option>
        </select>
    </td>
</tr>
