<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class FlutterwavePaymentController extends Controller
{
    public $secret_key;
    public $public_key;
    public $is_enabled;
    protected $invoiceData;


    public function paymentConfig()
    {

        $payment_setting = Utility::getCompanyPaymentSetting($this->invoiceData->created_by);


        $this->secret_key = isset($payment_setting['flutterwave_secret_key']) ? $payment_setting['flutterwave_secret_key'] : '';
        $this->public_key = isset($payment_setting['flutterwave_public_key']) ? $payment_setting['flutterwave_public_key'] : '';
        $this->is_enabled = isset($payment_setting['is_flutterwave_enabled']) ? $payment_setting['is_flutterwave_enabled'] : 'off';

        return $this;
    }


    public function customerPayWithFlutterwave(Request $request)
    {
        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($request->invoice_id);
        $invoice   = Invoice::find($invoiceID);
        $user      = User::find($invoice->created_by);
        if($invoice)
        {
            $price = $request->amount;
            if($price > 0)
            {
                $res_data['email']       =$user->email;
                $res_data['total_price'] = $price;
                $res_data['currency']    = Utility::getValByName('site_currency');
                $res_data['flag']        = 1;

                return $res_data;

            }
            else
            {
                $res['msg']  = __("Enter valid amount.");
                $res['flag'] = 2;

                return $res;
            }

        }
        else
        {
            return redirect()->back()->with('error', __('Invoice is deleted.'));

        }


    }

    public function getInvoicePaymentStatus(Request $request, $pay_id, $invoice_id)
    {

        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice   = Invoice::find($invoiceID);
        $this->invoiceData = $invoice;

        $orderID   = strtoupper(str_replace('.', '', uniqid('', true)));
        $settings  = DB::table('settings')->where('created_by', '=', $invoice->created_by)->get()->pluck('value', 'name');

        $payment   = $this->paymentConfig();
        $result    = array();

        if($invoice)
        {
            try
            {

                $data = array(
                    'txref' => $pay_id,
                    'SECKEY' => $this->secret_key,
                    //secret key from pay button generated on rave dashboard
                );
                // make request to endpoint using unirest.
                $headers = array('Content-Type' => 'application/json');
                $body    = \Unirest\Request\Body::json($data);
                $url     = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify"; //please make sure to change this to production url when you go live

                // Make `POST` request and handle response with unirest
                $response = \Unirest\Request::post($url, $headers, $body);
                if(!empty($response))
                {
                    $response = json_decode($response->raw_body, true);
                }
                if(isset($response['status']) && $response['status'] == 'success')
                {
                    $paydata = $response['data'];

                    $payments = InvoicePayment::create(
                        [

                            'invoice_id' => $invoice->id,
                            'date' => date('Y-m-d'),
                            'amount' => $request->amount,
                            'payment_method' => 1,
                            'order_id' => $orderID,
                            'payment_type' => __('Flutterwave'),
                            'receipt' => '',
                            'description' => __('Invoice') . ' ' . Utility::invoiceNumberFormat($settings, $invoice->invoice_id),
                        ]
                    );

                    $invoice = Invoice::find($invoice->id);


                    if($invoice->getDue() <= 0)
                    {
                        Invoice::change_status($invoice->id, 4);
                    }
                    else
                    {
                        Invoice::change_status($invoice->id, 3);
                    }

                    //Slack Notification
                    $setting  = Utility::settings($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    if(isset($setting['payment_notification']) && $setting['payment_notification'] == 1)
                    {
                        $msg = __("New payment of").' ' . $request->amount . __("created for").' '. $customer->name . __("by").' '. __('Flutterwave'). '.';
                        Utility::send_slack_msg($msg,$invoice->created_by);
                    }

                    //Telegram Notification
                    $setting  = Utility::settings($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    if(isset($setting['telegram_payment_notification']) && $setting['telegram_payment_notification'] == 1)
                    {
                        $msg = __("New payment of").' ' . $request->amount . __("created for").' '. $customer->name . __("by").' '. __('Flutterwave'). '.';
                        Utility::send_telegram_msg($msg,$invoice->created_by);
                    }

                    //Twilio Notification
                    $setting  = Utility::settings($invoice->created_by);
                    $customer = Customer::find($invoice->customer_id);
                    if(isset($setting['twilio_payment_notification']) && $setting['twilio_payment_notification'] ==1)
                    {
                        $msg = __("New payment of").' ' . $request->amount . __("created for").' ' . $customer->name . __("by").' '.  $payments['payment_type'] . '.';
                        Utility::send_twilio_msg($customer->contact,$msg,$invoice->created_by);
                    }


                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('success', __(' Payment successfully added.'));

                }
                else
                {
                    return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Transaction has been failed! '));
                }
            }
            catch(\Exception $e)
            {
                return redirect()->route('invoice.link.copy', Crypt::encrypt($invoice->id))->with('error', __('Invoice not found!'));
            }
        }
    }
}
