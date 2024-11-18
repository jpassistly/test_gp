<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Razorpay\Api\Api;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    //
    public function make_payment($customer_id, $recharge_amount, $payment_type)
    {
        $customer_id = $customer_id;
        $payment_amount = $recharge_amount;
        $payment_type = $payment_type;
        $description = 'Payment for Order';
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $customer_data = Customer::where('id', $customer_id)->first();
        $now = Carbon::now();
        $expire_at = $now->addMinutes(20);
        $reference_id = 'REF' . Str::uuid();
        $expire_at_timestamp = $expire_at->timestamp;

        $order = $api->order->create([
            'amount' => $recharge_amount * 100,
            'currency' => 'INR',
            'receipt' => $reference_id, // Using reference_id as receipt for simplicity
            'notes' => [
                'policy_name' => 'Jeevan Bima',
            ],
        ]);

        $razorpayOrderId = $order->id;

        if ($payment_type == 1) {
            $razorpayOrder = $api->paymentLink->create(
                array(
                    'amount' => $payment_amount * 100,
                    'currency' => 'INR',
                    'accept_partial' => true,
                    'first_min_partial_amount' => 100,
                    'expire_by' => $expire_at_timestamp,
                    'reference_id' => $reference_id,
                    'description' => $description,
                    'customer' => array(
                        'name' => ($customer_data['name'] != '') ? $customer_data['name'] : 'User',
                        'contact' => $customer_data['mobile']
                    ),
                    'notify' => array('sms' => true),
                    'reminder_enable' => true,
                    'notes' => array('policy_name' => 'Jeevan Bima'),
                    'callback_url' => 'https://example-callback-url.com/',
                    'callback_method' => 'get',
                )
            );

        } else {

            $razorpayOrder = $api->paymentLink->create(
                array(
                    'upi_link' => true,
                    'amount' => $payment_amount * 100,
                    'currency' => 'INR',
                    'expire_by' => $expire_at_timestamp,
                    'reference_id' => $reference_id,
                    'description' => $description,
                    'customer' => array(
                        'name' => ($customer_data['name'] != '') ? $customer_data['name'] : 'User',
                        'contact' => $customer_data['mobile']
                    ),
                    'notify' => array('sms' => true, ),
                    'reminder_enable' => true,
                    'notes' => array('policy_name' => 'Jeevan Bima')
                )
            );

        }

        $payment_details = [
            'razorpayorderid' => $razorpayOrderId,
            'razorpaylink' => $razorpayOrder,
        ];

        return $payment_details;

    }

    public function getPaymentDetails($razorpay_payment_id)
    {

        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));


        try {
            // Fetch the payment details
            $payment = $api->payment->fetch($razorpay_payment_id);
            return $payment;

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
