<?php

use Restserver\Libraries\REST_Controller;

include(APPPATH . '/libraries/REST_Controller.php');
include(APPPATH . '/libraries/Format.php');

class Atompay extends REST_Controller
{
    private $data;
    public function __construct()
    {
        parent::__construct();
        // $header = $this->input->request_headers('token');
        // if (!isset($header['Token'])) {
        //     $data['message'] = 'Invalid Request';
        //     $data['code'] = HTTP_UNAUTHORIZED;
        //     $this->response($data, HTTP_OK);
        //     exit();
        // }
        // if ($header['Token'] != getToken()) {
        //     $data['message'] = 'Invalid Authorization';
        //     $data['code'] = HTTP_METHOD_NOT_ALLOWED;
        //     $this->response($data, HTTP_OK);
        //     exit();
        // }
        // if (!isset($header['Content-Type']) && $header['Content-Type']!= 'application/json') {
        //     $data['message'] = 'Invalid Data';
        //     $data['code'] = HTTP_UNAUTHORIZED;
        //     $this->response($data, HTTP_OK);
        //     exit();
        // }
        // $this->data = json_decode(file_get_contents('php://input'));

        $this->load->model([
            'Users_model',
            'Coin_plan_model'
        ]);
    }

    public function index_get()
    {
        $user_id = $this->input->get('user_id');
        $token = $this->input->get('token');

        if (!$this->Users_model->TokenConfirm($user_id, $token)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $plan_id = $this->input->get('plan_id');

        if (empty($user_id) || empty($plan_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
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

        $transactionId = $Order_ID;

        $this->Coin_plan_model->UpdateOrder($user_id, $Order_ID, $transactionId);

        $this->load->library('Atompay/TransactionRequest');
        date_default_timezone_set('Asia/Kolkata');
        $datenow = date("d/m/Y h:m:s");
        $transactionDate = str_replace(" ", "%20", $datenow);

        // require_once 'TransactionRequest.php';


        $transactionRequest = new TransactionRequest();

        //Setting all values here
        $transactionRequest->setLogin('192');
        $transactionRequest->setPassword("Test@123");
        $transactionRequest->setProductId("NSE");
        $transactionRequest->setAmount($Amount);
        $transactionRequest->setTransactionCurrency("INR");
        $transactionRequest->setTransactionAmount($Amount);
        $transactionRequest->setReturnUrl(base_url('/api/atompay/response'));
        $transactionRequest->setClientCode('NAVIN');
        $transactionRequest->setTransactionId($transactionId);
        $transactionRequest->setTransactionDate($transactionDate);
        $transactionRequest->setCustomerName($user[0]->name);
        $transactionRequest->setCustomerEmailId("test@test.com");
        $transactionRequest->setCustomerMobile($user[0]->mobile);
        $transactionRequest->setCustomerBillingAddress("Mumbai");
        $transactionRequest->setCustomerAccount($user[0]->id);
        $transactionRequest->setReqHashKey("KEY123657234");
        $transactionRequest->seturl("https://paynetzuat.atomtech.in/paynetz/epi/fts");
        $transactionRequest->setRequestEncypritonKey("8E41C78439831010F81F61C344B7BFC7");
        $transactionRequest->setSalt("8E41C78439831010F81F61C344B7BFC7");


        $url = $transactionRequest->getPGUrl();
        header("Location: $url");
        // echo 'hi';
    }

    public function response_post()
    {
        $this->load->library('Atompay/TransactionResponse');
        $transactionResponse = new TransactionResponse();

        $transactionResponse->setRespHashKey("KEYRESP123657234");
        $transactionResponse->setResponseEncypritonKey("8E41C78439831010F81F61C344B7BFC7");
        $transactionResponse->setSalt("8E41C78439831010F81F61C344B7BFC7");

        $arrayofdata = $transactionResponse->decryptResponseIntoArray($_POST['encdata']);
        // print_r($arrayofdata);
        if ($arrayofdata['f_code']=='Ok') {
            // print_r($arrayofdata);
            $user_id = $arrayofdata['udf5'];
            $order_id = $arrayofdata['mer_txn'];
            $Payment_ID = $arrayofdata['mer_txn'];

            if (empty($user_id) || empty($order_id)  || empty($Payment_ID)) {
                $data['message'] = 'Invalid Params';
                $data['code'] = HTTP_BLANK;
                $this->response($data, 200);
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

            $Amount = $CheckTicket[0]->price;
            if ($arrayofdata['amt'] >= $Amount) {
                $this->Coin_plan_model->UpdateOrderPayment($CheckTicket[0]->razor_payment_id, json_encode($arrayofdata));
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

                echo '<html>
                <head>
                  <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
                </head>
                  <style>
                    body {
                      text-align: center;
                      padding: 40px 0;
                      background: #EBF0F5;
                    }
                      h1 {
                        color: #88B04B;
                        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
                        font-weight: 900;
                        font-size: 40px;
                        margin-bottom: 10px;
                      }
                      p {
                        color: #404F5E;
                        font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
                        font-size:20px;
                        margin: 0;
                      }
                    i {
                      color: #9ABC66;
                      font-size: 100px;
                      line-height: 200px;
                      margin-left:-15px;
                    }
                    .card {
                      background: white;
                      padding: 60px;
                      border-radius: 4px;
                      box-shadow: 0 2px 3px #C8D0D8;
                      display: inline-block;
                      margin: 0 auto;
                    }
                  </style>
                  <body>
                    <div class="card">
                    <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
                      <i class="checkmark">✓</i>
                    </div>
                      <h1>Success</h1> 
                      <p>We received your purchase request;</p>
                    </div>
                  </body>
              </html>';
                // $data['message'] = 'Success';
                // $data['code'] = HTTP_OK;
                // $this->response($data, 200);
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