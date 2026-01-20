<?php 

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Imports\AmortissementsImport;
use App\Models\Amortissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AmortissementController extends Controller
{
    public function index()
    {
        $companyId = session('company_id') ?? Auth::user()->company_id;
        session(['company_id' => $companyId]);

        $years = Amortissement::where('company_id', $companyId)
            ->select('year')
            ->distinct()
            ->orderBy('year')
            ->get();

        return view('user.amortissements.index', compact('years'));
    }

    public function create(Request $request)
    {
        $start = $request->query('start');
        $end   = $request->query('end');

        $companyId = session('company_id') ?? (Auth::check() ? Auth::user()->company_id : null);
        if (!$companyId) {
            return redirect()->route('login')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }
        session(['company_id' => $companyId]);

        $dataByYear = [];

        for ($i = $start; $i <= $end; $i++) {
            $currentData = Amortissement::where('company_id', $companyId)
                ->where('year', $i)
                ->get();

            $dataByYear[$i] = $currentData->toArray();
        }

        return view('user.amortissements.create', compact('start', 'end', 'dataByYear'));
    }

   
    public function store(Request $request)
{
    $companyId = session('company_id') ?? Auth::user()->company_id;
    session(['company_id' => $companyId]);

    $data = $request->input('amortissements', []);
    $activeYear = $request->input('active_year');

    if (empty($data)) {
        return back()->with('error', 'Aucune donnée envoyée.');
    }

    $years = array_map('intval', array_keys($data));
    sort($years);
    $minYear = min($years);
    $maxYear = max($years);

    $memo = [];                 // état poste après chaque année
    $firstYearByPoste = [];     // pour savoir où commence chaque poste

    // ---- PASS 1: on enregistre les années envoyées ----
    foreach ($years as $year) {
        foreach ($data[$year] as $ligne) {
            $poste = trim($ligne['poste'] ?? '');
            if ($poste === '' || ($ligne['taux'] ?? '') === '') continue;

            $taux = (float) $ligne['taux'];
            $type_amortissement = $ligne['type_amortissement'] ?? 'D';
            $acquisition_annee  = (float) ($ligne['acquisition_annee'] ?? 0);

            if (!isset($firstYearByPoste[$poste])) {
                $firstYearByPoste[$poste] = $year;
            }

            if (!isset($memo[$poste])) {
                $cout = (float) ($ligne['cout'] ?? 0);
                $amort_cumule_anterieur = (float) ($ligne['amort_cumule_anterieur'] ?? 0);
            } else {
                $prev = $memo[$poste];
                $cout = $prev['cout'] + $prev['acquisition_annee'];
                $amort_cumule_anterieur = $prev['amort_cumule_anterieur'] + $prev['amortissement_annee'];
                // conserver le taux/méthode depuis la première année
                $taux = $prev['taux'];
                $type_amortissement = $prev['type_amortissement'];
            }

            $valeur_nette_anterieure = $cout - $amort_cumule_anterieur;

            // calcul amortissement
            if ($type_amortissement === 'L') {
                $amortissement_annee = $cout * ($taux / 100) * 0.5;
            } elseif ($type_amortissement === 'D') {
                $amortissement_annee = ($valeur_nette_anterieure + ($acquisition_annee / 2)) * ($taux / 100);
            } else {
                $amortissement_annee = 0.0;
            }

            $amortissement_mensuel = $amortissement_annee / 12.0;

            // sauvegarde en BDD
            Amortissement::updateOrCreate(
                ['company_id' => $companyId, 'poste' => $poste, 'year' => $year],
                [
                    'cout'                     => $cout,
                    'amort_cumule_anterieur'   => $amort_cumule_anterieur,
                    'valeur_nette_anterieure'  => $valeur_nette_anterieure,
                    'acquisition_annee'        => $acquisition_annee,
                    'amortissement_annee'      => $amortissement_annee,
                    'amortissement_mensuel'    => $amortissement_mensuel,
                    'taux'                     => $taux,
                    'type_amortissement'       => $type_amortissement,
                ]
            );

            // préparer état pour année suivante
            $memo[$poste] = [
                'cout'                   => $cout + $acquisition_annee,
                'acquisition_annee'      => 0.0,
                'amort_cumule_anterieur' => $amort_cumule_anterieur + $amortissement_annee,
                'amortissement_annee'    => $amortissement_annee,
                'taux'                   => $taux,
                'type_amortissement'     => $type_amortissement,
            ];
        }
    }

    // ---- PASS 2: compléter toutes les années manquantes ----
    foreach ($firstYearByPoste as $poste => $startY) {
        if (!isset($memo[$poste])) continue;

        $prev = $memo[$poste];

        for ($y = $startY + 1; $y <= $maxYear; $y++) {
            $cout = $prev['cout'];
            $amort_cumule_anterieur = $prev['amort_cumule_anterieur'];
            $taux = $prev['taux'];
            $type = $prev['type_amortissement'];
            $acq  = 0.0;

            $valeur_nette_anterieure = $cout - $amort_cumule_anterieur;

            if ($type === 'L') {
                $amortissement_annee = $cout * ($taux / 100) * 0.5;
            } elseif ($type === 'D') {
                $amortissement_annee = ($valeur_nette_anterieure + ($acq / 2)) * ($taux / 100);
            } else {
                $amortissement_annee = 0.0;
            }
            $amortissement_mensuel = $amortissement_annee / 12.0;

            Amortissement::updateOrCreate(
                ['company_id' => $companyId, 'poste' => $poste, 'year' => $y],
                [
                    'cout'                     => $cout,
                    'amort_cumule_anterieur'   => $amort_cumule_anterieur,
                    'valeur_nette_anterieure'  => $valeur_nette_anterieure,
                    'acquisition_annee'        => $acq,
                    'amortissement_annee'      => $amortissement_annee,
                    'amortissement_mensuel'    => $amortissement_mensuel,
                    'taux'                     => $taux,
                    'type_amortissement'       => $type,
                ]
            );

            // état pour année suivante
            $prev = [
                'cout'                   => $cout + $acq,
                'amort_cumule_anterieur' => $amort_cumule_anterieur + $amortissement_annee,
                'taux'                   => $taux,
                'type_amortissement'     => $type,
            ];
        }
    }

    return redirect()
        ->back()
        ->withInput()
        ->with('success', 'Amortissements enregistrés et propagés avec succès.')
        ->with('active_year', $activeYear);
}


    public function import(Request $request, $year)
    {
        $import = new AmortissementsImport($year);
        Excel::import($import, $request->file('file'));
        $amortissements = $import->getImported();

        return view('user.amortissements.preview', compact('amortissements', 'year'));
    }

    public function storeImported(Request $request, $year)
    {
        $companyId = session('company_id') ?? Auth::user()->company_id;
        session(['company_id' => $companyId]);

        $amortissements = $request->input('amortissements');

        foreach ($amortissements as $ligne) {
            Amortissement::create(array_merge($ligne, [
                'company_id' => $companyId,
                'year' => $year,
            ]));
        }

        return redirect()->route('user.amortissements.index')->with('success', 'Importation enregistrée avec succès.');
    }

    public function showByYear($year)
{
    $companyId = session('company_id') ?? Auth::user()->company_id;
    session(['company_id' => $companyId]);

    $amortissements = Amortissement::where('company_id', $companyId)
        ->where('year', $year)
        ->get();

    // ➝ On ajoute la récupération de toutes les années ici :
    $years = Amortissement::where('company_id', $companyId)
        ->select('year')
        ->distinct()
        ->orderBy('year')
        ->pluck('year'); // ça renvoie une Collection [2025, 2026, 2027, ...]

    return view('user.amortissements.show', compact('amortissements', 'year', 'years'));
}


    public function destroy($year)
    {
        $companyId = session('company_id') ?? Auth::user()->company_id;
        session(['company_id' => $companyId]);

        Amortissement::where('company_id', $companyId)
            ->where('year', $year)
            ->delete();

        return redirect()->route('user.amortissements.index')->with('success', 'Amortissements de l\'année supprimés.');
    }

    public function bulkDestroy(Request $request)
    {
        $companyId = session('company_id') ?? Auth::user()->company_id;
        session(['company_id' => $companyId]);

        $postes = $request->input('postes', []);
        $postes = array_values(array_unique(
            array_filter(array_map(fn($p) => trim((string) $p), $postes))
        ));

        if (empty($postes)) {
            return response()->json(['ok' => false, 'message' => 'Aucun poste valide.'], 422);
        }

        $deleted = Amortissement::where('company_id', $companyId)
            ->whereIn('poste', $postes)
            ->delete();

        return response()->json(['ok' => true, 'deleted' => $deleted]);
    }
}
