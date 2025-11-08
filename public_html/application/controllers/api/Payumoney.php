<?php
use Restserver\Libraries\REST_Controller;

include APPPATH . '/libraries/REST_Controller.php';
include APPPATH . '/libraries/Format.php';
class Payumoney extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Users_model');
        $this->load->model('Coin_plan_model');
        $this->load->model('Setting_model');
    }

    public function call_back_post()
    {
        $txnid = $this->input->post('txnid');
        $status = $this->input->post('status');
        $amount = $this->input->post('amount');

        if (empty($status) || empty($txnid)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $CheckTicket = $this->Coin_plan_model->GetUserByOrderTxnId($txnid);
        // print_r($CheckTicket);
        if (empty($CheckTicket)) {
            $data['message'] = 'Invalid Ticket ID';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($CheckTicket[0]->user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($status == "success") {
            $setting = $this->Setting_model->Setting();
            // echo $CheckTicket[0]->price;
            if ($CheckTicket[0]->price == $amount) {
                $this->Coin_plan_model->UpdateOrderPaymentStatus($CheckTicket[0]->id);
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
                      <p>We received your purchase Successfully;<br/> Chips Added In Wallet!</p>
                    </div>
                  </body>
              </html>';
            // $data['message'] = 'Success';
                // $data['code'] = HTTP_OK;
                // $this->response($data, 200);
                // exit();
            } else {
                $data['message'] = 'Invalid Payment';
                $data['code'] = HTTP_NOT_FOUND;
                $this->response($data, 200);
                exit();
            }
        } else {
            $data['message'] = 'Invalid Payment';
            $data['code'] = HTTP_NOT_FOUND;
            $this->response($data, 200);
            exit();
        }
    }
}