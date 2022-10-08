<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionHolder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Delete unused
        // $unsued = ["supplier-list","supplier-create","supplier-edit","supplier-delete","refund-list","refund-create","refund-edit","refund-delete","report-create","report-edit","report-delete"];
        $unsued = ["report-create","report-edit","report-delete"];
        foreach ($unsued as $key => $value) {
            Permission::where('name',$value)->delete();
        }        
        $new = [
            [
                'module' => 'product',
                'name' => 'product-import-export',
                'display_name' => 'Import/Export Product as Excel',
            ], [
                'module' => 'customer',
                'name' => 'customer-import-export',
                'display_name' => 'Import/Export Customer as Excel',
            ], [
                'module' => 'product',
                'name' => 'product-show-purchase-price',
                'display_name' => 'Show Product Purchase Price, if not, then nowhere purchase price is shown',
            ], [
                'module' => 'report',
                'name' => 'report-balance_sheet',
                'display_name' => 'View Balance Sheet Report',
            ], [
                'module' => 'report',
                'name' => 'report-top_selling',
                'display_name' => 'View Top Selling Product Report',
            ], [
                'module' => 'report',
                'name' => 'report-customer_reporting',
                'display_name' => 'Complete Customer Report',
            ], [
                'module' => 'report',
                'name' => 'report-aging',
                'display_name' => 'Aging Report',
            ], [
                'module' => 'report',
                'name' => 'report-stock_detail',
                'display_name' => 'Stock In/Out Report',
            ], [
                'module' => 'report',
                'name' => 'report-product_supplier',
                'display_name' => 'Supplier Product Report',
            ], [
                'module' => 'report',
                'name' => 'report-product_record',
                'display_name' => 'Customer to Product Report',
            ], [
                'module' => 'supplier',
                'name' => 'report-product_record_supplier',
                'display_name' => 'Product To Supplier Relation',
            ], [
                'module' => 'report',
                'name' => 'report-receivable',
                'display_name' => 'Receivable Report',
            ], [
                'module' => 'report',
                'name' => 'report-payable',
                'display_name' => 'Payable Report',
            ], [
                'module' => 'report',
                'name' => 'report-log_report',
                'display_name' => 'Log Report',
            ], [
                'module' => 'report',
                'name' => 'report-worth',
                'display_name' => 'Stock Worth Caculator Report',
            ], [
                'module' => 'report',
                'name' => 'report-purchase_detailed',
                'display_name' => 'Purchase Insight Report (Business Intelligent)',
            ], [
                'module' => 'report',
                'name' => 'report-profit',
                'display_name' => 'Profit/Loss Insight (Business Intelligent)',
            ], [
                'module' => 'misc',
                'name' => 'backup',
                'display_name' => 'Software Backup',
            ], [
                'module' => 'report',
                'name' => "report-day_sale",
                'display_name' => "Get A Detailed View of product wise sale",
            ], [
                'module' => 'report',
                'name' => "report-profit_all",
                'display_name' => "Profit/Loss Calculation Based on your Sale & Purhcases",
            ], [
                'module' => 'report',
                'name' => "report-stock_in_out_view",
                'display_name' => "Stock Detailed In Out Summary",
            ], [
                'module' => 'report',
                'name' => "report-department",
                'display_name' => "Get A Detailed View of Sale by Warehouse",
            ], [
                'module' => 'report',
                'name' => "report-expense",
                'display_name' => "Get A Detailed View of Expenses",
            ], [
                'module' => 'report',
                'name' => "report-day_discount",
                'display_name' => "Get A Detailed View of who sold products and gave discounts",
            ],
            [
                'module' => 'human resource',
                'name' => "access-hr",
                'display_name' => "HR Module access",
            ],
            [
                'module' => 'report',
                'name' => "report-delivery_report",
                'display_name' => "Get A Detailed View Order Delivery status",
            ],
            [
                'module' => 'report',
                'name' => "report-saleorders_details",
                'display_name' => "Get A Detailed View of Sale Orders",
            ],
            [
                'module' => 'report',
                'name' => "report-shipping",
                'display_name' => "Get A Detailed View of Shipping/Packing/Transport",
            ],
        ];

         foreach ($new as $key => $value) {
        	if (!Permission::where('name',$value['name'])->first()) {
        		$value['description'] = $value['display_name'];
                $value['module'] = $value['module'];
                Permission::create($value);
            }
        }
    }
}
