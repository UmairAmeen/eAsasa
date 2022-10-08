<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            [
                'module' => 'customer',
                'name' => 'customer-list',
                'display_name' => 'Display Customer Listing',
                'description' => 'See only Listing Of Customers'
            ],
            [
                'module' => 'customer',
                'name' => 'customer-create',
                'display_name' => 'Create Customer',
                'description' => 'Create New Customer'
            ],
            [
                'module' => 'customer',
                'name' => 'customer-edit',
                'display_name' => 'Edit Customer',
                'description' => 'Edit Customer'
            ],
            [
                'module' => 'customer',
                'name' => 'customer-delete',
                'display_name' => 'Delete Customer',
                'description' => 'Delete Customer'
            ],
            [
                'module' => 'supplier',
                'name' => 'supplier-list',
                'display_name' => 'Display Suppliers Listing',
                'description' => 'See only Listing Of Suppliers'
            ],
            [
                'module' => 'supplier',
                'name' => 'supplier-create',
                'display_name' => 'Create Supplier',
                'description' => 'Create New Supplier'
            ],
            [
                'module' => 'supplier',
                'name' => 'supplier-edit',
                'display_name' => 'Edit Supplier',
                'description' => 'Edit Supplier'
            ],
            [
                'module' => 'supplier',
                'name' => 'supplier-delete',
                'display_name' => 'Delete Supplier',
                'description' => 'Delete Supplier'
            ],
            [
                'module' => 'warehouse',
                'name' => 'warehouse-list',
                'display_name' => 'Display Warehouses Listing',
                'description' => 'See only Listing Of Warehouses'
            ],
            [
                'module' => 'warehouse',
                'name' => 'warehouse-create',
                'display_name' => 'Create Warehouse',
                'description' => 'Create New Warehouse'
            ],
            [
                'module' => 'warehouse',
                'name' => 'warehouse-edit',
                'display_name' => 'Edit Warehouse',
                'description' => 'Edit Warehouse'
            ],
            [
                'module' => 'product',
                'name' => 'product-list',
                'display_name' => 'Display Products Listing',
                'description' => 'See only Listing Of Products'
            ],
            [
                'module' => 'product',
                'name' => 'product-create',
                'display_name' => 'Create Product',
                'description' => 'Create New Product'
            ],
            [
                'module' => 'product',
                'name' => 'product-edit',
                'display_name' => 'Edit Product',
                'description' => 'Edit Product'
            ],
            [
                'module' => 'product',
                'name' => 'product-delete',
                'display_name' => 'Delete Product',
                'description' => 'Delete Product'
            ],
            [
                'module' => 'sale',
                'name' => 'sale-list',
                'display_name' => 'Display Sales Listing',
                'description' => 'See only Listing Of Sales'
            ],
            [
                'module' => 'sale',
                'name' => 'sale-create',
                'display_name' => 'Create Sale',
                'description' => 'Create New Sale'
            ],
            [
                'module' => 'sale',
                'name' => 'sale-edit',
                'display_name' => 'Edit Sale',
                'description' => 'Edit Sale'
            ],
            [
                'module' => 'sale',
                'name' => 'sale-delete',
                'display_name' => 'Delete Sale',
                'description' => 'Delete Sale'
            ],
            [
                'module' => 'purchase',
                'name' => 'purchase-list',
                'display_name' => 'Display Purchases Listing',
                'description' => 'See only Listing Of Purchases'
            ],
            [
                'module' => 'purchase',
                'name' => 'purchase-create',
                'display_name' => 'Create Purchase',
                'description' => 'Create New Purchase'
            ],
            [
                'module' => 'purchase',
                'name' => 'purchase-edit',
                'display_name' => 'Edit Purchase',
                'description' => 'Edit Purchase'
            ],
            [
                'module' => 'purchase',
                'name' => 'purchase-delete',
                'display_name' => 'Delete Purchase',
                'description' => 'Delete Purchase'
            ],
            [
                'module' => 'transaction',
                'name' => 'transaction-list',
                'display_name' => 'Display Transaction Listing',
                'description' => 'See only Listing Of Transactions'
            ],
            [
                'module' => 'transaction',
                'name' => 'transaction-create',
                'display_name' => 'Create Transaction',
                'description' => 'Create New Transaction'
            ],
            [
                'module' => 'transaction',
                'name' => 'transaction-edit',
                'display_name' => 'Edit Transaction',
                'description' => 'Edit Transaction'
            ],
            [
                'module' => 'transaction',
                'name' => 'transaction-delete',
                'display_name' => 'Delete Transaction',
                'description' => 'Delete Transaction'
            ],
            [
                'module' => 'admin_transaction',
                'name' => 'admin_transaction-list',
                'display_name' => 'Display Admin Transaction Listing',
                'description' => 'See only Listing Of Admin Transactions'
            ],
            [
                'module' => 'admin_transaction',
                'name' => 'admin_transaction-create',
                'display_name' => 'Create Admin Transaction',
                'description' => 'Create New Admin Transaction'
            ],
            [
                'module' => 'admin_transaction',
                'name' => 'admin_transaction-edit',
                'display_name' => 'Edit Admin Transaction',
                'description' => 'Edit Admin Transaction'
            ],
            [
                'module' => 'admin_transaction',
                'name' => 'admin_transaction-delete',
                'display_name' => 'Delete Admin Transaction',
                'description' => 'Delete Admin Transaction'
            ],
            [
                'module' => 'stock',
                'name' => 'stocks-list',
                'display_name' => 'Display Stock Listing',
                'description' => 'See only Listing Of Stocks'
            ],
            [
                'module' => 'stock',
                'name' => 'stocks-create',
                'display_name' => 'Create Stock',
                'description' => 'Create New Stock'
            ],
            [
                'module' => 'stock',
                'name' => 'stocks-edit',
                'display_name' => 'Edit Stock',
                'description' => 'Edit Stock'
            ],
            [
                'module' => 'stock',
                'name' => 'stocks-delete',
                'display_name' => 'Delete Stock',
                'description' => 'Delete Stock'
            ],
            [
                'module' => 'refund',
                'name' => 'refund-list',
                'display_name' => 'Display Refund Listing',
                'description' => 'See only Listing Of Refunds'
            ],
            [
                'module' => 'refund',
                'name' => 'refund-create',
                'display_name' => 'Create Refund',
                'description' => 'Create New Refund'
            ],
            [
                'module' => 'refund',
                'name' => 'refund-edit',
                'display_name' => 'Edit Refund',
                'description' => 'Edit Refund'
            ],
            [
                'module' => 'refund',
                'name' => 'refund-delete',
                'display_name' => 'Delete Refund',
                'description' => 'Delete Refund'
            ],
            [
                'module' => 'report',
                'name' => 'report-list',
                'display_name' => 'Display Reports Listing',
                'description' => 'See only Listing Of Reports'
            ],
            [
                'module' => 'report',
                'name' => 'report-create',
                'display_name' => 'Create Report',
                'description' => 'Create New Report'
            ],
            [
                'module' => 'report',
                'name' => 'report-edit',
                'display_name' => 'Edit Report',
                'description' => 'Edit Report'
            ],
            [
                'module' => 'report',
                'name' => 'report-delete',
                'display_name' => 'Delete Report',
                'description' => 'Delete Report'
            ],
            [
                'module' => 'purchase',
                'name'=>'product-show-purchase-price',
                'display_name'=>'Allow See Purchase Price',
                'description'=>'Allow Display Purchase Price'
            ],
            [
                'module' => 'stock manage',
                'name' => "inventory-in",
                'display_name' => "Inventory In",
            ],            [
                'module' => 'stock manage',
                'name' => "inventory-out",
                'display_name' => "Inventory Out",
            ],            [
                'module' => 'stock manage',
                'name' => "warehouse-transfer",
                'display_name' => "Warehouse Transfer",
            ],
            [
                'module' => 'sale',
                'name' => "confirm-quotation",
                'display_name' => "Confirm Quotation",
            ],
            [
                'module' => 'sale',
                'name' => "update-status",
                'display_name' => "Sale Order Status Update",
            ],
            [
                'module' => 'sale',
                'name' => "fixed-discount",
                'display_name' => "Fixed Discount Allow",
            ],
            [
                'module' => 'sale',
                'name' => "allow-edit-sale-price",
                'display_name' => "Allow to edit Sale Price in Sales",
            ],
            [
                'module' => 'Sales Person',
                'name' => "sales-person",
                'display_name' => 'Display Sales Person Listing',
                'description' => 'Allow to see listings of Sales person'
            ],
            [
                'module' => 'Sales Person',
                'name' => "salesPerson-create",
                'display_name' => 'Allow to create new Sales Person',
                'description' => 'Allow to create new Sales person'
            ],
            [
                'module' => 'Sales Person',
                'name' => "salesPerson-list",
                'display_name' => 'Allow to show a single Sales Person',
                'description' => 'Allow to show a single Sales person'
            ],
            
            [
                'module' => 'Sales Person',
                'name' => "salesPerson-edit",
                'display_name' => 'Allow to edit Sales Person',
                'description' => 'Allow to edit Sales person'
            ],
            
            [
                'module' => 'Sales Person',
                'name' => "salesPerson-delete",
                'display_name' => 'Allow to delete Sales Person',
                'description' => 'Allow to delete Sales person'
            ],
            
            [
                'module' => 'Sales Person',
                'name' => "salesPerson-import-export",
                'display_name' => 'Allow to import Sales Person',
                'description' => 'Allow to to export excel file of Sales person'
            ],
            
            [
                'module' => 'Sales Person',
                'name' => "SalesPerson-import-import",
                'display_name' => 'Allow to import Sales Person ',
                'description' => 'Allow to import excel file of Sales person'
            ],
            
        ];


        // if (Permission::first())
        // {
        //     return true;
        // }

        foreach ($permission as $key => $value) {
            if (!Permission::where('name', $value['name'])->first()) {
                Permission::create($value);
            }
        }
    }
}
