<?php

namespace App\Http\Controllers\FrontEnd\Booking\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FrontEnd\Booking\ServicePaymentController;
use App\Http\Helpers\CheckLimitHelper;
use App\Models\PaymentGateway\OnlineGateway;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffGlobalHour;
use App\Models\Staff\StaffServiceHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalController extends Controller
{
  private $api_context;

  public function __construct()
  {
    $data = OnlineGateway::whereKeyword('paypal')->first();
    $paypalData = json_decode($data->information, true);

    $paypal_conf = Config::get('paypal');
    $paypal_conf['client_id'] = $paypalData['client_id'];
    $paypal_conf['secret'] = $paypalData['client_secret'];
    $paypal_conf['settings']['mode'] = $paypalData['sandbox_status'] == 1 ? 'sandbox' : 'live';

    $this->api_context = new ApiContext(
      new OAuthTokenCredential(
        $paypal_conf['client_id'],
        $paypal_conf['secret']
      )
    );

    $this->api_context->setConfig($paypal_conf['settings']);
  }

  public function index(Request $request, $paymentFor)
  {
    Log::info('PayPal service booking init started', [
      'host' => $request->getHost(),
      'session_id' => $request->session()->getId(),
      'payment_for' => $paymentFor,
      'has_service_data' => Session::has('serviceData'),
      'booking_date' => $request->input('bookingDate'),
      'service_hour_id' => $request->input('serviceHourId'),
      'staff_id' => $request->input('staffId'),
      'user_id' => $request->input('user_id'),
    ]);

    if (Session::has('serviceData')) {
      $serviceData = Session::get('serviceData');
    } else {
      Log::warning('PayPal init aborted because serviceData session is missing', [
        'session_id' => $request->session()->getId(),
      ]);
      return redirect()->back()->with('error', 'Something went wrong');
    }

    $service = \App\Models\Services\Services::find($serviceData['service_id'] ?? null);
    $vendorId = $service ? (int) ($service->vendor_id ?? 0) : 0;
    $serviceData['vendor_id'] = $vendorId;
    
    // Get service_ammount from session or database
    if (!isset($serviceData['service_ammount']) || empty($serviceData['service_ammount'])) {
      $serviceData['service_ammount'] = $service ? $service->price : 0;
    }
    
    //check membership expire date
    $expireDate = $vendorId == 0 ? null : checkMembersipExpireDate($vendorId);
    if ($vendorId != 0 && empty($expireDate)) {
      return redirect()->back()->with('error', 'Vendor membership is not active. Please contact support.');
    }

    if ($vendorId != 0 && $request['bookingDate'] > $expireDate) {
      return redirect()->back()->with('error', 'Vendor membership has expired. Please contact support.');
    }
    
    $countAppointment = CheckLimitHelper::countAppointment($vendorId);
    if ($countAppointment > 0) {

      $customerPaid = $serviceData['service_ammount'];
      $currencyInfo = $this->getCurrencyInfo();

      // changing the currency before redirect to PayPal
    //   if ($currencyInfo->base_currency_text === 'GBP') {
    //     $rate = floatval($currencyInfo->base_currency_rate);
    //     $convertedTotal = $customerPaid / $rate;
    //   }

    //   $paypalTotal = $currencyInfo->base_currency_text === 'USD' ? $customerPaid : $convertedTotal;
    
     $paypalTotal = $customerPaid;

      $staff = Staff::find($request['staffId']);
      
      // Safety check: if staff doesn't exist, get the first staff member for this service owner.
      if (!$staff) {
        $staff = Staff::where('vendor_id', $vendorId)->first();
      }
      
      // Final safety check: if still no staff, create default data
      if (!$staff) {
        return redirect()->back()->with('error', 'Staff information not available. Please contact support.');
      }

      if (strpos($request['serviceHourId'], 'default_') === 0) {
        $timeId = str_replace('default_', '', $request['serviceHourId']);
        $startTime = substr($timeId, 0, 2) . ':' . substr($timeId, 2, 2) . ':' . substr($timeId, 4, 2);
        
        $staffHour = new \stdClass();
        $staffHour->start_time = $startTime;
        $staffHour->end_time = \Carbon\Carbon::parse($startTime)->addHours(2)->format('H:i:s');
      } else {
        if ($staff->is_day == 1) {
          $staffHour = StaffServiceHour::find($request['serviceHourId']);
        } else {
          $staffHour = StaffGlobalHour::find($request['serviceHourId']);
        }
      }

      if (!$staffHour) {
        return redirect()->back()->with('error', 'Selected time slot is no longer available.');
      }

      $arrData = array(
        'zoom_status' => $serviceData['zoom_status'] ?? 0,
        'calender_status' => $serviceData['calendar_status'] ?? 0,
        'customer_name' => $request['name'],
        'customer_phone' => $request['phone'],
        'customer_email' => $request['email'],
        'customer_address' => $request['address'],
        'customer_zip_code' => $request['zip_code'],
        'customer_country' => $request['country'],
        'start_date' => $staffHour->start_time,
        'end_date' => $staffHour->end_time,
        'booking_date' => $request['bookingDate'],
        'service_hour_id' => $request['serviceHourId'],
        'staff_id' => $request['staffId'],
        'max_person' => $request['max_person'],
        'service_id' => $serviceData['service_id'],
        'user_id' => $request['user_id'],
        'vendor_id' => $vendorId,
        'customer_paid' => $customerPaid,
        'currencyText' => $currencyInfo->base_currency_text,
        'currencyTextPosition' => $currencyInfo->base_currency_text_position,
        'currencySymbol' => $currencyInfo->base_currency_symbol,
        'currencySymbolPosition' => $currencyInfo->base_currency_symbol_position,
        'paymentMethod' => 'PayPal',
        'gatewayType' => 'online',
        'paymentStatus' => 'completed',
        'orderStatus' => 'pending',
        'refund' => 'pending',
        'question_answers' => $serviceData['question_answers'] ?? null
      );

      $title = 'Service Booking';
      $notifyURL = route('frontend.service_booking.paypal.notify');
      $cancelURL = route('frontend.service_booking.cancel');

      $payer = new Payer();
      $payer->setPaymentMethod('paypal');

      $item_1 = new Item();
      $item_1->setName($title)
        /** item name **/
        ->setCurrency('GBP')
        ->setQuantity(1)
        ->setPrice($paypalTotal);
      /** unit price **/
      $item_list = new ItemList();
      $item_list->setItems(array($item_1));

      $amount = new Amount();
      $amount->setCurrency('GBP')
        ->setTotal($paypalTotal);

      $transaction = new Transaction();
      $transaction->setAmount($amount)
        ->setItemList($item_list)
        ->setDescription($title . ' via PayPal');

      $redirect_urls = new RedirectUrls();
      $redirect_urls->setReturnUrl($notifyURL)
        /** Specify return URL **/
        ->setCancelUrl($cancelURL);

      $payment = new Payment();
      $payment->setIntent('Sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirect_urls)
        ->setTransactions(array($transaction));

      try {
        $payment->create($this->api_context);
      } catch (\Exception $ex) {
        Log::error('PayPal payment creation failed', [
          'session_id' => $request->session()->getId(),
          'message' => $ex->getMessage(),
        ]);
        return redirect($cancelURL)->with('error', $ex->getMessage());
      }


      foreach ($payment->getLinks() as $link) {
        if ($link->getRel() == 'approval_url') {
          $redirectURL = $link->getHref();
          break;
        }
      }

      // put some data in session before redirect to paypal url
      $request->session()->put('paymentFor', $paymentFor);
      $request->session()->put('arrData', $arrData);
      $request->session()->put('paymentId', $payment->getId());

      Log::info('PayPal payment session prepared', [
        'session_id' => $request->session()->getId(),
        'payment_id' => $payment->getId(),
        'has_arr_data' => $request->session()->has('arrData'),
      ]);

      if (isset($redirectURL)) {
        /** redirect to paypal **/
        Log::info('PayPal redirect URL generated', [
          'session_id' => $request->session()->getId(),
          'payment_id' => $payment->getId(),
        ]);
        return response()->json(['redirectURL' => $redirectURL]);
      }
    } else {
      Log::warning('PayPal init blocked by appointment limit', [
        'session_id' => $request->session()->getId(),
      ]);
      return redirect()->back()->with('error', 'Please Contact Support');
    }
  }

  public function notify(Request $request)
  {
    // get the information from session
    $paymentPurpose = $request->session()->get('paymentFor');
    $arrData = $request->session()->get('arrData');
    $paymentId = $request->session()->get('paymentId');

    $urlInfo = $request->all();

    Log::info('PayPal notify received', [
      'host' => $request->getHost(),
      'full_url' => $request->fullUrl(),
      'session_id' => $request->session()->getId(),
      'payment_purpose' => $paymentPurpose,
      'has_arr_data' => !empty($arrData),
      'payment_id' => $paymentId,
      'query' => array_intersect_key($urlInfo, array_flip(['token', 'PayerID', 'paymentId'])),
    ]);

    if (empty($urlInfo['token']) || empty($urlInfo['PayerID'])) {
      Log::warning('PayPal notify missing token or PayerID', [
        'session_id' => $request->session()->getId(),
        'query' => $urlInfo,
      ]);
      if ($paymentPurpose == 'service booking') {
        return redirect()->route('frontend.services');
      }
    }

    /** Execute The Payment **/
    $payment = Payment::get($paymentId, $this->api_context);
    $execution = new PaymentExecution();
    $execution->setPayerId($urlInfo['PayerID']);
    $result = $payment->execute($execution, $this->api_context);

    if ($result->getState() == 'approved') {
      $purchaseProcess = new ServicePaymentController();

      $bookings = $purchaseProcess->finalizeOrder($arrData);

      $bookings->each(function ($booking) {
        $type = 'service_payment_approved';
        payemntStatusMail($type, $booking->id);
      });

      // remove this session datas
      $request->session()->forget('paymentFor');
      $request->session()->forget('arrData');
      $request->session()->forget('paymentId');
      $request->session()->forget('serviceData');

      // redirect url here after succesfully payment
      Session::put('complete', 'payment_complete');
      Session::put('paymentInfo', $bookings->first());

      Log::info('PayPal payment approved and success session stored', [
        'session_id' => $request->session()->getId(),
        'booking_ids' => $bookings->pluck('id')->all(),
        'complete' => Session::get('complete'),
        'payment_info_id' => optional(Session::get('paymentInfo'))->id,
      ]);
      return redirect()->route('frontend.services');
    } else {
      Log::warning('PayPal payment execution did not return approved state', [
        'session_id' => $request->session()->getId(),
        'state' => $result->getState(),
      ]);
      $request->session()->forget('paymentFor');
      $request->session()->forget('arrData');
      $request->session()->forget('serviceData');
      $request->session()->forget('paymentId');

      if ($paymentPurpose == 'service booking') {
        return redirect()->back();
      }
    }
  }
}
