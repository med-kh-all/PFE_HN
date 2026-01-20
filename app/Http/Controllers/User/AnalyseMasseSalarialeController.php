<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OperationType;
use App\Models\RecapitulatifActivite; // Table: recapitulatifs_activites

class AnalyseMasseSalarialeController extends Controller
{
public function index(Request $request)
{
    // Récupérer la compagnie active (session ou user)
    $companyId = session('company_id') ?? auth()->user()->company_id;
    session(['company_id' => $companyId]); // au cas où

    // Filtrer uniquement les opérations de cette compagnie
    $ops = OperationType::where('company_id', $companyId)
        ->orderBy('id')
        ->get();

    // Charger uniquement les recap liés à ces opérations
    $recaps = RecapitulatifActivite::whereIn('operation_type_id', $ops->pluck('id'))
        ->get()
        ->keyBy('operation_type_id');

    // Lignes du tableau -> champs de la table recap
    $rowsMap = [
        'salaires'   => 'salaire_total',
        'vacances'   => 'vacances_total',
        'avantages'  => 'avantages_sociaux_total',
        'csst'       => 'csst_total',
        'boni'       => 'boni_total',
        'assurance'  => 'assurance_groupe_total',
    ];

    $columns = [];
    $grand = [
        'rows'  => array_fill_keys(array_keys($rowsMap), 0.0),
        'total' => 0.0,
        'hours' => 0.0,
    ];

    foreach ($ops as $op) {
        $rec = $recaps[$op->id] ?? null;

        $col = [
            'label' => $op->name,
            'rows'  => [],
            'hours' => $rec->total_heures ?? 0,
        ];

        $total = 0.0;
        foreach ($rowsMap as $key => $field) {
            $v = (float) ($rec->$field ?? 0);
            $col['rows'][$key] = $v;
            $total += $v;
            $grand['rows'][$key] += $v;
        }

        $col['total'] = $total;
        $col['rate_per_hour'] = $col['hours'] > 0 ? ($total / $col['hours']) : 0;
        $columns[] = $col;

        $grand['total'] += $total;
        $grand['hours'] += $col['hours'];
    }

    $grand['rate_per_hour'] = $grand['hours'] > 0 ? ($grand['total'] / $grand['hours']) : 0;

    // % de chaque activité
    $percentages = [];
    foreach ($columns as $col) {
        $percentages[] = $grand['total'] > 0 ? ($col['total'] / $grand['total']) * 100 : 0;
    }

    // Données pour le graphique
    $chart = [
        'labels' => array_map(fn($c) => $c['label'], $columns),
        'values' => array_map(fn($c) => round($c['total'], 2), $columns),
    ];

    return view('user.fardeauMO.analyse-mo.index', compact('columns', 'rowsMap', 'grand', 'percentages', 'chart'));
}


}
