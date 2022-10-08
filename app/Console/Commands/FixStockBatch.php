<?php

namespace App\Console\Commands;

use App\StockManage;
use App\SupplierPriceRecord;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixStockBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:updateStockBatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Batch number in stock log against purchase';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $records = SupplierPriceRecord::all();
        foreach ($records as $record) {
            // $this->line("RECORD: ". json_encode($record));
            $logs = StockManage::where([
                'supplier_id' => $record->supplier_id,
                'product_id' => $record->product_id,
                'type' => 'purchase',
                // 'date' => $record->date
            ])->where('date', '<=', $record->date)
            ->update(['batch_id' => $record->id]);
            $this->warn("ProductID {$record->product_id} counts " . $logs);
            // ->update(['batch_id' => $record->id]);
        }
    }
}
