<?php


namespace App\Http\Controllers;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;
use Stripe\Balance as StripeBalance;
use Stripe\PaymentIntent as StripePaymentIntent;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\Customer as StripeCustomer;
use Auth;

class DashboardController extends Controller
{

    public function index(Request $request){

        $user = $request->user();

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $stripe_balance = StripeBalance::retrieve([
            ['stripe_account' => Auth::user()->stripe_id]
        ]);

        $balance_view = [ 
            'amount' => 0,
            'currency' => strtoupper( config('cashier.currency') ),
        ];
        
        $balance = [
            'available' => $balance_view,
            'pending' => $balance_view,
        ];

        foreach( $stripe_balance->available as $bal ){
            if( $bal->currency == config('cashier.currency') ){
                $balance['available']['amount'] = $bal->amount;
            }
        }
        

        return Inertia::render('Dashboard', [
            'balance' => $balance
        ]);
    }

    public function add_funds(Request $request){

        $user = Auth::user();
        $stripe_id = $user->stripe_id;
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            //hardcoded values for testing
            $paymentIntent = StripePaymentIntent::create([
                'customer' => $stripe_id,
                'amount' => ( $request->amount * 100 ),
                'currency' => config('cashier.currency'),
                'payment_method' => 'pm_card_visa',
                'description' => 'Add funds to customer balance',
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);

            $paymentIntent->confirm();

            $updated = StripeCustomer::retrieve($stripe_id);

        } catch (\Exception $e) {
            error_log($e->getMessage() );

            return Inertia::share('error', $e->getMessage() );
            
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
