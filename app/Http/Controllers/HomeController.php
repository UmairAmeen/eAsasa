<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Requests\ProfileRequest;

use Illuminate\Http\Request;
use App;
use App\User;
use App\License;
use App\Products;
use App\Transaction;
use App\ChequeManager;
use App\Customer;
use App\SaleOrder;
use App\Setting;
use Illuminate\Support\Facades\DB;
use Hash;
use File;
use Session;
use Artisan;
use PDF;
use Hisune\EchartsPHP\ECharts;
use Illuminate\Support\Facades\View;

class HomeController extends Controller
{
    public function __construct()
    {
        View::share('load_head', true);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        session()->put('settings', Setting::GetCurrentSettings());
        $notice_stock = count(stock_notice());
        $total_product = Products::count();
        $transaction_count = Transaction::where('release_date', date('Y-m-d'))->count('amount');
        $transaction_amount = Transaction::where('release_date', date('Y-m-d'))->sum('amount');
        $chque = ChequeManager::where('release_date', date('Y-m-d'))->count('amount') + $transaction_count;
        $chque_worth = ChequeManager::where('release_date', date('Y-m-d'))->sum('amount') + $transaction_amount;
        $customer_call = Customer::where('after_last_payment', '=', DB::raw('DATEDIFF(NOW(), last_contact_on)'))
            ->where('payment_notify', true)->get();
        $finStats = [];
        if (is_admin()) {
            $finStats = Transaction::whereNull('deleted_at')
                ->select(DB::raw('SUM(amount) as amount'), 'type', 'payment_type')
                ->groupBy('type')->groupBy('payment_type')
                ->orderBy('type', 'asc')->get()->toArray();
        }
        $customer_call_count = 0;
        foreach ($customer_call as $key => $value) {
            if (!getCustomerBalance($value->id)) {
                continue;
            }
            $customer_call_count++;
        }
        // dd(SaleOrder::where('delivery_date', '<=', date('Y-m-d'))->get());
        // $debit_transactions = Transaction::where('type','in')->select(DB::raw('sum(amount) as total, DATE_FORMAT(date,"%M-%Y") as xyz'))
        //     ->groupBy(DB::raw('xyz'))->get();
        View::share('dashboard', true);

        $fin_cash = [0,0,0,0];
        $fin_bank = [0,0,0,0];
        $fin_online = [0,0,0,0];

        foreach ($finStats as $stat) {
            if ($stat['payment_type'] == 'cash') {
                if ($stat['type'] == 'in') {
                    $fin_cash[0] = (int)$stat['amount'];
                }
                if ($stat['type'] == 'out') {
                    $fin_cash[1] = (int)$stat['amount'];
                }
                if ($stat['type'] == 'expense') {
                    $fin_cash[2] = (int)$stat['amount'];
                }
            }
            if ($stat['payment_type'] == 'cheque') {
                if ($stat['type'] == 'in') {
                    $fin_bank[0] = (int)$stat['amount'];
                }
                if ($stat['type'] == 'out') {
                    $fin_bank[1] = (int)$stat['amount'];
                }
                if ($stat['type'] == 'expense') {
                    $fin_bank[2] = (int)$stat['amount'];
                }
            }
            if ($stat['payment_type'] == 'transfer') {
                if ($stat['type'] == 'in') {
                    $fin_online[0] = (int)$stat['amount'];
                }
                if ($stat['type'] == 'out') {
                    $fin_online[1] = (int)$stat['amount'];
                }
                if ($stat['type'] == 'expense') {
                    $fin_online[2] = (int)$stat['amount'];
                }
            }
        }

        $fin_cash[3] = $fin_cash[0] - $fin_cash[1] - $fin_cash[2];
        $fin_bank[3] = $fin_bank[0] - $fin_bank[1] - $fin_bank[2];
        $fin_online[3] = $fin_online[0] - $fin_online[1] - $fin_online[2];

        $finData = json_encode([
            ['name' => 'Cash', 'color' => '#4caf50', 'data' => $fin_cash],
            ['name' => 'Online','color' => '#4ECDC4', 'data' => $fin_online],
            ['name' => 'Bank', 'color' => '#406882', 'data' => $fin_bank],
        ]);
        return view('home', compact('notice_stock', 'finData', 'total_product', 'customer_call_count', 'chque', 'chque_worth', 'finStats'));
    }

    /**
     * Show the user profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        View::share('dashboard', true);
        $name = \Auth::user()->name;
        $license = \Session::get('license');
        return view('profile', compact('license', 'name'));
    }

    public function saveprofile(ProfileRequest $req)
    {
        // print_r(\Auth::user()->id);die;
        $user = auth()->user();
        // dd($user);
        $user->name = $req->name;
        if ($req->password) {
            $user->password = Hash::make($req->password);
        }
        $user->save();

        $license = License::first();
        $license->license = $req->license;
        $license->save();

        // $profilePhoto = $req->file('profile_photo');

        // if ($profilePhoto) {
        //     $imageName = \Auth::user()->id . '_' . \Auth::user()->name . '.' . $profilePhoto->getClientOriginalExtension();
        //     if (File::exists($imageName))
        //     {
        //         File::delete($imageName);
        //     }

        //     $req->file('profile_photo')->move(public_path() . '/uploads', $imageName);
        // }

        return response()->json(['message' => 'Profile updated','action'=>'redirect','do'=>url('/profile')], 200);
        // return redirect()->back()->with('success', ['Profile Updated']);
    }
    public function clearCache()
    {
        Artisan::call("cache:clear");
        Artisan::call("route:clear");
        Artisan::call('config:clear');
        return back();
    }

    public function clearSession(){
        Session::flush();
        return redirect('/logout');
    }

    public function checkNotification()
    {
        Artisan::call('collectnotifications');
        Artisan::call('report:end');
        return back();
    }

    public function generatePDF($flag = '')
    {
        $pdf = App::make('snappy.pdf.wrapper');
        $pdf->loadHTML('<h1>Test</h1>');
        return $pdf->inline();
        // return PDF::loadFile('http://www.github.com')->inline('github.pdf');
    }
    
    public function api_login(Request $req){
		$user = App\User::where('email',$req->email)->first();
		if(Hash::check($req->password, $user->password)){
			return response()->json(['success'=>true,'hash'=>Hash::make($user->email.$user->password)]);
		}
		return response()->json(['success'=>false,'message'=>'Login Credentials are invalid']);
    }
}
