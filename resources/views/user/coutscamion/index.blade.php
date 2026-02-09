@extends('user.layouts.app')

@section('title', 'Coût par Camion')

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="col-12">
                <h2 class="content-header-title">Coût par Camion</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('user.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active">Coût par Camion</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">Tableau de gestion des coûts</h4>
                            <div>
                                <button id="addRow" type="button" class="btn btn-primary btn-sm">+ Ajouter une ligne</button>
                                <button id="addColumn" type="button" class="btn btn-outline-secondary btn-sm">+ Ajouter une colonne personnalisée</button>
                            </div>
                        </div>
                        <form action="{{ route('user.coutscamion.store') }}" method="POST" id="camionForm">
                            @csrf
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="camionTable" class="table table-borderless text-center align-middle">
                                        <thead class="thead-light">
                                            <tr id="tableHead">
                                                <th style="min-width: 200px">Unité</th>
                                                <th>Année de construction</th>
                                                <th>No plaque</th>
                                                <th>Responsable</th>
                                                <th>Marque</th>
                                                <th>Coût/km</th>
                                                <th>KM parcourus</th>
                                                <th>Coût/hr</th>
                                                <th>Heures</th>
                                                <th>Carburant</th>
                                                <th>Entretien</th>
                                                <th>Immat.</th>
                                                <th>Assurance</th>
                                                <th>Intérêt prêt</th>
                                                <th>Location</th>
                                                <th>Amortissement</th>
                                                @foreach($colonnesPersonnalisees as $colonne)
                                                <th data-slug="{{ $colonne }}">
                                                <span class="col-title">{{ $titresColonnesPersonnalisees[$colonne] ?? $colonne }}</span>
                                                <button type="button" class="btn btn-sm btn-link p-0 edit-col" data-slug="{{ $colonne }}" title="Modifier">
                                                  ✏️
                                                 </button>
                                                  <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-col" data-slug="{{ $colonne }}" title="Supprimer">
                                                 ❌
                                                 </button>
                                                   </th>
                                                 @endforeach

                                               
                                                <th style="min-width: 200px">Total Dépenses Directes</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                            @foreach($camions as $index => $camion)
                                                <tr>
                                                    <td><input type="text" name="unite[]" class="form-control form-control-sm" value="{{ $camion->unite }}"></td>
                                                    <td><input type="number" name="annee_de_construction[]" class="form-control form-control-sm" value="{{ $camion->annee_de_construction }}"></td>
                                                    <td><input type="text" name="no_plaque[]" class="form-control form-control-sm" value="{{ $camion->no_plaque }}"></td>
                                                    <td><input type="text" name="responsable[]" class="form-control form-control-sm" value="{{ $camion->responsable }}"></td>
                                                    <td><input type="text" name="marque[]" class="form-control form-control-sm" value="{{ $camion->marque }}"></td>
                                                    <td><input type="number" step="0.01" name="cout_km[]" class="form-control form-control-sm" value="{{ $camion->cout_km }}"></td>
                                                    <td><input type="number" step="0.01" name="km_parcourus[]" class="form-control form-control-sm" value="{{ $camion->km_parcourus }}"></td>
                                                    <td><input type="number" step="0.01" name="cout_hr[]" class="form-control form-control-sm" value="{{ $camion->cout_hr }}"></td>
                                                    <td><input type="number" step="0.01" name="heures[]" class="form-control form-control-sm" value="{{ $camion->heures }}"></td>
                                                    <td><input type="number" step="0.01" name="carburant[]" class="form-control form-control-sm" value="{{ $camion->carburant }}"></td>
                                                    <td><input type="number" step="0.01" name="entretien[]" class="form-control form-control-sm" value="{{ $camion->entretien }}"></td>
                                                    <td><input type="number" step="0.01" name="immatriculation[]" class="form-control form-control-sm" value="{{ $camion->immatriculation }}"></td>
                                                    <td><input type="number" step="0.01" name="assurance[]" class="form-control form-control-sm" value="{{ $camion->assurance }}"></td>
                                                    <td><input type="number" step="0.01" name="interet[]" class="form-control form-control-sm" value="{{ $camion->interet }}"></td>
                                                    <td><input type="number" step="0.01" name="location[]" class="form-control form-control-sm" value="{{ $camion->location }}"></td>
                                                    <td><input type="number" step="0.01" name="amortissement[]" class="form-control form-control-sm" value="{{ $camion->amortissement }}"></td>
                                                   @foreach($colonnesPersonnalisees as $colonne)
                                                     @php
                                                     $value = $camion->colonnes_personnalisees[$colonne] ?? '';
                                                      @endphp
                                                     <td>
                                                     <input type="number" step="0.01"
                                                      name="personnalise[{{ $colonne }}][]"
                                                     class="form-control form-control-sm"
                                                     value="{{ $value }}">
                                                     </td>
                                                    @endforeach

                                                    <td><input type="number" step="0.01" name="total_depenses[]" class="form-control form-control-sm" value="{{ $camion->total_depenses }}" readonly></td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <button type="submit" class="btn btn-sm rounded-circle bg-white d-flex align-items-center justify-content-center border-0" title="Sauvegarder">
                                                                <i class="fas fa-check" style="font-size: 14px; color:#5A55FF;"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-sm rounded-circle bg-white d-flex align-items-center justify-content-center border-0" title="Modifier">
                                                                <i class="fas fa-edit" style="font-size: 14px; color:#5A55FF;"></i>
                                                            </button>
                                                           <button type="button"
                                                              class="btn btn-sm rounded-circle bg-white d-flex align-items-center justify-content-center border-0"
                                                              title="Supprimer"
                                                              onclick="deleteRow(this, {{ $camion->id }})">
                                                             <i class="fas fa-trash" style="font-size: 14px; color:#5A55FF;"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr id="summaryRow" class="bg-primary text-white font-weight-bold">
                                                <!-- Les totaux seront calculés par JavaScript -->
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                             <!-- 🔹 Champ caché pour stocker slug + title -->
                              <input type="hidden" id="custom_columns" name="custom_columns" value="{{ json_encode(
                                 collect($colonnesPersonnalisees)->map(fn($c) => [
                                 'slug' => $c,
                                 'title' => $titresColonnesPersonnalisees[$c] ?? $c
                                 ])->values()
                                 ) }}">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialisation des variables
