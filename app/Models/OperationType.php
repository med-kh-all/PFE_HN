<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationType extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'is_ccq'];

    protected $casts = [
        'is_ccq'     => 'boolean',
        'company_id' => 'integer',
    ];

    // Compagnie
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Employés liés à ce type d’opération
    public function employees()
    {
        return $this->hasMany(employees::class, 'operation_type_id', 'id');
    }
}
