<?php

use Restserver\Libraries\REST_Controller;

include APPPATH . '/libraries/REST_Controller.php';
include APPPATH . '/libraries/Format.php';
class Callback extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Users_model');
        $this->load->model('Coin_plan_model');
        $this->load->helper('string');
    }

    public function index_post()
    {
        $post_data_expected = file_get_contents("php://input");
        $data = [
            'response' => $post_data_expected
        ];
        $this->db->insert('response_log', $data);

        $post = json_decode($post_data_expected);

        //param1 is mandatory
        if (empty($post->param1)) {
            $data['message'] = 'Invalid Order Id';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //checks param1 in local
        $order_details = $this->Coin_plan_model->GetUserByOrderId($post->param1);

        if (empty($order_details)) {
            $data['message'] = 'Invalid Order Id';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //cross check user id with your local order
        if ($post->user_id!=$order_details[0]->user_id) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //cross check amount with your local order
        if ($post->amount!=$order_details[0]->price) {
            $data['message'] = 'Invalid Amount';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //cross check if your local payment already done
        if ($post->status==1 && $order_details[0]->payment==0) {
            //update local payment status
            $this->Coin_plan_model->UpdateOrderPaymentStatus($post->param1);
            $this->Users_model->UpdateWalletOrder($order_details[0]->coin, $post->user_id);
            $this->Users_model->UpdateSpin($post->user_id, ceil($post->amount/100));

            if ($order_details[0]->extra>0) {
                $this->Users_model->UpdateWalletOrder(($order_details[0]->coin*($order_details[0]->extra/100)), $post->user_id);
            }
        }
    }

    public function verify_post()
    {
        $post_data_expected = json_encode($_POST);
        $data = [
            'response' => $post_data_expected
        ];
        $this->db->insert('response_log', $data);

        $post = json_decode($post_data_expected);

        //param1 is mandatory
        if (empty($post->param1)) {
            $data['message'] = 'Invalid Order Id';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //checks param1 in local
        $order_details = $this->Coin_plan_model->GetUserByOrderId($post->param1);

        if (empty($order_details)) {
            $data['message'] = 'Invalid Order Id';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //cross check user id with your local order
        if ($post->user_id!=$order_details[0]->user_id) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //cross check amount with your local order
        if ($post->amount!=$order_details[0]->price) {
            $data['message'] = 'Invalid Amount';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        //cross check if your local payment already done
        if ($post->status==1 && $order_details[0]->payment==0) {
            //update local payment status
            $total_amount=$this->Coin_plan_model->GetTotalAmountByUser($post->user_id);
            $category=$this->Coin_plan_model->GetUserCategoryByAmount($total_amount+$order_details[0]->coin);

            $category_id = (empty($category)) ? 0 : $category->id;
            $category_amount = (empty($category)) ? 0 : $order_details[0]->coin*($category->percentage/100);

            $this->Coin_plan_model->UpdateOrderPaymentStatus($post->param1);
            $this->Users_model->UpdateWalletOrder($order_details[0]->coin+$category_amount, $post->user_id);

            $this->Users_model->UpdateSpin($post->user_id, ceil($post->amount/100), $category_id);

            if ($order_details[0]->extra>0) {
                $extra_amount = $order_details[0]->coin*($order_details[0]->extra/100);
                $this->Users_model->UpdateWalletOrder($extra_amount, $post->user_id);
                $this->Users_model->ExtraWalletLog($post->user_id, $extra_amount, 0);
            }

            if ($category_amount>0) {
                $this->Users_model->ExtraWalletLog($post->user_id, $category_amount, 1);
            }

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        }
    }

    public function spin_post()
    {
        $post_data_expected = json_encode($_POST);
        $log_data = [
            'response' => $post_data_expected
        ];
        $this->db->insert('response_log', $log_data);

        $post = json_decode($post_data_expected);

        //param1 is mandatory
        $user = $this->Users_model->UserProfile($post->user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        //cross check if your local payment already done
        if ($user[0]->spin_remaining>0) {
            //update local payment status
            $coin = $post->amount;
            if ($coin>3) {
                $data['message'] = 'Invalid Spin';
                $data['code'] = HTTP_NOT_ACCEPTABLE;
                $this->response($data, 200);
                exit();
            }
            $this->Users_model->UpdateWalletSpin($post->user_id, $coin);
            $this->Users_model->ExtraWalletLog($post->user_id, $coin, 0);

            $data['message'] = 'Success';
            $data['coin'] = $coin;
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = 'No Spin Found';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
    }
}
