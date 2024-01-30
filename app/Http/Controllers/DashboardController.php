<?php


namespace App\Http\Controllers;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Traits\StripeTrait;

class DashboardController extends Controller
{
    use StripeTrait;

    public function index(Request $request){

        $user = $request->user();

        $stripe_balance = $this->getCustomerBalance( $user->stripe_id );

        $balance_view = [ 
            'amount' => 0,
            'currency' => strtoupper( config('cashier.currency') ),
        ];
        
        $balance = [
            'available' => $balance_view,
            'pending' => $balance_view,
        ];

        if( $stripe_balance ){
            foreach( $stripe_balance->available as $bal ){
                if( $bal->currency == config('cashier.currency') ){
                    $balance['available']['amount'] = $bal->amount;
                }
            }
        }        

        return Inertia::render('Dashboard', [
            'balance' => $balance
        ]);
    }

    public function add_funds(Request $request){

        $user = $request->user();
       try {
            $payload = new \stdClass;
            $payload->stripe_id = $user->stripe_id;
            $payload->amount = $request->amount;
            $this->createPaymentIntent($payload);

            $customer = $this->getCustomer($user->stripe_id);

       } catch (\Exception $e) {
           error_log($e->getMessage() );
           return Inertia::share('error', $e->getMessage() );            
       }

        return redirect(RouteServiceProvider::HOME);
    }
}
