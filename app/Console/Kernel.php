<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\collectnotifications::class,
        Commands\RunEveningReport::class,
        Commands\PurchaseToPurchaseInvoice::class,
        Commands\PurchaseInvoiceToSupplierPriceList::class,
        Commands\FixPurchaseSupplierPricing::class,
        Commands\FixPostedOrderTransactions::class,
        Commands\ProcessExcelFileWithQuantity::class,
        Commands\FixPurchaseOrderPricing::class,
        Commands\FixStockBatch::class,
        Commands\SupplierTableToCustomer::class,
        Commands\MergeCustomerProfiles::class,
        Commands\MergeProductProfiles::class,
        Commands\UpdateInvoiceTotal::class,
        Commands\ImportProductsWithSupplier::class,

        //for demo purpose only. Do not use these commands on production environment
        Commands\AddCustomersForDemo::class,
        Commands\AddProductsForDemo::class,
        Commands\AddSaleForDemo::class,
        Commands\AddSuppliersForDemo::class,
        Commands\AddPurchaseForDemo::class,
        Commands\AddTransactionsForDemo::class,
        Commands\ProcessExcelFileSp::class,
        Commands\FixInvoiceTotal::class,
        Commands\FixTransactionWithInvoices::class,
        Commands\SettelCustomerBalance::class,
        Commands\FixMinSalePriceIssue::class,
        Commands\DetectAndRemoveEmptyPurchase::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('collectnotifications')->timezone('Asia/Karachi')->dailyAt('09:00');
        // $schedule->command('report:end')->timezone('Asia/Karachi')->dailyAt('21:00');
    }
}
