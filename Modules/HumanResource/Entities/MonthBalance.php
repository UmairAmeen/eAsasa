<?php

namespace Modules\HumanResource\Entities;

use Illuminate\Database\Eloquent\Model;

class MonthBalance extends Model
{
    protected $fillable = ['date','employee_id'];
    protected $table = 'monthly_balance';
}
