<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BankAccount extends Model
{
    protected $table = 'bank_accounts';

    public static function GetBankDropDown() {
        $banks = self::select(DB::raw("CONCAT(name, ' - ', branch) AS bank"), 'id')->pluck('bank', 'id')->toArray();
        $banks[""] = "Select Bank";
        ksort($banks);
        return  $banks;
    }

    public function Invoice() {
		return $this->hasMany("App\Invoice");
    }
}
