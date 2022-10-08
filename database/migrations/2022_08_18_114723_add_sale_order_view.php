<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSaleOrderView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::connection()->getpdo()->exec("DROP VIEW IF EXISTS `invoice_extended_view`;");
        if (!Schema::hasTable('invoice_extended_view')) {
            DB::connection()->getpdo()->exec("CREATE VIEW `invoice_extended_view`AS 
        SELECT invoice.*, invoice.id as invoice_id, calculate_balance(invoice.id) as balance, 
        sale_orders.id as sale_order_id, sale_orders.status as sale_order_status, sale_orders.posted as posted, 
        sale_orders.delivery_date as delivery_date, sale_orders.date as sale_order_date,
        sale_orders.source as sale_order_source, sale_orders.sales_people_id as sales_person_id, sale_orders.completion_date as completion_date, 
        sales_people.name as sale_person_name, sales_people.commission as commission, sales_people.address as sales_person_address,
        supplier.name as supplier_name, supplier.address as supplier_address,
        customer.name as customer_name, customer.city as customer_city, customer.address as customer_address FROM `invoice`
        LEFT JOIN sale_orders ON sale_orders.invoice_id = invoice.id
        LEFT JOIN sales_people ON sale_orders.sales_people_id = sales_people.id
        LEFT JOIN supplier ON supplier.id = invoice.supplier_id
        LEFT JOIN customer ON customer.id = invoice.customer_id
        WHERE invoice.deleted_at IS NULL
        ;");
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
