<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryChallan extends Model
{
    use SoftDeletes;
    protected $fillable = ['name'];
    protected $casts = ['o_details' => 'array'];

    const PENDING = 'Pending';
    const READY = 'Ready to Deliver';
    const DELIVERING = 'In Progress';
    const DELIVERED = 'Delivered';

    public static $deliveryStatuses = [
        // "" => "Select",
        self::PENDING => self::PENDING,
        self::READY => self::READY,
        self::DELIVERING => self::DELIVERING,
        self::DELIVERED => self::DELIVERED,
    ];

    public function delivery_invoice()
	{
		return $this->belongsTo('App\Invoice','order_no');
	}
} 
