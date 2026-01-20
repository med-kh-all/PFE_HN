@extends('user.layouts.app')

@section('title', 'Créer des Amortissements')

@push('css')
<style>
  .tab-content{min-height:500px}
  .tab-pane{padding:15px}
  .fixed-action-bar{position:sticky;top:0;z-index:1050;background:#fff;padding:10px 0;border-bottom:1px solid #ccc}
  tfoot tr{font-weight:700;background:#f8f9fa}
</style>
@endpush

@section('content')
<div class="app-content content">
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-12">
        <div class="fixed-action-bar d-flex justify-content-between align-items-center mb-1">
          <div>
            <a href="{{ route('user.amortissements.index') }}" class="btn btn-outline-secondary mr-2" title="Retour à la liste">←</a>
            <h4 class="mb-0 d-inline">Amortissements</h4>
          </div>
          <button type="submit" form="amortissementsForm" class="btn btn-primary">Enregistrer</button>
        </div>
      </div>
    </div>

    <div class="content-body">
      <ul class="nav nav-tabs" id="yearTabs" role="tablist">
        @for ($i = $start; $i <= $end; $i++)
          <li class="nav-item">
            <a class="nav-link @if($i === $start) active @endif"
               id="year-{{ $i }}-tab"
               data-toggle="tab"
               href="#year-{{ $i }}"
               role="tab">{{ $i }}</a>
          </li>
        @endfor
      </ul>

      <form method="POST" action="{{ route('user.amortissements.store') }}" id="amortissementsForm">
        @csrf
        <input type="hidden" name="active_year" id="active_year" value="{{ old('active_year', $start) }}">
        <input type="hidden" name="start_year" value="{{ $start }}">
        <input type="hidden" name="end_year" value="{{ $end }}">

        <div class="tab-content mt-2" id="yearTabsContent">
          @for ($i = $start; $i <= $end; $i++)
            <div class="tab-pane fade @if($i === $start) show active @endif" id="year-{{ $i }}" role="tabpanel">
              <div class="card">
                <div class="card-body">
                  <h5>Année : {{ $i }}</h5>

                  <div class="table-responsive">
                    <table class="table mb-2">
                      <thead>
                        <tr>
                          <th style="width:36px;">
                            <input type="checkbox" class="check-all" data-year="{{ $i }}">
                          </th>
                          <th>Poste</th>
                          <th>Coût</th>
                          <th>Amort. Cumulé Préc.</th>
                          <th>Valeur Nette Préc.</th>
                          <th>Acquisition</th>
                          <th>Amort. Année</th>
                          <th>Amort. Mensuel</th>
                          <th>Taux %</th>
                          <th>Méthode</th>
                        </tr>
                      </thead>

                      <tbody id="rows-{{ $i }}">
                        @if(old('amortissements.' . $i))
                          @foreach(old('amortissements.' . $i) as $key => $line)
                            @includeIf('user.amortissements._row', ['i' => $i, 'key' => $key, 'line' => $line, 'start' => $start])
                          @endforeach
                        @elseif(isset($dataByYear[$i]))
                          @foreach($dataByYear[$i] as $key => $line)
                            @includeIf('user.amortissements._row', ['i' => $i, 'key' => $key, 'line' => $line, 'start' => $start])
                          @endforeach
                        @else
                          @includeIf('user.amortissements._row', ['i' => $i, 'key' => 0, 'line' => null, 'start' => $start])
                        @endif
                      </tbody>

                      <tfoot>
                        <tr>
                          <td style="width:36px;">
                            <input type="checkbox" class="row-check" data-poste="">
                          </td>
                          <td>Total</td>
                          <td class="total-cout">0</td>
                          <td class="total-cumule">0</td>
                          <td class="total-valeur">0</td>
                          <td class="total-acquisition">0</td>
                          <td class="total-amort-annee">0</td>
                          <td class="total-amort-mois">0</td>
                          <td colspan="2"></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>

                  <button type="button" class="btn btn-secondary" onclick="addRow({{ $i }})">
                    Ajouter une ligne
                  </button>
                  <button type="button" class="btn btn-danger ml-1" onclick="deleteSelected()">Supprimer</button>
                </div>
              </div>
            </div>
          @endfor
        </div>
      </form>
    </div>
  </div>
</div>

@push('js')
<script>
// ====== bornes de la période ======
const START_YEAR = {{ $start }};
const END_YEAR   = {{ $end }};
let CHAIN_UPDATING = false;

/* ===== Utils ===== */
function nval(el){ return parseFloat(el?.value) || 0 }
function setNum(el, v){ if(el) el.value = isFinite(v) ? Number(v).toFixed(2) : '' }
function yearFromPane(p){return parseInt(p.id.replace('year-',''),10)}
function getBaseYear(){const f=document.querySelector('.nav-tabs li:first-child a');return parseInt(f.textContent,10)}
function findRowByPoste(tbody,poste){
  return Array.from(tbody.querySelectorAll('tr')).find(tr=>{
    const inp=tr.querySelector('input[name*="[poste]"]');
    return inp && inp.value.trim()===(poste||'').trim()
  })||null
}

/* ===== Recalc row ===== */
function recalculateRow(row){
  const cout   = parseFloat(row.querySelector('[name*="[cout]"]')?.value) || 0;
  const cumule = parseFloat(row.querySelector('[name*="[amort_cumule_anterieur]"]')?.value) || 0;
  const acq    = parseFloat(row.querySelector('[name*="[acquisition_annee]"]')?.value) || 0;
  const taux   = parseFloat(row.querySelector('[name*="[taux]"]')?.value) || 0;
  const meth   = row.querySelector('[name*="[type_amortissement]"]')?.value || 'D';

  const valeur = cout - cumule;
  let amort = 0;
  if(meth==='L'){ amort=cout*(taux/100)*0.5 }
  else if(meth==='D'){ amort=(valeur+(acq/2))*(taux/100) }
  const mois=amort/12;

  const vn=row.querySelector('[name*="[valeur_nette_anterieure]"]'),
        aa=row.querySelector('[name*="[amortissement_annee]"]'),
        am=row.querySelector('[name*="[amortissement_mensuel]"]');

  if(vn) vn.value=isFinite(valeur)?valeur.toFixed(2):''
  if(aa) aa.value=isFinite(amort)?amort.toFixed(2):''
  if(am) am.value=isFinite(mois)?mois.toFixed(2):''
}

function recalcAndRead(row){
  recalculateRow(row);
  return {
    cout: nval(row.querySelector('[name*="[cout]"]')),
    amort_cumule_anterieur: nval(row.querySelector('[name*="[amort_cumule_anterieur]"]')),
    valeur_nette_anterieure: nval(row.querySelector('[name*="[valeur_nette_anterieure]"]')),
    acquisition_annee: nval(row.querySelector('[name*="[acquisition_annee]"]')),
    amortissement_annee: nval(row.querySelector('[name*="[amortissement_annee]"]')),
    amortissement_mensuel: nval(row.querySelector('[name*="[amortissement_mensuel]"]')),
    taux: nval(row.querySelector('[name*="[taux]"]')),
    type_amortissement: row.querySelector('[name*="[type_amortissement]"]')?.value || 'D'
  };
}

/* ===== Totals ===== */
function recalcTotals(year){
  const tbody=document.querySelector('#rows-'+year),
        pane=document.querySelector('#year-'+year);
  if(!tbody||!pane) return;
  let tC=0,tCu=0,tV=0,tA=0,tAn=0,tM=0;
  tbody.querySelectorAll('tr').forEach(r=>{
    tC+=parseFloat(r.querySelector('[name*="[cout]"]')?.value)||0
    tCu+=parseFloat(r.querySelector('[name*="[amort_cumule_anterieur]"]')?.value)||0
    tV+=parseFloat(r.querySelector('[name*="[valeur_nette_anterieure]"]')?.value)||0
    tA+=parseFloat(r.querySelector('[name*="[acquisition_annee]"]')?.value)||0
    tAn+=parseFloat(r.querySelector('[name*="[amortissement_annee]"]')?.value)||0
    tM+=parseFloat(r.querySelector('[name*="[amortissement_mensuel]"]')?.value)||0
  })
  pane.querySelector('.total-cout').innerText=tC.toFixed(2)
  pane.querySelector('.total-cumule').innerText=tCu.toFixed(2)
  pane.querySelector('.total-valeur').innerText=tV.toFixed(2)
  pane.querySelector('.total-acquisition').innerText=tA.toFixed(2)
  pane.querySelector('.total-amort-annee').innerText=tAn.toFixed(2)
  pane.querySelector('.total-amort-mois').innerText=tM.toFixed(2)
}

/* ===== Propagation ===== */
function getOrCreateRowFor(year, poste, seed){
  const tbody=document.getElementById('rows-'+year);
  let row=Array.from(tbody.querySelectorAll('tr')).find(tr=>{
    const p=tr.querySelector('input[name*="[poste]"]')?.value?.trim()||'';
    return p===poste;
  });
  if(row) return row;
  appendReadonlyRow(year,Object.assign({
    poste,cout:0,amort_cumule_anterieur:0,valeur_nette_anterieure:0,
    acquisition_annee:'',amortissement_annee:'',amortissement_mensuel:'',
    taux:seed?.taux||0,type_amortissement:seed?.type_amortissement||'D'
  },seed||{}));
  return Array.from(tbody.querySelectorAll('tr')).pop();
}

function updateChainFrom(year,row){
  const poste=(row.querySelector('[name*="[poste]"]')?.value||'').trim();
  if(!poste) return;
  CHAIN_UPDATING=true;
  try{
    let cur=recalcAndRead(row);
    let next_cout=cur.cout+cur.acquisition_annee;
    let next_cumule=cur.amort_cumule_anterieur+cur.amortissement_annee;
    for(let y=year+1;y<=END_YEAR;y++){
      const r=getOrCreateRowFor(y,poste,{taux:cur.taux,type_amortissement:cur.type_amortissement});
      setNum(r.querySelector('[name*="[cout]"]'),next_cout);
      setNum(r.querySelector('[name*="[amort_cumule_anterieur]"]'),next_cumule);
      const vals=recalcAndRead(r);
      next_cout=vals.cout+vals.acquisition_annee;
      next_cumule=vals.amort_cumule_anterieur+vals.amortissement_annee;
      recalcTotals(y);
    }
    recalcTotals(year);
  }finally{CHAIN_UPDATING=false;}
}

/* ===== Rows ===== */
function appendReadonlyRow(year,data){
  const tbody=document.getElementById('rows-'+year);
  const index=tbody.rows.length;
  const r=document.createElement('tr');
  r.innerHTML=`
   <td><input type="checkbox" class="row-check" data-poste=""></td>
   <td><input type="text" name="amortissements[${year}][${index}][poste]" class="form-control input-calc" value="${data.poste||''}" readonly></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][cout]" class="form-control" value="${data.cout||''}" readonly></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][amort_cumule_anterieur]" class="form-control" value="${data.amort_cumule_anterieur||''}" readonly></td>
   <td><input type="text" name="amortissements[${year}][${index}][valeur_nette_anterieure]" class="form-control" value="${data.valeur_nette_anterieure||''}" readonly></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][acquisition_annee]" class="form-control input-calc" value="${data.acquisition_annee||''}"></td>
   <td><input type="text" name="amortissements[${year}][${index}][amortissement_annee]" class="form-control" value="${data.amortissement_annee||''}" readonly></td>
   <td><input type="text" name="amortissements[${year}][${index}][amortissement_mensuel]" class="form-control" value="${data.amortissement_mensuel||''}" readonly></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][taux]" class="form-control input-calc" value="${data.taux||''}"></td>
   <td>
     <select name="amortissements[${year}][${index}][type_amortissement]" class="form-control input-calc">
       <option value="L" ${data.type_amortissement==='L'?'selected':''}>L (Linéaire)</option>
       <option value="D" ${data.type_amortissement==='D'?'selected':''}>D (Dégressif)</option>
     </select>
   </td>`;
  tbody.appendChild(r);recalculateRow(r);recalcTotals(year)
}

function addRow(year){
  const tbody=document.getElementById('rows-'+year);
  const index=tbody.rows.length;
  const isReadonly=year>parseInt(document.getElementById('active_year').value);
  const r=document.createElement('tr');
  r.innerHTML=`
   <td><input type="checkbox" class="row-check" data-poste=""></td>
   <td><input type="text" name="amortissements[${year}][${index}][poste]" class="form-control input-calc"></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][cout]" class="form-control input-calc" ${isReadonly?'readonly':''}></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][amort_cumule_anterieur]" class="form-control input-calc" ${isReadonly?'readonly':''}></td>
   <td><input type="text" name="amortissements[${year}][${index}][valeur_nette_anterieure]" class="form-control" readonly></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][acquisition_annee]" class="form-control input-calc"></td>
   <td><input type="text" name="amortissements[${year}][${index}][amortissement_annee]" class="form-control" readonly></td>
   <td><input type="text" name="amortissements[${year}][${index}][amortissement_mensuel]" class="form-control" readonly></td>
   <td><input type="number" step="0.01" name="amortissements[${year}][${index}][taux]" class="form-control input-calc"></td>
   <td>
     <select name="amortissements[${year}][${index}][type_amortissement]" class="form-control input-calc">
       <option value="L">L (Linéaire)</option>
       <option value="D">D (Dégressif)</option>
     </select>
   </td>`;
  tbody.appendChild(r);
}

/* ===== Listeners ===== */
document.getElementById('amortissementsForm').addEventListener('submit',function(){
  const active=document.querySelector('.nav-link.active').getAttribute('href');
  localStorage.setItem('activeTab',active);
  document.getElementById('active_year').value=active.replace('#year-','');
});

document.addEventListener('input', function(e){
  if(!e.target.classList.contains('input-calc')) return;
  if(CHAIN_UPDATING) return;
  const pane=e.target.closest('.tab-pane');
  const year=parseInt(pane.id.replace('year-',''),10);
   recalcAllFrom(year);
});

/* si on change le "poste", on met à jour le data-poste de la checkbox et on propage */
document.addEventListener('input', function(e){
  if (e.target.name && e.target.name.includes('[poste]')) {
    const tr = e.target.closest('tr');
    const chk = tr.querySelector('.row-check');
    if (chk) chk.setAttribute('data-poste', (e.target.value || '').trim());
    const pane = tr.closest('.tab-pane');
    const year = parseInt(pane.id.replace('year-',''),10);
    updateChainFrom(year, tr);
  }
});


document.addEventListener('shown.bs.tab',function(e){
  const targetId=e.target.getAttribute('href')
  const targetYear=parseInt(targetId.replace('#year-',''),10)
  const baseYear=getBaseYear()
  if(targetYear>baseYear) syncYearFromBase(baseYear,targetYear)
  recalcTotals(targetYear)
})

document.addEventListener('DOMContentLoaded',function(){
  const saved=localStorage.getItem('activeTab')
  if(saved){
    const trg=document.querySelector(`a[href="${saved}"]`);
    if(trg) trg.click()
  }
  document.querySelectorAll('tbody tr').forEach(recalculateRow)
  document.querySelectorAll('.tab-pane').forEach(tab=>recalcTotals(yearFromPane(tab)))
})

/* ===== recalcul de toute une année ===== */
function recalcAllFrom(year){
  const pane = document.getElementById('year-'+year);
  const rows = pane.querySelectorAll('tbody tr');
  rows.forEach(row => {
    recalculateRow(row);
    updateChainFrom(year, row);
  });
  recalcTotals(year);
}

/* ===== Suppression lignes sélectionnées ===== */
function selectedPostesInActiveTab() {
  const activePane = document.querySelector('.tab-pane.active');
  const postes = [];
  activePane.querySelectorAll('.row-check:checked').forEach(chk => {
    const poste = chk.getAttribute('data-poste') || '';
    let p = poste;
    if (!p) {
      const tr = chk.closest('tr');
      p = tr.querySelector('input[name*="[poste]"]')?.value || '';
    }
    if (p.trim()) postes.push(p.trim());
  });
  return [...new Set(postes)];
}

function removeRowsFromAllYears(postes) {
  if (!Array.isArray(postes) || postes.length === 0) return;
  document.querySelectorAll('.tab-pane tbody tr').forEach(tr => {
    const posteInput = tr.querySelector('input[name*="[poste]"]');
    const p = (posteInput?.value || '').trim();
    if (postes.includes(p)) {
      tr.remove();
    }
  });
  document.querySelectorAll('.tab-pane').forEach(pane => {
    const year = parseInt(pane.id.replace('year-',''),10);
    recalcTotals(year);
  });
}

async function deleteSelected() {
  const postes = selectedPostesInActiveTab();
  if (postes.length === 0) {
    alert('Sélectionnez au moins une ligne.');
    return;
  }
  if (!confirm(`Supprimer "${postes.join(', ')}" dans TOUTES les années ?`)) return;

  const csrf = document.querySelector('#amortissementsForm input[name=_token]').value;

  try {
    const resp = await fetch("{{ route('user.amortissements.bulkDestroy') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json'
      },
      credentials: 'same-origin',
      body: JSON.stringify({ postes })
    });

    const data = await resp.json().catch(() => ({}));

    if (!resp.ok || data.ok !== true) {
      alert('Suppression en base échouée.');
      return;
    }
    removeRowsFromAllYears(postes);

  } catch (e) {
    alert('Erreur réseau. Réessaie.');
  }
}

/* ===== Check-all par année ===== */
document.addEventListener('change', function(e){
  if (e.target.classList.contains('check-all')) {
    const pane = e.target.closest('.tab-pane') || document.querySelector('#year-'+e.target.dataset.year);
    pane.querySelectorAll('.row-check').forEach(ch => ch.checked = e.target.checked);
  }
});

/* Mise à jour du data-poste quand on tape dans "poste" */
document.addEventListener('input', function(e){
  if (e.target.name && e.target.name.includes('[poste]')) {
    const tr = e.target.closest('tr');
    const chk = tr.querySelector('.row-check');
    if (chk) chk.setAttribute('data-poste', (e.target.value || '').trim());
  }
});
</script>
@endpush

@endsection