const existingCamions = @json($camions ?? []);
const existingPersonnalisees = @json($colonnesPersonnalisees ?? []);
let customColumns = (existingPersonnalisees || []).map(c => {
    return typeof c === 'object' ? c : { slug: c, title: @json($titresColonnesPersonnalisees)[c] || c };
});

// Fonction utilitaire
function sumColumn(name) {
    let sum = 0;
    document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
        sum += parseFloat(input.value) || 0;
    });
    return sum;
}

// Fonction pour créer une nouvelle ligne
function createRow(data = {}) {
    const tbody = document.getElementById("tableBody");
    const tr = document.createElement("tr");

    const fixedColumns = [
        {name: "unite", type: "text"},
        {name: "annee_de_construction", type: "number"},
        {name: "no_plaque", type: "text"},
        {name: "responsable", type: "text"},
        {name: "marque", type: "text"},
        {name: "cout_km", type: "number"},
        {name: "km_parcourus", type: "number"},
        {name: "cout_hr", type: "number"},
        {name: "heures", type: "number"},
        {name: "carburant", type: "number"},
        {name: "entretien", type: "number"},
        {name: "immatriculation", type: "number"},
        {name: "assurance", type: "number"},
        {name: "interet", type: "number"},
        {name: "location", type: "number"},
        {name: "amortissement", type: "number"}
    ];

    fixedColumns.forEach(col => {
        const td = document.createElement("td");
        const input = document.createElement("input");
        input.type = col.type;
        input.className = "form-control form-control-sm";
        input.name = `${col.name}[]`;
        input.value = data[col.name] || "";
        if (col.type === "number") input.step = "0.01";
        input.addEventListener("input", updateRowTotals);
        td.appendChild(input);
        tr.appendChild(td);
    });

    // Colonnes personnalisées
    customColumns.forEach(col => {
        const td = document.createElement("td");
        const input = document.createElement("input");
        input.type = "number";
        input.className = "form-control form-control-sm";
        input.name = `personnalise[${col.slug}][]`;
        input.value = data.colonnes_personnalisees?.[col.slug] || "";
        input.step = "0.01";
        input.addEventListener("input", updateRowTotals);
        td.appendChild(input);
        tr.appendChild(td);
    });

    // Total dépenses
    const totalTd = document.createElement("td");
    const totalInput = document.createElement("input");
    totalInput.type = "number";
    totalInput.className = "form-control form-control-sm";
    totalInput.name = "total_depenses[]";
    totalInput.value = data.total_depenses || "0.00";
    totalInput.readOnly = true;
    totalTd.appendChild(totalInput);
    tr.appendChild(totalTd);

    // Actions
    const actionsTd = document.createElement("td");
    actionsTd.className = "text-center";
    actionsTd.innerHTML = `
        <div class="d-flex justify-content-center gap-2">
            <button type="submit" class="btn btn-sm rounded-circle bg-white border-0" title="Sauvegarder">
                <i class="fas fa-check" style="font-size:14px;color:#5A55FF;"></i>
            </button>
            <button type="button" class="btn btn-sm rounded-circle bg-white border-0" title="Modifier">
                <i class="fas fa-edit" style="font-size:14px;color:#5A55FF;"></i>
            </button>
            <button type="button"
        class="btn btn-sm rounded-circle bg-white d-flex align-items-center justify-content-center border-0"
        title="Supprimer"
        onclick="deleteRow(this, {{ $camion->id }})">
    <i class="fas fa-trash" style="font-size: 14px; color:#5A55FF;"></i>
</button>
        </div>
    `;
    tr.appendChild(actionsTd);

    tbody.appendChild(tr);
    updateRowTotals();
}

