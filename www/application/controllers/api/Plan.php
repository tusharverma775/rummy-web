<?php

use Restserver\Libraries\REST_Controller;
use Razorpay\Api\Api;
use paytm\paytmchecksum\PaytmChecksum;

include APPPATH . '/libraries/REST_Controller.php';
include APPPATH . '/libraries/Format.php';
class Plan extends REST_Controller
{
    private $data;

    public function __construct()
    {
        parent::__construct();
        $header = $this->input->request_headers('token');

        if (!isset($header['Token'])) {
            $data['message'] = 'Invalid Request';
            $data['code'] = HTTP_UNAUTHORIZED;
            $this->response($data, HTTP_OK);
            exit();
        }

        if ($header['Token'] != getToken()) {
            $data['message'] = 'Invalid Authorization';
            $data['code'] = HTTP_METHOD_NOT_ALLOWED;
            $this->response($data, HTTP_OK);
            exit();
        }

        $this->data = $this->input->post();

        $this->load->model('Users_model');
        $this->load->model('Coin_plan_model');
        $this->load->model('Setting_model');
        $this->load->model('Gift_model');
    }

    public function index_post()
    {
        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $PlanDetails = $this->Coin_plan_model->List();
        if ($PlanDetails) {
            $data['code'] = HTTP_OK;
            $data['message'] = 'Success';
            $data['PlanDetails']=$PlanDetails;
            $this->response($data, 200);
        } else {
            $data['code'] = HTTP_NOT_FOUND;
            $data['message'] = 'Somthing Happend, try again later..';
            $this->response($data, 200);
        }
    }

    public function gift_post()
    {
        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $Gift = $this->Gift_model->List();
        if ($Gift) {
            $data['code'] = HTTP_OK;
            $data['message'] = 'Success';
            $data['Gift']=$Gift;
            $this->response($data, 200);
        } else {
            $data['code'] = HTTP_NOT_FOUND;
            $data['message'] = 'Somthing Happend, try again later..';
            $this->response($data, 200);
        }
    }

