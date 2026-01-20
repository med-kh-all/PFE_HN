<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeCCQ extends Model
{
    protected $table = 'employee_ccq';
    public $timestamps = true;

    protected $fillable = [
        'employee_id',
        'avantages_sociaux',
        'taxes_assurance',
        'ccq',
        'aecq',
        'fonds_divers',
        'equipement_securite',
        'clauses_normatives',
        'total_cout_horaire',
        'cout_annuel_total',
    ];

    protected $casts = [
        'avantages_sociaux'   => 'decimal:2',
        'taxes_assurance'     => 'decimal:2',
        'ccq'                 => 'decimal:2',
        'aecq'                => 'decimal:2',
        'fonds_divers'        => 'decimal:2',
        'equipement_securite' => 'decimal:2',
        'clauses_normatives'  => 'decimal:2',
        'total_cout_horaire'  => 'decimal:4',
        'cout_annuel_total'   => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employees::class, 'employee_id');
    }
}
