<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminTransaction extends Model
{
    use SoftDeletes;
    protected $table = 'admin_transaction';
}
