<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employees extends Model
{
    use HasFactory;

    protected $fillable = [
'operation_type_id','employee_name','position','hours_worked_annual','weeks_worked',
  'vacation_rate','hourly_rate','annual_salary_base','retirement_fund','bonus',
  'group_insurance','other_benefits_hourly','paid_vacation','paid_leave',
  'adjusted_hourly_rate','rrq','ae','rqap','csst','fssq','cnt','other_benefits',
  'rate_before_downtime','total_annual_cost','non_taxable_dividends','breaks_per_hour',
  'idle_time_per_hour','total_non_productive_time','productive_time',
  'productive_time_percentage','rate_with_burden','burden_percentage','hire_date','seniority'
    ];

    // Relation avec l'activité (operation_type)
    public function operationType()
{
    return $this->belongsTo(OperationType::class, 'operation_type_id', 'id');
}


    public function ccq()
    {
    return $this->hasOne(EmployeeCCQ::class, 'employee_id');
     }
}
