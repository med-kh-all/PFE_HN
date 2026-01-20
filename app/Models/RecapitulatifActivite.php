<?php
// app/Models/RecapitulatifActivite.php
namespace App\Models;

 
use Illuminate\Database\Eloquent\Model;
 
class RecapitulatifActivite extends Model
{
    protected $table = 'recapitulatifs_activites';
 
    protected $fillable = [
        'operation_type_id',
         'company_id',
        'total_heures',
        'salaire_total',
        'cout_total',
        'vacances_total',
        'avantages_sociaux_total',
        'rrq_total',
        'ae_total',
        'rqap_total',
        'cnt_total',
        'fssq_total',
        'csst_total',
        'boni_total',
        'assurance_groupe_total',
        'ccq_total',
        'total_general',
    ];
     public function company()
{
    return $this->belongsTo(Company::class);
}
  public function operationType()
{
    return $this->belongsTo(OperationType::class, 'operation_type_id', 'id');
}
}