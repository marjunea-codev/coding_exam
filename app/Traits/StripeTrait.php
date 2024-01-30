<?php

namespace App\Traits;

use Stripe\Stripe;
use Stripe\Customer as StripeCustomer;
use Stripe\Balance as StripeBalance;
use Stripe\PaymentIntent as StripePaymentIntent;

trait StripeTrait 
{
  public function connectToStripe():void
  {    
    Stripe::setApiKey( env('STRIPE_SECRET') );
  }

  public function createStripeCustomer( $data )
  {
    if( !isset($data->name) OR 
      !isset($data->email)
    ){
      throw new Exception('Missing name or email field.');
    }

    try {
      return StripeCustomer::create([
        'name' => $data->name,
        'email' => $data->email,
        'description' => 'Stripe customer',
      ]);
    } catch(\Exception $e){
      error_log($e->getMessage());
      throw new Exception( $e->getMessage() );
    }
  }

  public function getCustomerBalance( $stripe_id ):StripeBalance
  {
    if( empty($stripe_id) ){
      return null;
    }

    $this->connectToStripe();

    return StripeBalance::retrieve([
      ['stripe_account' => $stripe_id]
    ]);
  }

  public function createPaymentIntent( $data ):void
  {
    if( is_array($data) ){
      $data = (object) $data;
    }

    if( !isset($data->stripe_id) OR 
      empty($data->stripe_id) ){
        throw new \Exception('Missing stripe ID.');
    }

    try {

      $this->connectToStripe();

      $paymentIntent = StripePaymentIntent::create([
        'customer' => $data->stripe_id,
        'amount' => ($data->amount * 100),
        'currency' => config('cashier.currency'),
        'payment_method' => 'pm_card_visa',
        'description' => 'Add funds to customer balance',
        'automatic_payment_methods' => [
          'enabled' => true,
          'allow_redirects' => 'never',
        ]
      ]);

      $paymentIntent->confirm();
    } catch( \Exception $e){
      error_log($e->getMessage());
      throw new \Exception($e->getMessage() );
    }

  }

  public function getCustomer( $stripe_id )
  {

    if( !isset($stripe_id) OR 
      empty($stripe_id) ){
        throw new \Exception('Missing stripe ID.');
    }

    try {

      $this->connectToStripe();
      return StripeCustomer::retrieve($stripe_id);
    } catch( \Exception $e){
      error_log($e->getMessage());
      throw new \Exception($e->getMessage());
    }
  }
}