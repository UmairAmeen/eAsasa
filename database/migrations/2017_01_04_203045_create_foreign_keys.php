<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Model;

class CreateForeignKeys extends Migration {

	public function up()
	{
		Schema::table('purchase', function(Blueprint $table) {
			$table->foreign('warehouse_id')->references('id')->on('warehouse')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('purchase', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('purchase', function(Blueprint $table) {
			$table->foreign('supplier_id')->references('id')->on('supplier')
						->onDelete('set null')
						->onUpdate('restrict');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->foreign('purchase_id')->references('id')->on('purchase')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->foreign('sale_id')->references('id')->on('order')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->foreign('customer_id')->references('id')->on('customer')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->foreign('refund_id')->references('id')->on('refund')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->foreign('warehouse_id')->references('id')->on('warehouse')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->foreign('supplier_id')->references('id')->on('supplier')
						->onDelete('set null')
						->onUpdate('restrict');
		});
		Schema::table('rates', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('rates', function(Blueprint $table) {
			$table->foreign('customer_id')->references('id')->on('customer')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('transaction', function(Blueprint $table) {
			$table->foreign('invoice_id')->references('id')->on('invoice')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('transaction', function(Blueprint $table) {
			$table->foreign('customer_id')->references('id')->on('customer')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('transaction', function(Blueprint $table) {
			$table->foreign('supplier_id')->references('id')->on('supplier')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('invoice', function(Blueprint $table) {
			$table->foreign('customer_id')->references('id')->on('customer')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('order', function(Blueprint $table) {
			$table->foreign('invoice_id')->references('id')->on('invoice')
						->onDelete('cascade')
						->onUpdate('restrict');
		});
		Schema::table('order', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('refund', function(Blueprint $table) {
			$table->foreign('customer_id')->references('id')->on('customer')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('refund', function(Blueprint $table) {
			$table->foreign('product_id')->references('id')->on('products')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('refund', function(Blueprint $table) {
			$table->foreign('supplier_id')->references('id')->on('supplier')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('receipe', function(Blueprint $table) {
			$table->foreign('raw_id')->references('id')->on('products')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
		Schema::table('receipe', function(Blueprint $table) {
			$table->foreign('final_id')->references('id')->on('products')
						->onDelete('restrict')
						->onUpdate('restrict');
		});
	}

	public function down()
	{
		Schema::table('purchase', function(Blueprint $table) {
			$table->dropForeign('purchase_warehouse_id_foreign');
		});
		Schema::table('purchase', function(Blueprint $table) {
			$table->dropForeign('purchase_product_id_foreign');
		});
		Schema::table('purchase', function(Blueprint $table) {
			$table->dropForeign('purchase_supplier_id_foreign');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->dropForeign('stocklog_purchase_id_foreign');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->dropForeign('stocklog_sale_id_foreign');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->dropForeign('stocklog_refund_id_foreign');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->dropForeign('stocklog_product_id_foreign');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->dropForeign('stocklog_warehouse_id_foreign');
		});
		Schema::table('stocklog', function(Blueprint $table) {
			$table->dropForeign('stocklog_supplier_id_foreign');
		});
		Schema::table('rates', function(Blueprint $table) {
			$table->dropForeign('rates_product_id_foreign');
		});
		Schema::table('rates', function(Blueprint $table) {
			$table->dropForeign('rates_customer_id_foreign');
		});
		Schema::table('transaction', function(Blueprint $table) {
			$table->dropForeign('transaction_invoice_id_foreign');
		});
		Schema::table('transaction', function(Blueprint $table) {
			$table->dropForeign('transaction_customer_id_foreign');
		});
		Schema::table('transaction', function(Blueprint $table) {
			$table->dropForeign('transaction_supplier_id_foreign');
		});
		Schema::table('invoice', function(Blueprint $table) {
			$table->dropForeign('invoice_customer_id_foreign');
		});
		Schema::table('order', function(Blueprint $table) {
			$table->dropForeign('order_invoice_id_foreign');
		});
		Schema::table('order', function(Blueprint $table) {
			$table->dropForeign('order_product_id_foreign');
		});
		Schema::table('refund', function(Blueprint $table) {
			$table->dropForeign('refund_customer_id_foreign');
		});
		Schema::table('refund', function(Blueprint $table) {
			$table->dropForeign('refund_product_id_foreign');
		});
		Schema::table('refund', function(Blueprint $table) {
			$table->dropForeign('refund_supplier_id_foreign');
		});
		Schema::table('receipe', function(Blueprint $table) {
			$table->dropForeign('receipe_raw_id_foreign');
		});
		Schema::table('receipe', function(Blueprint $table) {
			$table->dropForeign('receipe_final_id_foreign');
		});
	}
}