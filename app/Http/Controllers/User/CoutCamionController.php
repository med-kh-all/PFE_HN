<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CoutCamion;
use App\Models\CamionCustomColumn;

class CoutCamionController extends Controller
{
    public function index()
{
    $camions = CoutCamion::where('est_moyenne', false)->get();
    $colonnesPersonnalisees = [];

    foreach ($camions as $camion) {
        if (is_array($camion->colonnes_personnalisees)) {
            foreach ($camion->colonnes_personnalisees as $key => $value) {
                if (!in_array($key, $colonnesPersonnalisees)) {
                    $colonnesPersonnalisees[] = $key; // ICI → uniquement les slugs
                }
            }
        }
    }

    // Associer slug → titre
    $titresColonnesPersonnalisees = CamionCustomColumn::pluck('title', 'slug')->toArray();

    return view('user.coutscamion.index', compact(
        'camions',
        'colonnesPersonnalisees',
        'titresColonnesPersonnalisees'
    ));
}


    public function store(Request $request)
    {
        $rowCount = count($request->input('unite', []));

        CoutCamion::where('est_moyenne', false)->delete();
        // 🔹 Enregistrement des titres des colonnes personnalisées
 // Sauvegarder les colonnes personnalisées
    if ($request->filled('custom_columns')) {
    $columns = json_decode($request->input('custom_columns'), true) ?? [];
    foreach ($columns as $column) {
        if (!empty($column['slug']) && !empty($column['title'])) {
            CamionCustomColumn::updateOrCreate(
                ['slug' => $column['slug']],
                ['title' => $column['title']]
            );
        }
    }
}

        for ($i = 0; $i < $rowCount; $i++) {
            $data = [
                'unite' => $request->input('unite')[$i] ?? null,
                'annee_de_construction' => $request->input('annee_de_construction')[$i] ?? null,
                'no_plaque' => $request->input('no_plaque')[$i] ?? null,
                'responsable' => $request->input('responsable')[$i] ?? null,
                'marque' => $request->input('marque')[$i] ?? null,
                'cout_km' => $request->input('cout_km')[$i] ?? null,
                'km_parcourus' => $request->input('km_parcourus')[$i] ?? null,
                'cout_hr' => $request->input('cout_hr')[$i] ?? null,
                'heures' => $request->input('heures')[$i] ?? null,
                'carburant' => $request->input('carburant')[$i] ?? null,
                'entretien' => $request->input('entretien')[$i] ?? null,
                'immatriculation' => $request->input('immatriculation')[$i] ?? null,
                'assurance' => $request->input('assurance')[$i] ?? null,
                'interet' => $request->input('interet')[$i] ?? null,
                'location' => $request->input('location')[$i] ?? null,
                'amortissement' => $request->input('amortissement')[$i] ?? null,
                'total_depenses' => $request->input('total_depenses')[$i] ?? null,
                'est_moyenne' => false,
                'colonnes_personnalisees' => collect($request->input('personnalise', []))->map(fn($col) => $col[$i] ?? null)->filter()->toArray(),
            ];

            $data['total_depenses'] =
                ($data['carburant'] ?? 0) +
                ($data['entretien'] ?? 0) +
                ($data['immatriculation'] ?? 0) +
                ($data['assurance'] ?? 0) +
                ($data['interet'] ?? 0) +
                ($data['location'] ?? 0) +
                ($data['amortissement'] ?? 0);

            if (($data['km_parcourus'] ?? 0) > 0) {
                $data['cout_km'] = $data['total_depenses'] / $data['km_parcourus'];
            }
            if (($data['heures'] ?? 0) > 0) {
                $data['cout_hr'] = $data['total_depenses'] / $data['heures'];
            }

            CoutCamion::create($data);
        }

        return redirect()->route('user.coutscamion.index')->with('success', 'Données sauvegardées');
    }

    public function edit($id)
    {
        $camion = CoutCamion::findOrFail($id);
        return view('user.coutscamion.edit', compact('camion'));
    }

    public function destroy($id)
    {
        $camion = CoutCamion::findOrFail($id);
        $camion->delete();

        return response()->json(['success' => true]);
    }

    public function destroyColumn($slug)
{
    // Supprimer la définition de la colonne
    CamionCustomColumn::where('slug', $slug)->delete();

    // Retirer la colonne de chaque camion
    $camions = CoutCamion::all();
    foreach ($camions as $camion) {
        $colonnes = $camion->colonnes_personnalisees ?? [];
        if (isset($colonnes[$slug])) {
            unset($colonnes[$slug]);
            $camion->colonnes_personnalisees = $colonnes;
            $camion->save();
        }
    }

    return response()->json(['success' => true]);
}


}
