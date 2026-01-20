@if(isset($employee))
<tr data-id="{{ $employee->id }}">
    <td>
        <input type="text"
               class="form-control form-control-sm"
               name="employee_name"
               list="usersList"
               placeholder="Nom employé (ou taper un nouveau)"
               value="{{ old('employee_name', $employee->employee_name) }}">
    </td>
    <td><input type="text" class="form-control form-control-sm" name="position" value="{{ old('position', $employee->position) }}"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="hours_worked_annual" value="{{ old('hours_worked_annual', $employee->hours_worked_annual) }}"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="weeks_worked" value="{{ old('weeks_worked', $employee->weeks_worked) }}"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="vacation_rate" value="{{ old('vacation_rate', $employee->vacation_rate) }}"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="hourly_rate" value="{{ old('hourly_rate', $employee->hourly_rate) }}"></td>

    <!-- 🔹 Calculé automatiquement -->
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="annual_salary" value="{{ old('annual_salary', $employee->annual_salary) }}" readonly></td>

    <td><input type="number" step="0.01" class="form-control form-control-sm" name="retirement_fund" value="{{ old('retirement_fund', $employee->retirement_fund) }}"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="bonus" value="{{ old('bonus', $employee->bonus) }}"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="group_insurance" value="{{ old('group_insurance', $employee->group_insurance) }}"></td>

    <!-- 🔹 Calculé automatiquement -->
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="other_benefits_hourly" value="{{ old('other_benefits_hourly', $employee->other_benefits_hourly) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="paid_vacation" value="{{ old('paid_vacation', $employee->paid_vacation) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="paid_leave" value="{{ old('paid_leave', $employee->paid_leave) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="adjusted_hourly_rate" value="{{ old('adjusted_hourly_rate', $employee->adjusted_hourly_rate) }}" readonly></td>

    <!-- 🔹 Cotisations calculées -->
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rrq" value="{{ old('rrq', $employee->rrq) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="ae" value="{{ old('ae', $employee->ae) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rqap" value="{{ old('rqap', $employee->rqap) }}" readonly></td>
   {{-- CSST uniquement hors CCQ --}}
@unless((int)($isCCQ ?? 0) === 1)
  <td><input type="number" step="0.01" class="form-control form-control-sm" name="csst"
             value="{{ old('csst', $employee->csst) }}"></td>
@endunless

<td><input type="number" step="0.01" class="form-control form-control-sm" name="fssq"
           value="{{ old('fssq', $employee->fssq) }}"></td>

{{-- 🔻 Champs CCQ insérés ICI quand modèle CCQ --}}
@if((int)($isCCQ ?? 0) === 1)
<td><input type="number" step="0.01" class="form-control form-control-sm" name="avantages_sociaux"
             value="{{ old('avantages_sociaux', $employee->ccq->avantages_sociaux?? null) }}"></td>
<td><input type="number" step="0.01" class="form-control form-control-sm" name="taxes_assurance"
             value="{{ old('taxes_assurance', $employee->ccq->taxes_assurance ?? null) }}"></td>
  <td><input type="number" step="0.01" class="form-control form-control-sm" name="ccq"
             value="{{ old('ccq', $employee->ccq->ccq ?? null) }}"></td>
  <td><input type="number" step="0.01" class="form-control form-control-sm" name="aecq"
             value="{{ old('aecq', $employee->ccq->aecq ?? null) }}"></td>
  <td><input type="number" step="0.01" class="form-control form-control-sm" name="fonds_divers"
             value="{{ old('fonds_divers', $employee->ccq->fonds_divers ?? null) }}"></td>
  <td><input type="number" step="0.01" class="form-control form-control-sm" name="equipement_securite"
             value="{{ old('equipement_securite', $employee->ccq->equipement_securite ?? null) }}"></td>
  <td><input type="number" step="0.01" class="form-control form-control-sm" name="clauses_normatives"
             value="{{ old('clauses_normatives', $employee->ccq->clauses_normatives ?? null) }}"></td>
@endif
{{-- 🔺 Fin CCQ --}}

{{-- CNT (affiché "CNESST" en tête si CCQ, mais le name reste "cnt") --}}
<td><input type="number" step="0.01" class="form-control form-control-sm" name="cnt"
           value="{{ old('cnt', $employee->cnt) }}" readonly></td>

    <td><input type="number" step="0.01" class="form-control form-control-sm" name="other_benefits" value="{{ old('other_benefits', $employee->other_benefits) }}"></td>

    <!-- 🔹 Calculs automatiques -->
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rate_before_downtime" value="{{ old('rate_before_downtime', $employee->rate_before_downtime) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="total_annual_cost" value="{{ old('total_annual_cost', $employee->total_annual_cost) }}" readonly></td>

    <td><input type="number" step="0.01" class="form-control form-control-sm" name="non_taxable_dividends" value="{{ old('non_taxable_dividends', $employee->non_taxable_dividends) }}"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="breaks_per_hour" value="{{ old('breaks_per_hour', $employee->breaks_per_hour) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="idle_time_per_hour" value="{{ old('idle_time_per_hour', $employee->idle_time_per_hour) }}" readonly></td>

    <!-- 🔹 Calculs automatiques -->
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="total_non_productive_time" value="{{ old('total_non_productive_time', $employee->total_non_productive_time) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="productive_time" value="{{ old('productive_time', $employee->productive_time) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="productive_time_percentage" value="{{ old('productive_time_percentage', $employee->productive_time_percentage) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rate_with_burden" value="{{ old('rate_with_burden', $employee->rate_with_burden) }}" readonly></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="burden_percentage" value="{{ old('burden_percentage', $employee->burden_percentage) }}" readonly></td>

    <td><input type="date" class="form-control form-control-sm" name="hire_date" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d')) }}"></td>

    <!-- 🔹 Calculé -->
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="seniority" value="{{ old('seniority', $employee->seniority) }}" readonly></td>

    <td>
        <div class="d-flex justify-content-center gap-1">
           
            <button class="btn btn-danger btn-sm btn-delete-employee" title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>
@endif
