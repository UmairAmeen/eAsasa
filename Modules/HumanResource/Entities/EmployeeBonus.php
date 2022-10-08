<?php

namespace Modules\HumanResource\Entities;

use Illuminate\Database\Eloquent\Model;

class EmployeeBonus extends Model
{
    protected $fillable = [];


    public function employee()
    {
    	return $this->belongsTo('Modules\HumanResource\Entities\Employee');
    }
}