    public function paytm_token_api_Post()
    {
        $user_id = $this->input->post('user_id');

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $plan_id = $this->input->post('plan_id');

        if (empty($user_id) || empty($plan_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (empty($this->Users_model->UserProfile($user_id))) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $plan = $this->Coin_plan_model->View($plan_id);
        if (empty($plan)) {
            $data['message'] = 'Invalid Plan';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $Amount = $plan->price;             //Product Amount While the Time OF Order

        $Order_ID = $this->Coin_plan_model->GetCoin($user_id, $plan_id, $plan->coin, $Amount);

        if (empty($Order_ID)) {
            $data['message'] = 'Error while Creating Ticket';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        // create ORder in razor pay
        // $RazorPay_order = $this->RazorPay_order($Order_ID, $Amount);
        $Order_ID_paytm = $Order_ID;
        $paytm_body['orderId'] = $Order_ID_paytm;
        $paytm_body['websiteName'] = str_replace(' ', '', PROJECT_NAME);
        $paytm_body['amount'] = number_format($Amount, 2, '.', '');
        $paytm_body['currency'] = 'INR';
        $paytm_body['custId'] = $user_id;
        $paytm_body['callbackUrl'] = 'https://securegw.paytm.in/theia/paytmCallback?ORDER_ID='.$Order_ID;
        $paytm_body['requestType'] = 'Payment';

        $paytm_token = $this->paytm_token($paytm_body);

        $Update_Order_Master = $this->Coin_plan_model->UpdateOrder($user_id, $Order_ID, $paytm_token);


        if ($Update_Order_Master) {
            $data['order_id'] = $Order_ID_paytm;
            $data['Total_Amount'] = $Amount;
            $data['paytm_token'] = $paytm_token;
            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'Technical Error';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
    }

    public function paytm_token($data)
    {
        $setting = $this->Setting_model->Setting();
        $paytmParams = array();

        $paytm_url = ($setting->cashfree_stage=='PROD') ? PAYTM_LIVE_URL : PAYTM_TEST_URL;

        $paytmParams["body"] = array(
            "requestType" => $data['requestType'],
            "mid"         => $setting->paytm_mercent_id,
            "websiteName"   => "DEFAULT",
            "orderId"       => $data['orderId'],
            "callbackUrl"   => $paytm_url.'/theia/paytmCallback?ORDER_ID='.$data['orderId'],
            "txnAmount"     => array(
                "value"     => $data['amount'],
                "currency"  => $data['currency'],
            ),
            "userInfo"      => array(
                "custId"    => $data['custId'],
            ),
        );

        /*
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
        */
        // print_r(json_encode($paytmParams));
        // echo $setting->paytm_mercent_key;
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $setting->paytm_mercent_key);

        $paytmParams["head"] = array(
            "signature" => $checksum
        );

        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        // $paytm_url = ($setting->cashfree_stage=='PROD')?PAYTM_LIVE_URL:PAYTM_TEST_URL;
        // $paytm_url = PAYTM_TEST_URL;
        $url = $paytm_url."/theia/api/v1/initiateTransaction?mid=$setting->paytm_mercent_id&orderId=".$data['orderId'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        $response = curl_exec($ch);
        $reponse_arr = json_decode($response);
        // print_r($reponse_arr->body->resultInfo);
        return (isset($reponse_arr->body->txnToken)) ? $reponse_arr->body->txnToken : $reponse_arr->body->resultInfo->resultMsg;
    }

    public function paytm_pay_now_api_post()
    {
        $user_id = $this->input->post('user_id');
        $order_id = $this->input->post('order_id');

        if (empty($user_id) || empty($order_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $CheckTicket = $this->Coin_plan_model->GetUserByOrderId($order_id);
        if (empty($CheckTicket)) {
            $data['message'] = 'Invalid Ticket ID';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $setting = $this->Setting_model->Setting();
        /* initialize an array */
        $paytmParams = array();
        /* body parameters */
        $paytmParams["body"] = array(
            "mid" => $setting->paytm_mercent_id,
            "orderId" => $order_id,
        );
        /**
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys
        */
        $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $setting->paytm_mercent_key);

        /* head parameters */
        $paytmParams["head"] = array(
            /* put generated checksum value here */
            "signature"	=> $checksum
        );

        /* prepare JSON string for request */
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

        /* for Staging */
        $paytm_url = ($setting->cashfree_stage=='PROD') ? PAYTM_LIVE_URL : PAYTM_TEST_URL;
        $url = $paytm_url."/v3/order/status";

        /* for Production */
        // $url = "https://securegw.paytm.in/v3/order/status";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $response = curl_exec($ch);
        $response_arr = json_decode($response);

        if ($response_arr->body->resultInfo->resultStatus!='TXN_SUCCESS') {
            // Reject this call
            $data['message'] = $response_arr->body->resultInfo->resultMsg;
            $data['code'] = HTTP_UNAUTHORIZED;
            $this->response($data, 200);
            exit();
        }

        $Amount = $CheckTicket[0]->price;

        if ($CheckTicket[0]->payment==0 && $response_arr->body->txnAmount == $Amount) {
            $this->Coin_plan_model->UpdateOrderPayment($CheckTicket[0]->razor_payment_id);
            $this->Users_model->UpdateWalletOrder($CheckTicket[0]->coin, $CheckTicket[0]->user_id);


            for ($i=1; $i <= 3; $i++) {
                if ($user[0]->referred_by!=0) {
                    $level = 'level_'.$i;
                    $coins = (($CheckTicket[0]->coin*$setting->$level)/100);
                    $this->Users_model->UpdateWalletOrder($coins, $user[0]->referred_by);

                    $log_data = [
                        'user_id' => $user[0]->referred_by,
                        'purchase_id' => $order_id,
                        'purchase_user_id' => $user_id,
                        'coin' => $coins,
                        'level' => $i,
                    ];

                    $this->Users_model->AddPurchaseReferLog($log_data);
                    $user = $this->Users_model->UserProfile($user[0]->referred_by);
                } else {
                    break;
                }
            }

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'Invalid Payment';
            $data['code'] = HTTP_NOT_FOUND;
            $this->response($data, 200);
            exit();
        }
    }

    public function cashfree_token_api_Post()
    {
        $user_id = $this->input->post('user_id');

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $plan_id = $this->input->post('plan_id');

        if (empty($user_id) || empty($plan_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (empty($this->Users_model->UserProfile($user_id))) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $plan = $this->Coin_plan_model->View($plan_id);
        if (empty($plan)) {
            $data['message'] = 'Invalid Plan';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $Amount = $plan->price;             //Product Amount While the Time OF Order

        $Order_ID = $this->Coin_plan_model->GetCoin($user_id, $plan_id, $plan->coin, $Amount);

        if (empty($Order_ID)) {
            $data['message'] = 'Error while Creating Ticket';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        // create ORder in razor pay
        // $RazorPay_order = $this->RazorPay_order($Order_ID, $Amount);
        $cashfree_token = $this->cashkaro_token($Order_ID, $Amount);
        // print_r($cashfree_token->status);
        if ($cashfree_token->status=='OK') {
            $cftoken = $cashfree_token->cftoken;
        } else {
            $cftoken = $cashfree_token->message;
        }


        $Update_Order_Master = $this->Coin_plan_model->UpdateOrder($user_id, $Order_ID, $cftoken);


        if ($Update_Order_Master) {
            $data['order_id'] = $Order_ID;
            $data['Total_Amount'] = $Amount;
            $data['cftoken'] = $cftoken;
            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'Technical Error';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
    }

    public function cashkaro_token($order_id, $amount)
    {
        $setting = $this->Setting_model->Setting();
        $url = ($setting->cashfree_stage=='PROD') ? CLIENT_LIVE_URL : CLIENT_TEST_URL;
        // print_r($setting);
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url.'/api/v2/cftoken/order',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
        "orderId": '.$order_id.',
        "orderAmount":'.$amount.',
        "orderCurrency": "INR"
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'x-client-id: '.$setting->cashfree_client_id,
            'x-client-secret: '.$setting->cashfree_client_secret
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);
    }

    public function cashfree_pay_now_api_Post()
    {
        $user_id = $this->input->post('user_id');
        $order_id = $this->input->post('order_id');

        if (empty($user_id) || empty($order_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $CheckTicket = $this->Coin_plan_model->GetUserByOrderId($order_id);
        if (empty($CheckTicket)) {
            $data['message'] = 'Invalid Ticket ID';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $setting = $this->Setting_model->Setting();

        $orderAmount = $_POST["orderAmount"];
        $referenceId = $_POST["referenceId"];
        $txStatus = $_POST["txStatus"];
        $paymentMode = $_POST["paymentMode"];
        $txMsg = $_POST["txMsg"];
        $txTime = $_POST["txTime"];
        $signature = $_POST["signature"];
        $cashfree_data = $order_id.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime;
        $hash_hmac = hash_hmac('sha256', $cashfree_data, $setting->cashfree_client_secret, true) ;
        $computedSignature = base64_encode($hash_hmac);
        if ($signature != $computedSignature) {
            // Reject this call
            $data['message'] = 'Invalid Payment Id';
            $data['code'] = HTTP_UNAUTHORIZED;
            $this->response($data, 200);
            exit();
        }

        $Amount = $CheckTicket[0]->price;

        if ($CheckTicket[0]->payment==0 && $orderAmount == $Amount) {
            $this->Coin_plan_model->UpdateOrderPayment($CheckTicket[0]->razor_payment_id);
            $this->Users_model->UpdateWalletOrder($CheckTicket[0]->coin, $CheckTicket[0]->user_id);


            for ($i=1; $i <= 3; $i++) {
                if ($user[0]->referred_by!=0) {
                    $level = 'level_'.$i;
                    $coins = (($CheckTicket[0]->coin*$setting->$level)/100);
                    $this->Users_model->UpdateWalletOrder($coins, $user[0]->referred_by);

                    $log_data = [
                        'user_id' => $user[0]->referred_by,
                        'purchase_id' => $order_id,
                        'purchase_user_id' => $user_id,
                        'coin' => $coins,
                        'level' => $i,
                    ];

                    $this->Users_model->AddPurchaseReferLog($log_data);
                    $user = $this->Users_model->UserProfile($user[0]->referred_by);
                } else {
                    break;
                }
            }

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'Invalid Payment';
            $data['code'] = HTTP_NOT_FOUND;
            $this->response($data, 200);
            exit();
        }
    }

    public function payumoney_token_api_Post()
    {
        $user_id = $this->input->post('user_id');

        // if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
        //     $data['message'] = 'Invalid User';
        //     $data['code'] = HTTP_INVALID;
        //     $this->response($data, HTTP_OK);
        //     exit();
        // }

        $plan_id = $this->input->post('plan_id');

        if (empty($user_id) || empty($plan_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user_data = $this->Users_model->UserProfile($user_id);
        if (empty($user_data)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $plan = $this->Coin_plan_model->View($plan_id);
        if (empty($plan)) {
            $data['message'] = 'Invalid Plan';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $Amount = $plan->price;             //Product Amount While the Time OF Order

        $Order_ID = $this->Coin_plan_model->GetCoin($user_id, $plan_id, $plan->coin, $Amount);

        if (empty($Order_ID)) {
            $data['message'] = 'Error while Creating Ticket';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        // create ORder in razor pay
        // $RazorPay_order = $this->RazorPay_order($Order_ID, $Amount);
        $txn_id = uniqid().$Order_ID;
        $paytm_body['orderId'] = $txn_id;
        $paytm_body['plan_id'] = $plan_id;
        $paytm_body['name'] = $user_data[0]->name;
        $paytm_body['email'] = ($user_data[0]->email) ? $user_data[0]->email : 'support@androappstech.com';
        $paytm_body['mobile'] = $user_data[0]->mobile;
        $paytm_body['amount'] = number_format($Amount, 1);

        // $payumoney_token = $this->payumoney_salt($paytm_body);
        $Update_Order_Master = $this->Coin_plan_model->UpdateOrder($user_id, $Order_ID, $txn_id);

        if ($Update_Order_Master) {
            $data['order_id'] = $txn_id;
            $data['Total_Amount'] = $Amount;
            // $data['payumoney_token'] = $payumoney_token['hash'];
            // $data['payumoney_string'] = $payumoney_token['string'];
            $data['payumoney_body'] = $paytm_body;
            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'Technical Error';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
    }

    public function payumoney_salt_post()
    {
        $setting = $this->Setting_model->Setting();
        $paytmParams = array();

        $hash_data = $this->input->post('hash_data');
        // $product_info = $data['plan_id'];
        // $customer_name = $data['name'];
        // $customer_email = $data['email'];
        // $customer_mobile = $data['mobile'];
        // $customer_address = $data['email'];

        // //payumoney details


        // $MERCHANT_KEY = $setting->payumoney_key; //change  merchant with yours
        $SALT = $setting->payumoney_salt;  //change salt with yours

        // $txnid = $data['orderId'];
        // // $txnid = uniqid().md5($data['orderId']);
        // //optional udf values
        // $udf1 = '';
        // $udf2 = '';
        // $udf3 = '';
        // $udf4 = '';
        // $udf5 = '';

        // $return['string'] = $hashstring = $MERCHANT_KEY . '|' . $txnid . '|' . $amount . '|' . $product_info . '|' . $customer_name . '|' . $customer_email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $SALT;
        // $return['string'] = $hashstring = $MERCHANT_KEY . '|payment_related_details_for_mobile_sdk|'.$customer_email.'|' . $SALT;
        // $return['hash'] = strtolower(hash('sha512', $hashstring));
        // return $return;

        if ($hash_data) {
            $data['payumoney_hash'] = strtolower(hash('sha512', ($hash_data . $SALT)));
            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'hash data empty';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
    }

    public function Place_Order_Post()
    {
        $user_id = $this->input->post('user_id');

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $plan_id = $this->input->post('plan_id');

        if (empty($user_id) || empty($plan_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (empty($this->Users_model->UserProfile($user_id))) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $plan = $this->Coin_plan_model->View($plan_id);
        if (empty($plan)) {
            $data['message'] = 'Invalid Plan';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $Amount = $plan->price;             //Product Amount While the Time OF Order

        $Order_ID = $this->Coin_plan_model->GetCoin($user_id, $plan_id, $plan->coin, $Amount);

        if (empty($Order_ID)) {
            $data['message'] = 'Error while Creating Ticket';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        // create ORder in razor pay
        $RazorPay_order = $this->RazorPay_order($Order_ID, $Amount);


        $Update_Order_Master = $this->Coin_plan_model->UpdateOrder($user_id, $Order_ID, $RazorPay_order->id);


        if ($Update_Order_Master) {
            $data['order_id'] = $Order_ID;
            $data['Total_Amount'] = $Amount;
            $data['RazorPay_ID'] = $RazorPay_order->id;
            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'Technical Error';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
    }

    public function Place_Order_upi_Post()
    {
        $user_id = $this->input->post('user_id');
        $extra = $this->input->post('extra');

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $plan_id = $this->input->post('plan_id');

        if (empty($user_id) || empty($plan_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (empty($this->Users_model->UserProfile($user_id))) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $plan = $this->Coin_plan_model->View($plan_id);
        if (empty($plan)) {
            $data['message'] = 'Invalid Plan';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $Amount = $plan->price;             //Product Amount While the Time OF Order

        $Order_ID = $this->Coin_plan_model->GetCoin($user_id, $plan_id, $plan->coin, $Amount, $extra);

        if (empty($Order_ID)) {
            $data['message'] = 'Error while Creating Ticket';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        // create ORder in razor pay
        // $RazorPay_order = $this->RazorPay_order($Order_ID, $Amount);


        // $Update_Order_Master = $this->Coin_plan_model->UpdateOrder($user_id, $Order_ID, $RazorPay_order->id);


        if ($Order_ID) {
            $data['order_id'] = $Order_ID;
            $data['Total_Amount'] = $Amount;
            // $data['RazorPay_ID'] = $RazorPay_order->id;
            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'Technical Error';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
    }

    public function RazorPay_order($Order_ID, $Amount)
    {
        $setting = $this->Setting_model->Setting();
        $api = new Api($setting->razor_api_key, $setting->razor_secret_key);
        $order = $api->order->create(
            array(
                'receipt' => $Order_ID,
                'amount' => ($Amount * 100),
                'payment_capture' => 0,
                'currency' => 'INR'
            )
        );
        return $order;
    }

    public function Pay_Now_post()
    {
        $user_id = $this->input->post('user_id');
        $order_id = $this->input->post('order_id');
        $Payment_ID = $this->input->post('payment_id');

        if (empty($user_id) || empty($order_id)  || empty($Payment_ID)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $CheckTicket = $this->Coin_plan_model->GetUserByOrderId($order_id);
        if (empty($CheckTicket)) {
            $data['message'] = 'Invalid Ticket ID';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $setting = $this->Setting_model->Setting();
        $api = new Api($setting->razor_api_key, $setting->razor_secret_key);
        try {
            $payment = $api->payment->fetch($Payment_ID);
        } catch (\Exception $e) {
            // print_r($e);
            $data['message'] = 'Invalid Payment Id';
            $data['code'] = HTTP_UNAUTHORIZED;
            $this->response($data, 200);
            exit();
        }

        if ($payment) {
            $R_Order_ID = $payment->order_id;

            if ($CheckTicket[0]->razor_payment_id != $R_Order_ID) {
                $data['message'] = 'Invalid Order Data';
                $data['code'] = HTTP_NOT_ACCEPTABLE;
                $this->response($data, 200);
                exit();
            }

            $Amount = $CheckTicket[0]->price;
            if ($payment->status = 'authorized' && $payment->amount >= $Amount) {
                $payment->capture(array('amount' => ($Amount * 100), 'currency' => 'INR'));
                $this->Coin_plan_model->UpdateOrderPayment($CheckTicket[0]->razor_payment_id, $payment);
                $this->Users_model->UpdateWalletOrder($CheckTicket[0]->coin, $CheckTicket[0]->user_id);


                for ($i=1; $i <= 3; $i++) {
                    if ($user[0]->referred_by!=0) {
                        $level = 'level_'.$i;
                        $coins = (($CheckTicket[0]->coin*$setting->$level)/100);
                        $this->Users_model->UpdateWalletOrder($coins, $user[0]->referred_by);

                        $log_data = [
                            'user_id' => $user[0]->referred_by,
                            'purchase_id' => $order_id,
                            'purchase_user_id' => $user_id,
                            'coin' => $coins,
                            'level' => $i,
                        ];

                        $this->Users_model->AddPurchaseReferLog($log_data);
                        $user = $this->Users_model->UserProfile($user[0]->referred_by);
                    } else {
                        break;
                    }
                }

                $data['message'] = 'Success';
                $data['code'] = HTTP_OK;
                $this->response($data, 200);
                exit();
            } else {
                $data['message'] = 'Invalid Payment';
                $data['code'] = HTTP_NOT_FOUND;
                $this->response($data, 200);
                exit();
            }
        }
    }
}