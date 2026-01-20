<?php
 
namespace App\Http\Controllers\User;
 
use App\Http\Controllers\Controller;
use App\Models\Employees;
use App\Models\RecapitulatifActivite;
use Illuminate\Http\Request;
 
class RecapitulatifActiviteController extends Controller
{
    public function recompute(Request $request)
    {
        $operationTypeId = $request->query('type') ?? $request->input('type');
        if (!$operationTypeId) {
            return response()->json(['message' => 'Paramètre "type" manquant.'], 422);
        }
 
        // RecapitulatifActiviteController@recompute
        $agg = Employees::where('operation_type_id', $operationTypeId)
     
    ->selectRaw('
        COALESCE(SUM(hours_worked_annual),0)      as total_heures,
        COALESCE(SUM(annual_salary_base),0)       as salaire_total,
        COALESCE(SUM(total_annual_cost),0)        as cout_total,

        COALESCE(SUM(paid_vacation * hours_worked_annual),0) as vacances_total,
        COALESCE(SUM(
    (paid_vacation* hours_worked_annual) +
    (rrq * hours_worked_annual) +
    (ae * hours_worked_annual) +
    (rqap * hours_worked_annual) +
    (cnt * hours_worked_annual) +
    (fssq * hours_worked_annual) 
   
),0) as avantages_sociaux_total,


        COALESCE(SUM(rrq * hours_worked_annual),0)   as rrq_total,
        COALESCE(SUM(ae),0) as ae_total,
        COALESCE(SUM(rqap * hours_worked_annual),0)  as rqap_total,
        COALESCE(SUM(cnt * hours_worked_annual),0)   as cnt_total,
        COALESCE(SUM(fssq * hours_worked_annual),0)  as fssq_total,
        COALESCE(SUM(csst * hours_worked_annual),0)  as csst_total,

        COALESCE(SUM(bonus),0)                       as boni_total,
        COALESCE(SUM(group_insurance),0)             as assurance_groupe_total
    ')
    ->first();


 
        // Si tu as ccq_total côté employees, récupère-le, sinon 0
        $ccq_total = property_exists($agg, 'ccq_total') ? (float)$agg->ccq_total : 0.0;
 
        // total_general = somme de tous les postes du récap SAUF:
        // total_heures, cout_total, rrq_total, ae_total, rqap_total, cnt_total, fssq_total, csst_total
        $total_general =
            (float)$agg->salaire_total +
            (float)$agg->vacances_total +
            (float)$agg->avantages_sociaux_total +
            (float)$agg->boni_total +
            (float)$agg->assurance_groupe_total +
            $ccq_total;
 
        $payload = [
            'operation_type_id'       => $operationTypeId,
            'total_heures'            => (float)$agg->total_heures,
            'salaire_total'           => (float)$agg->salaire_total,
            'cout_total'              => (float)$agg->cout_total,
 
            'vacances_total'          => (float)$agg->vacances_total,
            'avantages_sociaux_total' => (float)$agg->avantages_sociaux_total,
 
            'rrq_total'               => (float)$agg->rrq_total,
            'ae_total'                => (float)$agg->ae_total,
            'rqap_total'              => (float)$agg->rqap_total,
            'cnt_total'               => (float)$agg->cnt_total,
            'fssq_total'              => (float)$agg->fssq_total,
            'csst_total'              => (float)$agg->csst_total,
 
            'boni_total'              => (float)$agg->boni_total,
            'assurance_groupe_total'  => (float)$agg->assurance_groupe_total,
            'ccq_total'               => $ccq_total,
 
            'total_general'           => $total_general,
        ];
 
        $recap = RecapitulatifActivite::updateOrCreate(
            ['operation_type_id' => $operationTypeId],
            $payload
        );
 
        return response()->json(['message' => 'Récapitulatif mis à jour', 'recap' => $recap]);
    }
 
    public function show(Request $request)
    {
        $operationTypeId = $request->query('type');
        if (!$operationTypeId) {
            return response()->json(['message' => 'Paramètre "type" manquant.'], 422);
        }
 
        $recap = RecapitulatifActivite::where('operation_type_id', $operationTypeId)->first();
        return response()->json(['recap' => $recap]);
    }
}