function deleteRow(button, id = null) {
    if (!confirm("Voulez-vous vraiment supprimer cette ligne ?")) return;

    // Supprimer la ligne visuellement
    const tr = button.closest("tr");
    tr.remove();
    updateSummary();

    // Si un ID existe -> supprimer en base
    if (id) {
        fetch(`/user/coutscamion/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                "Accept": "application/json"
            }
        })
        .then(res => {
            if (!res.ok) throw new Error("Erreur réseau");
            return res.json();
        })
        .then(data => {
            if (data.success) {
                toastr.success("Ligne supprimée avec succès");
            } else {
                toastr.error("Erreur lors de la suppression en base");
            }
        })
        .catch(err => {
            console.error(err);
            toastr.error("Erreur lors de la suppression");
        });
    }
}




// Mise à jour des totaux par ligne
function updateRowTotals() {
    document.querySelectorAll("#tableBody tr").forEach(row => {
        let total = 0;
        const costColumns = ['carburant','entretien','immatriculation','assurance','interet','location','amortissement'];
        costColumns.forEach(col => {
            const input = row.querySelector(`input[name="${col}[]"]`);
            total += parseFloat(input?.value) || 0;
        });
        row.querySelectorAll("input[name^='personnalise[']").forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        const totalInput = row.querySelector("input[name='total_depenses[]']");
        if (totalInput) totalInput.value = total.toFixed(2);

        const km = parseFloat(row.querySelector("input[name='km_parcourus[]']")?.value) || 0;
        const heures = parseFloat(row.querySelector("input[name='heures[]']")?.value) || 0;
        if (km > 0) row.querySelector("input[name='cout_km[]']").value = (total/km).toFixed(2);
        if (heures > 0) row.querySelector("input[name='cout_hr[]']").value = (total/heures).toFixed(2);
    });
}

// Mise à jour du résumé (footer)
function updateSummary() {
    updateRowTotals();
    
    const tfoot = document.getElementById("summaryRow");
    tfoot.innerHTML = "";

    // Colonnes fixes
    const fixedColumns = [
        {name: "unite", isTotal: true},
        {name: "annee_de_construction"},
        {name: "no_plaque"},
        {name: "responsable"},
        {name: "marque"},
        {name: "cout_km", isCalc: true},
        {name: "km_parcourus", isSum: true},
        {name: "cout_hr", isCalc: true},
        {name: "heures", isSum: true},
        {name: "carburant", isSum: true},
        {name: "entretien", isSum: true},
        {name: "immatriculation", isSum: true},
        {name: "assurance", isSum: true},
        {name: "interet", isSum: true},
        {name: "location", isSum: true},
        {name: "amortissement", isSum: true}
    ];

    fixedColumns.forEach(col => {
        const td = document.createElement("td");
        if (col.isTotal) {
            td.textContent = "Total";
        } else if (col.isCalc) {
            const kmTotal = sumColumn("km_parcourus[]");
            const heuresTotal = sumColumn("heures[]");
            const totalDepenses = sumColumn("total_depenses[]");
            td.textContent = col.name === "cout_km"
                ? (kmTotal > 0 ? (totalDepenses / kmTotal).toFixed(2) : "0.00")
                : (heuresTotal > 0 ? (totalDepenses / heuresTotal).toFixed(2) : "0.00");
            td.classList.add("bg-info", "text-white");
        } else if (col.isSum) {
            const sum = sumColumn(`${col.name}[]`);
            td.textContent = sum.toFixed(2);
        } else {
            td.textContent = "";
        }
        tfoot.appendChild(td);
    });

    // Colonnes personnalisées
customColumns.forEach(col => {
    const slug = col.slug || col;
    const sum = sumColumn(`personnalise[${slug}][]`);
    const td = document.createElement("td");
    td.textContent = sum.toFixed(2);
    td.dataset.slug = slug;
    tfoot.appendChild(td);
});

// Ensuite seulement → Total Dépenses
const totalDepenses = sumColumn("total_depenses[]");
const totalTd = document.createElement("td");
totalTd.textContent = totalDepenses.toFixed(2);
totalTd.classList.add("bg-info", "text-white");
tfoot.appendChild(totalTd);

// Puis la colonne Actions vide
tfoot.appendChild(document.createElement("td"));

}

// Ajouter une colonne personnalisée
document.getElementById("addColumn").addEventListener("click",()=>{
    const title=prompt("Entrez le titre de la nouvelle colonne:");
    if(!title) return;
    const slug=`col_${Date.now()}`;
    const columnData={slug,title};
    customColumns.push(columnData);

    let hiddenInput=document.getElementById("custom_columns");
    let current=hiddenInput.value?JSON.parse(hiddenInput.value):[];
    current.push(columnData);
    hiddenInput.value=JSON.stringify(current);

    const th=document.createElement("th");
    th.dataset.slug=slug;
    th.innerHTML=`
        <span class="col-title">${title}</span>
        <button type="button" class="btn btn-sm btn-link p-0 edit-col" data-slug="${slug}" title="Modifier">✏️</button>
        <button type="button" class="btn btn-sm btn-link text-danger p-0 delete-col" data-slug="${slug}" title="Supprimer">❌</button>
    `;
    const header=document.getElementById("tableHead");
    header.insertBefore(th,header.querySelector("th:nth-last-child(2)"));

    document.querySelectorAll("#tableBody tr").forEach(tr=>{
        const td=document.createElement("td");
        const input=document.createElement("input");
        input.type="number";input.className="form-control form-control-sm";
        input.name=`personnalise[${slug}][]`;input.step="0.01";
        input.addEventListener("input",updateRowTotals);
        td.appendChild(input);
        tr.insertBefore(td,tr.querySelector("td:nth-last-child(2)"));
    });

    const tfoot=document.getElementById("summaryRow");
    const td=document.createElement("td");
    td.textContent="0.00";td.dataset.slug=slug;
    tfoot.insertBefore(td,tfoot.querySelector("td:nth-last-child(2)"));
    updateSummary();
});

// Gérer ✏️ et ❌
document.addEventListener("click",function(e){
    const btn=e.target.closest("button");
    if(!btn) return;
    if(btn.classList.contains("edit-col")) editColumn(btn.dataset.slug);
    if(btn.classList.contains("delete-col")) deleteColumn(btn.dataset.slug);
});

// Editer titre
function editColumn(slug){
    const th=document.querySelector(`th[data-slug="${slug}"]`);
    const span=th.querySelector(".col-title");
    const input=document.createElement("input");
    input.type="text";input.value=span.textContent;
    input.className="form-control form-control-sm d-inline-block";
    input.style.width="120px";
    th.replaceChild(input,span);
    input.focus();
    input.addEventListener("blur",()=>saveColumnTitle(slug,input.value));
    input.addEventListener("keydown",e=>{
        if(e.key==="Enter") saveColumnTitle(slug,input.value);
        if(e.key==="Escape") th.replaceChild(span,input);
    });
}

// Sauver titre
function saveColumnTitle(slug,newTitle){
    const th=document.querySelector(`th[data-slug="${slug}"]`);
    const input=th.querySelector("input");
    const span=document.createElement("span");
    span.className="col-title";span.textContent=newTitle;
    th.replaceChild(span,input);

    let hiddenInput=document.getElementById("custom_columns");
    let current=hiddenInput.value?JSON.parse(hiddenInput.value):[];
    current=current.map(c=>c.slug===slug?{...c,title:newTitle}:c);
    hiddenInput.value=JSON.stringify(current);
}

// Supprimer une colonne
function deleteColumn(slug) {
    if (!confirm("Voulez-vous vraiment supprimer cette colonne ?")) return;

    // Supprimer du DOM
    document.querySelector(`th[data-slug="${slug}"]`)?.remove();
    document.querySelectorAll(`#tableBody tr`).forEach(tr => {
        const td = tr.querySelector(`input[name="personnalise[${slug}][]"]`);
        if (td) td.closest("td").remove();
    });
    document.querySelectorAll(`#summaryRow td[data-slug="${slug}"]`).forEach(td => td.remove());
    updateSummary();

    // 🔹 Supprimer en BDD via AJAX
    fetch(`/user/coutscamion/column/${slug}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            toastr.success("Colonne supprimée");
        } else {
            toastr.error("Erreur lors de la suppression");
        }
    })
    .catch(() => toastr.error("Erreur serveur"));
}



// Init
document.addEventListener("DOMContentLoaded",()=>{
    if(existingCamions.length===0) createRow();
    updateSummary();
});
// ✅ Bouton Ajouter une ligne
document.getElementById("addRow").addEventListener("click", () => {
    createRow();      // ajoute une ligne vide
    updateSummary();  // met à jour le footer
});
// Sauvegarde AJAX du formulaire
$('#camionForm').on('submit', function (e) {
    e.preventDefault();

    let form = $(this);
    let submitBtn = form.find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Enregistrement...');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function (response) {
            if (response.success) {
                toastr.success(response.message || "Données sauvegardées avec succès ✅");
                // 🔹 Recalculer les totaux sans reload
                updateSummary();
            } else {
                toastr.error(response.message || "Erreur lors de l'enregistrement ❌");
            }
        },
        error: function (xhr) {
            let errorMsg = xhr.responseJSON?.message || "Erreur serveur";
            toastr.error(errorMsg);
        },
        complete: function () {
            submitBtn.prop('disabled', false).html('<i class="fas fa-check"></i> Sauvegarder');
        }
    });
});

</script>

@endsection