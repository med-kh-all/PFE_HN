@extends('user.layouts.app')

@section('title', 'Fardeau de la Main-d’Œuvre')



@php

    // Juste pour garantir que les deux objets sont disponibles (débogage / fallback)
    $constants = $constants ?? null;
    $csstContribution = $csstContribution ?? null;
@endphp

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css">
    <style>
        /* Réduction générale des inputs */
        input.form-control-sm {
            min-width: 80px;
            max-width: 130px;
            padding: 0.3rem 0.4rem;
            font-size: 0.75rem;
            text-align: center;
        }

        /* Ajustements spécifiques par colonne si nécessaire */
        td input[name="employee_name"],
        td input[name="position"] {
            min-width: 150px;
            max-width: 200px;
        }

        td input[name="hire_date"] {
            min-width: 120px;
        }

        td input[type="number"] {
            text-align: right;
        }

        /* Réduction des marges pour garder la table compacte */
        .table td {
            padding: 0.25rem 0.3rem;
            vertical-align: middle;
        }

        /* Optionnel : ajuster les boutons d'action */
        .btn-sm i {
            font-size: 0.8rem;
        }
        
    </style>
 <style>
  /* Conteneur qui centre verticalement et horizontalement */
  .recap-section{
    min-height: 55vh;                 /* hauteur suffisante pour le mettre "au milieu" visuellement */
    display: flex;
    align-items: center;               /* centre vertical */
    justify-content: center;           /* centre horizontal */
  }

  /* Largeur contrôlée et responsive du bloc */
  .recap-container{
    width: 100%;
    max-width: 820px;                  /* ↓ rétrécir ici (essaie 760–840px selon ton goût) */
    margin: 0 auto;
  }
  @media (min-width: 1400px){
    .recap-container{ max-width: 760px; } /* un poil plus étroit sur grands écrans */
  }

  /* Styles du bloc recap (restent comme avant) */
  .recap-card{ background: transparent; border: 0; box-shadow: none; }
  .recap-title{
    background: #f3f2f7; padding: .6rem 1rem;
    border: 1px solid #eee; border-bottom: 0;
    border-radius: .5rem .5rem 0 0; font-weight: 600;
  }
  .recap-wrap{ border: 1px solid #eee; border-top: 0; border-radius: 0 0 .5rem .5rem; overflow: hidden; }
  .recap-table{ margin-bottom: 0; }
  .recap-table td{ padding: .35rem .6rem; }
  .recap-table td:first-child{ text-align: left; }
  .recap-table td:last-child{ text-align: right; width: 220px; }
</style>
<style>
  /* Bloc blanc type "carte" */
  .white-panel{
    background:#fff;
    border:1px solid #eee;
    border-radius:14px;
    box-shadow:0 2px 10px rgba(76,87,125,.08);
    padding:16px 18px;
    margin-bottom:18px;
  }
  .white-panel .table{ margin-bottom:0; }

  /* Titre discret comme sur tes autres pages */
  .panel-title{
    font-weight:600;
    font-size:1rem;
    margin:2px 0 10px;
  }
</style>


@endpush



@section('content')

<div class="app-content content">
    <div class="content-wrapper">
         {{-- ✅ Liste déroulante des utilisateurs --}}
        <datalist id="usersList">
            @foreach($users as $u)
                <option value="{{ $u->name }}"></option>
                
            @endforeach
        </datalist>
        <div class="content-header row mb-2">
            <div class="col-12">
                <h2 class="content-header-title">Fardeau de la Main-d’Œuvre</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Fardeau MO</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- 🟦 Entêtes d'Activité --}}
        @include('user.fardeauMO.partials.entete-table', ['entetes' => $entetes])
       <input type="hidden" name="jour_ferie" value="{{ $entetes->first()->jours_feries ?? 0 }}">
        {{-- 🟨 Employés --}}
        <div class="white-panel">
        <div class="d-flex justify-content-end mb-2">
            <button id="addRowBtn" type="button" class="btn btn-primary btn-sm">
                + 
            </button>
            <button id="saveAllBtn" type="button" class="btn btn-success btn-sm ms-1">
                 Enregistrer tout
            </button>
             <input type="hidden" id="operationTypeId" value="{{ $operationTypeId }}">
             <input type="hidden" id="isCCQ" value="{{ !empty($isCCQ) ? 1 : 0 }}">
        </div>
         </div>


<div class="white-panel">
        <div class="table-responsive">
            <table id="employeeTable" class="table table-borderless text-center align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Nom employé</th>
                        <th>Poste</th>
                        <th>Heures travaillées/an</th>
                        <th>Nombre de semaine travaillées</th>
                        <th>Taux de vacance (%)</th>
                        <th>Taux horaire de base ($/h)</th>
                        <th>Salaire annuel de base ($)</th>    <!-- heur travaillé*taux horaire de base  --> 
                        <th>Fonds retraite ($/an)</th>
                        <th>Boni ($/an)</th>
                        <th>Assurance groupe ($/an)</th>
                        <th>Autres avantages ($/h)</th>   <!-- (Fonds retraite+Boni+Assurance groupe )/Heures travaillées-->
                        <th>Vacances payées ($/h)</th>   <!-- Taux de vacance*Taux horaire de base -->
                        <th>Congé payé ($/h)</th>   <!-- (taux horaire base*jour ferié((heure travaillé*nb semaine/5)/heur travaillé)) -->
                        <th>Taux horaire corrigé ($/h)</th>   <!--Taux Horaire de base+Autres avantages +Vacances payées+Congé payé -->
                        <th>RRQ ($/h)</th>   <!-- RRQ=SI(Nom de l'employé="";"";SI(Taux horaire corrigé=0;"";SI((Taux horaire corrigé*heures travaillée anuelement)<Exemption de base de RRQ :;"";SI((Taux horaire corrigé*heures travaillée anuelement)>Maximum des gains cotisable ;Cotisation maximale RRQ /heures travaillée anuelement;Taux horaire corrigé*heures travaillée anuelement-Exemption de base de RRQ )/heures travaillée anuelement*Taux de cotisation RRQ ))))
 -->
                        <th>AE ($/h)</th>   <!-- AE=SI(Nom de l'employé="";"";SI(Taux horaire corrigé=0;"";SI(Taux horaire corrigé*Taux de l'employé*Part de l'employeur*heures travaillée anuelement>Cotisation maximale AE de l'employeur ;Cotisation maximale AE de l'employeur /heures travaillée anuelement;Taux horaire corrigé*Taux de l'employé*Part de l'employeur)) -->
                        <th>RQAP ($/h)</th><!--RQAP=SI(Nom de l'employé="";"";SI(Taux horaire corrigé=0;"";SI((Taux horaire corrigé*heures travaillée anuelement)>Salaire Max assurable RQAP;Cotisation maximale au RQAP/heures travaillée anuelement;(Taux horaire corrigé*heures travaillée anuelement)/heures travaillée anuelement*Taux Employeur RQAP(%))))-->
                        {{-- CSST seulement en modèle standard --}}
                        @unless((int)($isCCQ ?? 0) === 1)
                        <th>CSST ($/h)</th>
                         @endunless<!--CSST=SI(Nom de l'employé="";"";SI(Taux horaire corrigé=0;"";Taux horaire corrigé*Taux CSST (%)))-->
                         <th>FSSQ ($/h)</th><!--FSSQ=SI(Nom de l'employé="";"";SI(Taux horaire corrigé=0;"";Taux horaire corrigé*Taux FSSQ (%)))-->
                        {{-- 🔻 Colonnes CCQ insérées ENTRE FSSQ et CNT quand modèle CCQ --}}
                          @if((int)($isCCQ ?? 0) === 1)
                          <th class="col-ccq">Avantages Sociaux ($/h)</th>
                          <th class="col-ccq">Taxes assurances($/h)</th>
                          <th class="col-ccq">CCQ ($/h)</th>
                          <th class="col-ccq">AECQ ($/h)</th>
                          <th class="col-ccq">Fonds divers ($/h)</th>
                          <th class="col-ccq">Équip. sécurité ($/h)</th>
                          <th class="col-ccq">Clauses normatives ($/h)</th>
                          @endif
                         {{-- 🔺 Fin CCQ --}}
                        
                         {{-- CNT renommé en CNESST coté UI si CCQ (le name reste "cnt") --}}
                          @if((int)($isCCQ ?? 0) === 1)
                           <th>CNESST ($/h)</th>
                           @else
                           <th>CNT ($/h)</th>
                          @endif <!--CNT=SI(Nom de l'employé="";"";SI(Taux horaire corrigé=0;"";SI((Taux horaire corrigé*heures travaillée anuelement)>Cotisation maximale au CNT ;Cotisation maximale au CNT /heures travaillée anuelement;(Taux horaire corrigé*Taux CNT (%)))))--> 
                        <th>Autres bénéfices avantages($)</th>
                        <th>Taux avant pauses ($/h)</th><!--Taux horaire corrigé+RRQ+AE+RQAP+CSST+FSSQ+CNT-->
                        <th>Coût annuel total ($)</th><!--Coût Annuel Total=SI(Nom de l'employé="";"";SI(Taux avant pauses, congés et temps mort="";"";(Taux avant pauses, congés et temps mort)*heures travaillée anuelement))-->
                        <th>Dividende et autres avantages non imposables</th>
                        <th>Pause (min/h)</th><!--Pauses en minutes/heure=pourcentage_pause*60-->
                        <th>Temps mort (min/h)</th><!--Temps mort en minutes/heure=pourcentage_temps_mort*60-->
                        <th>Total non productif (min/h)</th><!--Total minutes non productives=Pauses en minutes+Temps mort en minutes-->
                        <th>Temps productif (min/h)</th><!--Temps productif par heure=60-Total minutes non productives-->
                        <th>% temps productif</th><!--% de temps productif=Temps productif par heure/60-->
                        <th>Taux avec fardeau ($/h)</th><!--Taux Avec Fardeau=SINom de l'employé ="";"";SI(Taux horaire corrigé="";"";(Taux avant pauses, congés et temps mort/Temps productif par heure*60))-->
                        <th>Fardeau (%)</th>
                        <th>Date d'embauche</th>
                        <th>Ancienneté (années)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach($employees as $employee)
                       @include('user.fardeauMO.partials.employee-row', ['employee' => $employee, 'isCCQ' => $isCCQ])

                    @endforeach
                </tbody>
                <tfoot>
  <tr class="bg-light fw-bold">
    <td class="text-start">Totaux :</td>   <!-- 1: libellé -->
    <td></td>                               <!-- 2 -->
    <td class="text-end" id="sum-hours_worked_annual">0.00</td>  <!-- 3 -->
    <td></td>                               <!-- 4 -->
    <td></td>                               <!-- 5 -->
    <td></td>                               <!-- 6 -->
    <td class="text-end" id="sum-annual_salary">0.00</td>        <!-- 7 -->
    <td></td>                               <!-- 8 -->
    <td></td>                               <!-- 9 -->
    <td></td>                               <!-- 10 -->
    <td></td>                               <!-- 11 -->
    <td></td>                               <!-- 12 -->
    <td></td>                               <!-- 13 -->
    <td></td>                               <!-- 14 -->
    <td></td>                               <!-- 15 -->
    <td></td>                               <!-- 16 -->
    <td></td>                               <!-- 17 -->
    <td></td>                               <!-- 18 -->
    <td></td>                               <!-- 19-->
    <td></td>                               <!-- 20 -->
    <td></td>                               <!-- 21 -->
    <td></td>                               <!-- 22 -->
    <td></td>                               <!-- 23 -->
    <td></td>                               <!-- 24 -->
    <td></td>    <!-- 25 -->
    <td></td>                               <!-- 26 -->
    <td></td>                               <!-- 27 -->
    <td></td>                               <!-- 28 -->
    <td class="text-end" id="sum-total_annual_cost">0.00</td>   <!-- 29 -->
    <td></td>                               <!-- 30 -->
    <td></td>                               <!-- 31 -->
    <td></td>                               <!-- 32 -->
    <td></td>                               <!-- 33 -->
    <td></td>                               <!-- 34 -->
    <td></td>                               <!-- 35 -->
    <td></td>                               <!-- 36 -->
    <td></td>                               <!-- 37 -->
    <td></td>                               <!-- 38 -->
    <td></td>                               <!-- 39 -->
    <td></td>                               <!-- 40 (Actions) -->
  </tr>
</tfoot>

            </table>
        </div>

        {{-- Template pour ajout dynamique --}}
        <template id="employeeRowTemplate">
    <tr>
    <td>
    <input type="text"
           class="form-control form-control-sm"
           name="employee_name"
           list="usersList"
           placeholder="Nom employé (ou taper un nouveau)">
</td>
    <td><input type="text" class="form-control form-control-sm" name="position"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="hours_worked_annual"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="weeks_worked"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="vacation_rate"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="hourly_rate"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="annual_salary"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="retirement_fund"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="bonus"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="group_insurance"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="other_benefits_hourly"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="paid_vacation"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="paid_leave"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="adjusted_hourly_rate"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rrq"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="ae"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rqap"></td>
    @unless((int)($isCCQ ?? 0) === 1)
     <td><input type="number" step="0.01" class="form-control form-control-sm" name="csst"></td>
    @endunless

    <td><input type="number" step="0.01" class="form-control form-control-sm" name="fssq"></td>

    @if((int)($isCCQ ?? 0) === 1)
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="avantages_sociaux"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="taxes_assurance"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="ccq"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="aecq"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="fonds_divers"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="equipement_securite"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="clauses_normatives"></td>
   @endif

<td><input type="number" step="0.01" class="form-control form-control-sm" name="cnt"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="other_benefits"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rate_before_downtime"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="total_annual_cost"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="non_taxable_dividends"></td>
    <td><input type="number" step="0.1" class="form-control form-control-sm" name="breaks_per_hour"></td>
    <td><input type="number" step="0.1" class="form-control form-control-sm" name="idle_time_per_hour"></td>
    <td><input type="number" step="0.1" class="form-control form-control-sm" name="total_non_productive_time"></td>
    <td><input type="number" step="0.1" class="form-control form-control-sm" name="productive_time"></td>
    <td><input type="number" step="0.1" class="form-control form-control-sm" name="productive_time_percentage"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="rate_with_burden"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm" name="burden_percentage"></td>
    
    @php
    // Si pour une raison X la valeur est une string ou "0000-00-00", on neutralise.
    $hire = null;
    try {
        if ($employee->hire_date instanceof \Carbon\Carbon) {
            $hire = $employee->hire_date;
        } elseif (!empty($employee->hire_date) && $employee->hire_date !== '0000-00-00') {
            $hire = \Carbon\Carbon::parse($employee->hire_date);
        }
    } catch (\Exception $e) {
        $hire = null;
    }
@endphp

<td>
  <input type="date"
         class="form-control form-control-sm"
         name="hire_date"
         value="{{ old('hire_date', optional($hire)->format('Y-m-d')) }}">
</td>

    <td><input type="number" step="0.01" class="form-control form-control-sm" name="seniority"></td>
    <td>
        
  <div class="d-flex justify-content-center gap-1">
    <button class="btn btn-danger btn-sm btn-delete-employee" title="Supprimer">
      <i class="fas fa-trash"></i>
    </button>
  </div>

    </td>
        </tr>
    </template>
    </div>
</div>
</div>

<div class="white-panel">
<div class="recap-section mt-3">
  <div class="recap-container">
    <div class="card recap-card"> 
<div class="recap-title">Récapitulatif</div>
 <div class="recap-wrap">
    <div class="table-responsive">
 
 
          <table class="table table-borderless text-end align-middle recap-table">

        
          <tbody>
          <!--  <tr><td>Heures travaillées</td><td class="text-end"><span id="recap-total_heures">0.00</span> h</td></tr> --> 
            <tr><td><strong>Salaire annuel de base</strong></td><td class="text-end"><strong><span id="recap-salaire_total">0.00</span> $</strong></td></tr>
            <tr><td>Vacances payées</td><td class="text-end"><span id="recap-vacances_total">0.00</span> $</td></tr>
            <tr><td>Avantages sociaux (autres $/h)</td><td class="text-end"><span id="recap-avantages_sociaux_total">0.00</span> $</td></tr>`
            @if((int)($isCCQ ?? 0) === 1)
            <tr>
            <td>CCQ </td>
            <td class="text-end"><span id="recap-ccq_total">0.00</span> $</td>
           </tr>
           @endif

            <tr><td>RRQ</td><td class="text-end"><span id="recap-rrq_total">0.00</span> $</td></tr>
            <tr><td>AE</td><td class="text-end"><span id="recap-ae_total">0.00</span> $</td></tr>
            <tr><td>RQAP</td><td class="text-end"><span id="recap-rqap_total">0.00</span> $</td></tr>
            <tr><td>CNT</td><td class="text-end"><span id="recap-cnt_total">0.00</span> $</td></tr>
            <tr><td>FSSQ</td><td class="text-end"><span id="recap-fssq_total">0.00</span> $</td></tr>
            <tr><td>CSST</td><td class="text-end"><span id="recap-csst_total">0.00</span> $</td></tr>
            

            <tr><td>Boni</td><td class="text-end"><span id="recap-boni_total">0.00</span> $</td></tr>
            <tr><td>Assurance Groupe</td><td class="text-end"><span id="recap-assurance_groupe_total">0.00</span> $</td></tr>

            <tr class="border-top">
              <td><strong>Total général</strong></td>
              <td class="text-end"><strong><span id="recap-total_general">0.00</span> $</strong></td>
            </tr>

           <!-- <tr class="border-top">
              <td><strong>Coût annuel total (somme)</strong></td>
              <td class="text-end"><strong><span id="recap-cout_total">0.00</span> $</strong></td>
            </tr> -->
          </tbody>
        </table>
      </div>
</div>
    </div>
  </div>
</div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
  // ====== TomSelect (inchangé, juste rangé) ======
  window.usersOptions = @json($users->map(fn($u)=>['value'=>$u->id,'text'=>$u->name]));
  function initEmployeeSelect(row){
    const select = row.querySelector('select[name="employee_token"]');
    const hidden = row.querySelector('input[name="employee_name"]');
    if(!select || select.tomselect) return;

    select.innerHTML = '';
    (window.usersOptions || []).forEach(opt=>{
      const o = document.createElement('option');
      o.value = opt.value; o.textContent = opt.text;
      select.appendChild(o);
    });

    const ts = new TomSelect(select, {
      create: true,
      persist: false,
      maxItems: 1,
      valueField: 'value',
      labelField: 'text',
      searchField: 'text',
      placeholder: 'Choisir ou saisir un employé…',
      onInitialize(){
        const name = row.dataset.employeeName?.trim();
        if(name){
          const match = (window.usersOptions||[]).find(o => o.text === name);
          if(match){
            this.addItem(match.value);
            hidden.value = match.text;
          }else{
            this.addOption({value:name, text:name});
            this.addItem(name);
            hidden.value = name;
          }
        }
      },
      onChange(value){
        const isId = /^\d+$/.test(String(value));
        if(isId){
          const opt = (window.usersOptions||[]).find(o => String(o.value)===String(value));
          hidden.value = opt ? opt.text : '';
        }else{
          hidden.value = value || '';
        }
      }
    });
  }
  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('#employeeTable tbody tr').forEach(initEmployeeSelect);
  });
</script>

<script>
  // ====== Constantes venant du backend (OK) ======
  window.contributionRates = {
    rrq: {
      rate: {{ $constants->taux_de_cotisation_rrq ?? 0 }}/100,
      exemption: {{ $constants->rrq_exemption ?? 0 }},
      max: {{ $constants->rrq_max_salary ?? 0 }}
    },
    ae:  
    { rate: {{ $constants->ae_rate_employer ?? 0 }}, 
    rate_employee:{{ $constants->ae_rate_employee ?? 0 }}/100, 
     max: {{ $constants->ae_max_salary ?? 0 }} ,
    max_contrib_employer : {{ $constants->ae_max_employer ?? 0 }} },

    rqap:{ rate: {{ $constants->rqap_rate_employee ?? 0 }}/100, max: {{ $constants->rqap_max_salary ?? 0 }} },
    csst:{ rate: {{ $csstContribution?->csst_rate ?? 0 }}/100 },
    fssq:{ rate: {{ $constants->fss_rate ?? 0 }}/100 },
    
    cnt: { 
    rate: {{ $constants->cnt_rate ?? 0 }}/100,
    max: {{ $constants->cnt_max_salary ?? 0 }} ,
    max_contrib_employee:{{ $constants->cnt_max_contribution ?? 0 }} 
  
  },
    
    
  };
  window.constantsDebug = @json($constants);
  console.log('🔎 Constants from Laravel:', window.constantsDebug);
</script>

<script>
  // ====== Verrouiller les champs calculés (read-only) ======
  function lockComputedFields(row){
    const fields = [
      'annual_salary','other_benefits_hourly','paid_vacation','paid_leave',
      'adjusted_hourly_rate','rrq','ae','rqap','csst','fssq','cnt',
      'rate_before_downtime','total_annual_cost',
      'breaks_per_hour','idle_time_per_hour','total_non_productive_time',
      'productive_time','productive_time_percentage',
      'rate_with_burden','burden_percentage','seniority'
    ];
    fields.forEach(n => {
      const el = row.querySelector(`[name="${n}"]`);
      if (el) { el.readOnly = true; el.classList.add('bg-light'); }
    });
  }
  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('#employeeTable tbody tr').forEach(lockComputedFields);
  });
</script>

<script>
  // ====== Calcul d’une ligne employé (corrigé) ======
  function calculateEmployeeRow(input) {
    const row = input.closest('tr');
    if (!row) return;

    // Helpers
    const get = name => {
      const el = row.querySelector(`[name="${name}"]`);
      if (!el) return 0;
      const raw = (el.value || "").toString().replace(/\s/g, '').replace(',', '.');
      const n = parseFloat(raw);
      return isFinite(n) ? n : 0;
    };
    const set = (name, value) => {
      const el = row.querySelector(`[name="${name}"]`);
      if (!el) return;
      if (value === null || typeof value === 'undefined' || isNaN(value)) {
        el.value = '';
      } else {
        el.value = Number(value).toFixed(2);
      }
    };

    // Entêtes
    const joursFeries         = parseFloat(document.querySelector('[name="jours_feries"]')?.value || 0);
    const pourcentagePause    = parseFloat(document.querySelector('[name="pourcentage_pause"]')?.value || 0);
    const pourcentageTempsMort= parseFloat(document.querySelector('[name="pourcentage_temps_mort"]')?.value || 0);

    // Données employé
    const employeeName = (row.querySelector('[name="employee_name"]')?.value || '').trim();
    const hours        = get('hours_worked_annual');
    const weeks        = get('weeks_worked') || 52;
    const hourlyRate   = get('hourly_rate');
    const vacationRate = get('vacation_rate');
    const retirement   = get('retirement_fund');
    const bonus        = get('bonus');
    const insurance    = get('group_insurance');
    const dividends    = get('non_taxable_dividends');

    // 1) Salaire annuel
    const annualSalary = hours * hourlyRate;
    set('annual_salary', annualSalary);

    // 2) Autres avantages ($/h) = (retraite + boni + assurance)/heures
    const otherHourly = hours > 0 ? (retirement + bonus + insurance) / hours : 0;
    set('other_benefits_hourly', otherHourly);

    // 3) Vacances payées ($/h)
    const paidVacation = hourlyRate * (vacationRate / 100);
    set('paid_vacation', paidVacation);

    // 4) Congé payé ($/h)
    const paidLeave = (hourlyRate *  joursFeries * (((hours/weeks)/5)/hours));
    set('paid_leave', paidLeave);

    // 5) Taux horaire corrigé
    const adjustedRate = hourlyRate + get('other_benefits_hourly') + get('paid_vacation') + paidLeave;
    set('adjusted_hourly_rate', adjustedRate);

    // ===== Cotisations par heure =====
    const contrib = window.contributionRates || {};

    // petit helper: écrire en 4 décimales (ou vide)
    const set4 = (name, v) => {
      const el = row.querySelector(`[name="${name}"]`);
      if (!el) return;
      if (v === null || typeof v === 'undefined' || isNaN(v)) el.value = '';
      else el.value = Number(v).toFixed(2);
    };

    
// ===== Cotisations par heure (mêmes noms, formules corrigées) =====
const A = adjustedRate;          // Taux horaire corrigé
const H = hours;                 // Heures annuelles
const G = A * H;                 // Gains annuels

// --- RRQ ($/h) ---
let rrq = null;
{
  const cfg = contrib.rrq || { rate:0, exemption:0, max:0 };
  const rate = Number(cfg.rate || 0);           // décimal
  const ex   = Number(cfg.exemption || 0);
  const max  = Number(cfg.max || 0);

  if (A > 0 && H > 0) {
    if (G < ex) {
      rrq = null;                                // ""
    } else if (max && G > max) {
      rrq = ((max - ex) * rate) / H;            // cotisation max / H
    } else {
      rrq = ((G - ex) * rate) / H;              // ((G - ex)/H) * rate
    }
  }
}
set4('rrq', rrq);

// --- AE ($/h) ---
let ae = null;
{
  const cfg = contrib.ae || { rate:0, max:0 };
  const rate = Number(cfg.rate || 0);           // taux employeur
  const rate_employee = Number(cfg.rate_employee || 0); // taux employee
  const max  = Number(cfg.max || 0);
  const max_contrib_employer = Number(cfg.max_contrib_employer || 0);

const ann = A *rate_employee* rate * H; 
  if (A > 0 && H > 0) {
    if (ann>max_contrib_employer ) {
    ae=max_contrib_employer/H;  
    }else{
      ae=A*rate*rate_employee;
      }
    
  }
}
set4('ae', ae);

// --- RQAP ($/h) ---
let rqap = null;
{
  const cfg = contrib.rqap || { rate:0, max:0 };
  const rate = Number(cfg.rate || 0);           // taux employeur
  const max  = Number(cfg.max || 0);

  if (A > 0 && H > 0) {
    rqap = (max && G > max) ? (rate * max) / H  // cotisation max / H
                            : A * rate;         // (G/H)*rate
  }
}
set4('rqap', rqap);

// --- CSST ($/h) ---
let csst = null;
{
  if (A > 0) {
    const cfg = contrib.csst || { rate:0 };
    const rate = Number(cfg.rate || 0);
    csst = A * rate;
  }
}
set4('csst', csst);

// --- FSSQ ($/h) ---
let fssq = null;
{
  const cfg = contrib.fssq || { rate:0 };
  const rate = Number(cfg.rate || 0);
  if (A > 0) fssq = A * rate;
}
set4('fssq', fssq);

// --- CNT ($/h) ---
// --- CNT ($/h) ---
let cnt = null;
{
  const rate      = Number((contrib.cnt?.rate) || 0);
const max = Number((contrib.cnt?.max)  || 0);  // <= salaire max
const max_contrib_employee =Number((contrib.cnt?.max_contrib_employee)  || 0);


  if (A > 0 && H > 0) {
  
    if (G > max_contrib_employee) {
      // plafond: (taux * salaire max) / heures = $/h
      cnt = max_contrib_employee / H;
    } else {
      // sinon: taux * taux horaire corrigé = $/h
      cnt = A * rate;
    }
  } else {
    cnt = null; // champ vide si pas de données
  }
}
set4('cnt', cnt);

// --- Taux avant pauses (somme des variables locales) ---
// Somme des colonnes CCQ ($/h) si on est en mode CCQ
const ccqExtras = window.isCCQ
  ? (
      get('avantages_sociaux') +
      get('taxes_assurance') +
      get('ccq') +
      get('aecq') +
      get('fonds_divers') +
      get('equipement_securite') +
      get('clauses_normatives')
    )
  : 0;

// Base commune (sans CSST si CCQ car la colonne n’existe pas en CCQ)
const baseBefore =
  A + (rrq || 0) + (ae || 0) + (rqap || 0) + (fssq || 0) + (cnt || 0) 

// En CCQ on ajoute simplement toutes les colonnes CCQ
const rateBeforeDowntime = baseBefore + ccqExtras;

set('rate_before_downtime', rateBeforeDowntime);


    // 8) Coût annuel total
    set('total_annual_cost', rateBeforeDowntime * hours);

    // 9) Pauses & temps mort
    const breaksPerHour = (pourcentagePause * 60)/100;
    const idleTimePerHour = (pourcentageTempsMort * 60)/100;
    const totalNonProductive = breaksPerHour + idleTimePerHour;
    const productiveTime = 60 - totalNonProductive;
    const productivePercentage = (productiveTime / 60) * 100;

    set('breaks_per_hour', breaksPerHour);
    set('idle_time_per_hour', idleTimePerHour);
    set('total_non_productive_time', totalNonProductive);
    set('productive_time', productiveTime);
    set('productive_time_percentage', productivePercentage);

    // 10) Taux avec fardeau
    const dividendHourly = hours ? dividends / hours : 0;
    const rateWithBurden = productiveTime > 0 ? (rateBeforeDowntime / productiveTime) * 60 + dividendHourly : 0;
    set('rate_with_burden', rateWithBurden);

    // 11) Fardeau (%)
    const baseForBurden = hourlyRate + dividendHourly;
    
    const burdenPercentage =(rateWithBurden/(adjustedRate+(dividends /hours))-1)*100;
    set('burden_percentage', burdenPercentage);

    // 12) Ancienneté
    const hireDate = row.querySelector('[name="hire_date"]')?.value;
    if (hireDate) {
      const hireYear = new Date(hireDate).getFullYear();
      const currentYear = new Date().getFullYear();
      set('seniority', currentYear - hireYear);
    }
  }

  // Écoute globale : recalcul live
  document.addEventListener('input', (e) => {
    if (e.target.closest('#employeeTable')) {
      calculateEmployeeRow(e.target);
    }
  });

  // Calcul initial des lignes existantes
  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('#employeeTable tbody tr input').forEach(inp=>{
      calculateEmployeeRow(inp);
    });
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    window.isCCQ = (document.getElementById('isCCQ')?.value === '1');
  });
</script>
<script>
  // ====== Ajout de ligne (ordre corrigé, pas de pré-remplissage qui écrase) ======
  document.getElementById('addRowBtn').addEventListener('click', () => {
    const tbody = document.querySelector('#employeeTable tbody');
    const template = document.getElementById('employeeRowTemplate').content;
    const clone = document.importNode(template, true);
    tbody.appendChild(clone);

    const row = tbody.lastElementChild;     // ✅ d’abord on définit row
    initEmployeeSelect(row);                 // ✅ ensuite on l’utilise
    lockComputedFields(row);                 // ✅ rendre calculés en read-only

    // Listeners
    row.querySelectorAll('input, select').forEach(input => {
      input.addEventListener('input', () => calculateEmployeeRow(input));
    });

    // Premier calcul
    const firstInput = row.querySelector('input') || row;
    calculateEmployeeRow(firstInput);
  });
</script>

<script>
document.addEventListener('click', async e => {
        if (e.target.closest('.btn-save-entete')) {
            const row = e.target.closest('tr');

            const data = {};

            row.querySelectorAll('input').forEach(input => {
                if (input.name) data[input.name] = input.value;
            });

            data.id = row.dataset.id !== 'new' ? row.dataset.id : null;
            
            // add the type id to data
            const type = new URLSearchParams(window.location.search).get("type");
            data.operation_type_id = type;
            
            try {
                const res = await fetch(`{{ route('user.fardeauMO.entetes.storeAjax') }}?type=${type}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const text = await res.text();

                let json;
                try {
                    json = JSON.parse(text);
                } catch (parseError) {
                    console.error('❌ Réponse non JSON :', text);
                    alert('❌ Erreur serveur (réponse invalide)');
                    return;
                }

                if (res.ok && json.success) {
                    row.dataset.id = json.id;
                    alert('✅ Entête enregistrée avec succès !');
                } else if (res.status === 422 && json.errors) {
                    const messages = Object.values(json.errors).flat().join('\n');
                    alert('❌ Erreurs de validation :\n' + messages);
                } else {
                    console.warn('❌ Erreur inconnue :', json.message || text);
                    alert('❌ Une erreur est survenue : ' + (json.message || 'Erreur inconnue'));
                }

            } catch (err) {
                console.error('❌ Erreur AJAX', err);
                alert('❌ Impossible de communiquer avec le serveur');
            }
        }
    });
  // ====== Récap & totaux (inchangé) ======
(function(){
  const table = document.getElementById('employeeTable');
  const tbody = table?.querySelector('tbody');

  function num(v){ const n = parseFloat(String(v).replace(',','.')); return isNaN(n) ? 0 : n; }
  function fmt(n){ return Number(n).toFixed(2); }

  // Somme simple d’une colonne (valeurs brutes du champ)
  function sumInputsByName(name){
    let s = 0;
    tbody?.querySelectorAll(`tr [name="${name}"]`).forEach(inp => { s += num(inp.value); });
    return s;
  }

  // Somme annuelle: Σ( valeur $/h * heures )
  function sumAnnualByName(namePerHour){
    let s = 0;
    tbody?.querySelectorAll('tr').forEach(tr => {
      const h = num(tr.querySelector('[name="hours_worked_annual"]')?.value);
      const v = num(tr.querySelector(`[name="${namePerHour}"]`)?.value);
      s += h * v;
    });
    return s;
  }

  function recalcTotalsUI(){
    if (!tbody) return;
const IS_CCQ = document.getElementById('isCCQ')?.value === '1';
    // Totaux “simples”
    const sHours = sumInputsByName('hours_worked_annual');     // heures
    const sSal   = sumInputsByName('annual_salary');           // $ (déjà annuel)
    const sCost  = sumInputsByName('total_annual_cost');       // $ (déjà annuel)
// CCQ total (hors "avantages_sociaux")
    let ccq_total = 0;
    if (IS_CCQ) {
      ccq_total =
        sumAnnualByName('taxes_assurance') +
        sumAnnualByName('ccq') +
        sumAnnualByName('aecq') +
        sumAnnualByName('fonds_divers') +
        sumAnnualByName('equipement_securite') +
        sumAnnualByName('clauses_normatives');
    }
    // Totaux $/h -> $ annuels
    const rrq_total   = sumAnnualByName('rrq');
    const ae_total    = sumAnnualByName('ae');
    const rqap_total  = sumAnnualByName('rqap');
    const cnt_total   = sumAnnualByName('cnt');
    const fssq_total  = sumAnnualByName('fssq');
    const csst_total  = sumAnnualByName('csst'); // 0 si colonne absente

    // Vacances ($/h -> $ annuels) + Congé payé ($/h -> $ annuels)
    const vacances_total    = sumAnnualByName('paid_vacation');
    const conge_paye_total  = sumAnnualByName('paid_leave');

    // 🔁 NOUVELLE DÉFINITION: Avantages Sociaux (ADMIN)
    // = SOMMEPROD(M:C) + SOMME(G36:G40)
    // => congé payé + RRQ + AE + RQAP + CNT + FSSQ (+ CSST si pas CCQ)
  let avantages_sociaux_total;

if (!window.isCCQ) {
  // MODE STANDARD
  // = congé payé + RRQ + AE + RQAP + CNT + FSSQ (+ CSST si tu l’inclus côté standard)
  avantages_sociaux_total =
      conge_paye_total
    + rrq_total
    + ae_total
    + rqap_total
    + cnt_total
    + fssq_total; // ← retire cette ligne si tu ne veux pas CSST dans ce total
} else {
  // MODE CCQ
  // = SOMMEPROD(colonne "avantages_sociaux" CCQ ; heures)
  avantages_sociaux_total = sumAnnualByName('avantages_sociaux'); // ⚠️ sans espace !
}

    // Déjà annuels par ligne
    const boni_total             = sumInputsByName('bonus');
    const assurance_groupe_total = sumInputsByName('group_insurance');

    // Écrire dans le footer
    const elH = document.getElementById('sum-hours_worked_annual');
    const elS = document.getElementById('sum-annual_salary');
    const elC = document.getElementById('sum-total_annual_cost');
    if (elH) elH.textContent = fmt(sHours);
    if (elS) elS.textContent = fmt(sSal);
    if (elC) elC.textContent = fmt(sCost);

    // Récap (en $ annuels)
    const total_general =
        sSal +
        vacances_total +
        avantages_sociaux_total +
        boni_total +
        assurance_groupe_total+ccq_total;

    const recapMap = {
      total_heures: sHours,
      salaire_total: sSal,
      cout_total: sCost,

      vacances_total,
      avantages_sociaux_total,   // ⬅️ mis à jour
      rrq_total,
      ae_total,
      rqap_total,
      cnt_total,
      fssq_total,
      csst_total,
      ccq_total,
  
      boni_total,
      assurance_groupe_total,
      total_general
    };

    Object.entries(recapMap).forEach(([k,v])=>{
      const el = document.getElementById('recap-' + k);
      if (el) el.textContent = fmt(v || 0);
    });
  }

  document.addEventListener('input', (e) => {
    if (e.target.closest('#employeeTable')) recalcTotalsUI();
  });
  document.addEventListener('DOMContentLoaded', recalcTotalsUI);
})();

  document.addEventListener('DOMContentLoaded', () => {

        // Fonction pour mettre à jour les pourcentages dans chaque ligne
        function updatePercentagesForRow(row) {
            const pause = parseFloat(row.querySelector('[name="minutes_pause"]')?.value || 0);
            const mort = parseFloat(row.querySelector('[name="minutes_temps_mort"]')?.value || 0);
            const totalMin = 8 * 60;

            const pausePct = (pause / totalMin) * 100;
            const mortPct = (mort / totalMin) * 100;

            const pauseField = row.querySelector('[name="pourcentage_pause"]');
            const mortField = row.querySelector('[name="pourcentage_temps_mort"]');
            if (pauseField) pauseField.value = pausePct.toFixed(2);
            if (mortField) mortField.value = mortPct.toFixed(2);
        }

        // Fonction principale de mise à jour des totaux
        function updateEnteteTotals() {
            let totalJoursFeries = 0;
            let totalMinutesPause = 0;
            let totalMinutesTempsMort = 0;
            let totalPourcentagePause = 0;
            let totalPourcentageTempsMort = 0;
            let count = 0;

            document.querySelectorAll('#enteteTable tbody tr').forEach(row => {
                updatePercentagesForRow(row); // Toujours recalculer les %
                const get = name => parseFloat(row.querySelector(`[name="${name}"]`)?.value || 0);

                totalJoursFeries += get('jours_feries');
                totalMinutesPause += get('minutes_pause');
                totalMinutesTempsMort += get('minutes_temps_mort');
                totalPourcentagePause += get('pourcentage_pause');
                totalPourcentageTempsMort += get('pourcentage_temps_mort');
                count++;
            });

            document.getElementById('totalJoursFeries').innerText = totalJoursFeries.toFixed(2);
            document.getElementById('totalMinutesPause').innerText = totalMinutesPause.toFixed(2);
            document.getElementById('totalMinutesTempsMort').innerText = totalMinutesTempsMort.toFixed(2);
            document.getElementById('totalPourcentagePause').innerText = count ? (totalPourcentagePause / count).toFixed(2) : '0';
            document.getElementById('totalPourcentageTempsMort').innerText = count ? (totalPourcentageTempsMort / count).toFixed(2) : '0';
        }

        // Écoute globale : déclenche update dès qu’un input est modifié dans le tableau
        document.querySelector('#enteteTable').addEventListener('input', () => {
            updateEnteteTotals();
        });

        // Appel initial
        updateEnteteTotals();
    });


    /* === Enregistrer tout (bulk save) === */
(function () {
  const tbody = document.querySelector('#employeeTable tbody');

  const num = (v) => {
    const n = parseFloat(String(v).replace(',', '.'));
    return isNaN(n) ? 0 : n;
  };

  function getOpType() {
    const hid = document.getElementById('operationTypeId');
    return (hid && hid.value) || new URLSearchParams(location.search).get('type') || 0;
  }

  async function saveAllRows() {
    if (!tbody) return;

    // On sérialise CHAQUE input/select/textarea de chaque tr
    const rows = [];
    tbody.querySelectorAll('tr').forEach((tr, i) => {
      const row = {};
      // garder l'id si déjà en DB (ou input hidden "id")
      const id = tr.dataset.id || tr.querySelector('input[name="id"]')?.value || null;
      if (id) row.id = id;

      tr.querySelectorAll('input, select, textarea').forEach((el) => {
        if (!el.name) return;
        // num pour type=number, sinon string
        row[el.name] = el.type === 'number' ? num(el.value) : el.value;
      });

      rows.push(row);
    });

    try {
      const res = await fetch(`{{ route('user.fardeauMO.employees.bulkSave') }}`, {
        method: 'POST',
        credentials: 'same-origin',         
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          operation_type_id: Number(getOpType() || 0),
          rows
        })
      });

      let data, text;
      try {
        data = await res.json();
      } catch {
        text = await res.text();
      }

      if (!res.ok) {
        console.error('Bulk save error:', data || text);
        const msg = (data && (data.message || data.errors)) ? JSON.stringify(data) : (text || 'Erreur serveur');
        alert('❌ Erreur à l’enregistrement\n' + msg);
        return;
      }


      // Optionnel: si l’API renvoie les ids mis à jour, on les pousse dans les <tr>
      // Attendu: data.rows = [{client_index: 0, id: 123}, ...] OU data.ids = [123,124,...]
      if (Array.isArray(data?.rows)) {
        const trs = tbody.querySelectorAll('tr');
        data.rows.forEach((r) => {
          const idx = r.client_index ?? null;
          if (idx !== null && trs[idx]) trs[idx].dataset.id = r.id;
        });
      } else if (Array.isArray(data?.ids)) {
        const trs = tbody.querySelectorAll('tr');
        data.ids.forEach((id, i) => { if (trs[i]) trs[i].dataset.id = id; });
      }

      alert('✅ Toutes les lignes ont été enregistrées');
    } catch (err) {
      console.error(err);
      alert('❌ Impossible de communiquer avec le serveur');
    }
  }

  // Bouton
  document.getElementById('saveAllBtn')?.addEventListener('click', saveAllRows);
  })();

</script>
<script>
document.addEventListener('click', async (e) => {
  const btn = e.target.closest('.btn-delete-employee');
  if (!btn) return;

  const tr  = btn.closest('tr');
  const id  = btn.dataset.id || tr?.dataset.id || null;
  const url = btn.dataset.url || (id ? "{{ route('user.fardeauMO.employees.destroy', ':id') }}".replace(':id', id) : null);

  // Si pas d'ID -> ligne jamais enregistrée => on supprime juste dans l'UI
  if (!id || !url) {
    if (confirm('Supprimer cette ligne non enregistrée ?')) {
      tr?.remove();
      // Forcer recalcul des totaux
      const first = document.querySelector('#employeeTable tbody input');
      if (first) first.dispatchEvent(new Event('input', { bubbles: true }));
    }
    return;
  }

  if (!confirm('Supprimer définitivement cet employé ?')) return;

  try {
    const res = await fetch(url, {
      method: 'DELETE', // attend une route DELETE
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    let data;
    try { data = await res.json(); } catch { data = {}; }

    if (!res.ok) {
      throw new Error(data.message || 'Suppression impossible');
    }

    // OK: on enlève la ligne et on recalcule l’UI
    tr?.remove();
    const first = document.querySelector('#employeeTable tbody input');
    if (first) first.dispatchEvent(new Event('input', { bubbles: true }));

    // optionnel: toast/alert
    // alert(data.success || 'Employé supprimé avec succès');
  } catch (err) {
    console.error(err);
    alert('❌ Erreur de suppression : ' + err.message);
  }
});
</script>

@endpush







