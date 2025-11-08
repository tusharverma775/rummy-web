<?php

use Restserver\Libraries\REST_Controller;
use Razorpay\Api\Api;

include APPPATH . '/libraries/REST_Controller.php';
include APPPATH . '/libraries/Format.php';

class Ticket extends REST_Controller
{
    private $data;
    private $UserData;
    private $UserId;
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
        // print_r($this->data['user_id']);
        $this->load->model([
            'Ticket_model',
            'Game_model',
            'Users_model'
        ]);
    }

    public function get_post()
    {
        $user_id = $this->input->post('user_id');
        $game_id = $this->input->post('game_id');
        $no_of_tickets = $this->input->post('no_of_tickets');
        $no_of_tickets = (empty($no_of_tickets) ? 1 : $no_of_tickets);
        if (empty($user_id) || empty($game_id)) {
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

        $Game = $this->Game_model->View($game_id);

        if (!$Game) {
            $data['message'] = 'Invalid Game';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        // print_r($Game);

        if ($Game) {
            $Game_status = $Game->status;

            if ($Game_status==0) {
                $Amount = $Game->ticket_price*$no_of_tickets;
                if ($Amount<=$user[0]->wallet) {
                    for ($i=0; $i < $no_of_tickets; $i++) {
                        $TicketData = $this->Ticket_model->GetTicketWallet($this->data['user_id'], $this->data['game_id'], $Game->ticket_price);
                    }

                    $data['message'] = 'Success';
                    $data['TicketData'] = $TicketData;
                    $data['code'] = HTTP_OK;
                    $this->response($data, HTTP_OK);
                    exit();
                } else {
                    $data['message'] = 'Wallet Amount is less than Ticket Price';
                    $data['code'] = HTTP_OK;
                    $this->response($data, HTTP_OK);
                    exit();
                }
            } elseif ($Game_status==1) {
                $data['message'] = 'Game Started';
                $data['code'] = HTTP_OK;
                $this->response($data, HTTP_OK);
                exit();
            } else {
                $data['message'] = 'Game Ended';
                $data['code'] = HTTP_OK;
                $this->response($data, HTTP_OK);
                exit();
            }
        } else {
            $data['message'] = 'Game Not Found';
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        }
    }

    public function Place_Order_Post()
    {
        $user_id = $this->input->post('user_id');
        $game_id = $this->input->post('game_id');
        $no_of_tickets = $this->input->post('no_of_tickets');
        $no_of_tickets = (empty($no_of_tickets) ? 1 : $no_of_tickets);
        if (empty($user_id) || empty($game_id)) {
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

        $game = $this->Game_model->View($game_id);
        if (empty($game)) {
            $data['message'] = 'Invalid Game';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $Amount = $game->ticket_price*$no_of_tickets;             //Product Amount While the Time OF Order

        $Ticket = '';
        $Ticketids = array();
        for ($i=0; $i < $no_of_tickets; $i++) {
            $Ticket = $this->Ticket_model->GetTicket($user_id, $game_id);
            $Ticketids[] = $Ticket['ticket_id'];
        }

        if (empty($Ticket)) {
            $data['message'] = 'Error while Creating Ticket';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }
        // create ORder in razor pay
        $RazorPay_order = $this->RazorPay_order($Ticket['ticket_id'], $Amount);

        foreach ($Ticketids as $value) {
            $Update_Order_Master = $this->Ticket_model->Update_Ticket($user_id, $value, $game->ticket_price, $RazorPay_order->id);
        }

        if ($Update_Order_Master) {
            $data['ticket_id'] = $Ticket['ticket_id'];
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

    public function RazorPay_order($Order_ID, $Amount)
    {
        $api = new Api(API_KEY, API_SECRET);
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
        $ticket_id = $this->input->post('ticket_id');
        $Payment_ID = $this->input->post('payment_id');

        if (empty($user_id) || empty($ticket_id)  || empty($Payment_ID)) {
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

        $CheckTicket = $this->Ticket_model->GetUserByTicketId($ticket_id);
        if (empty($CheckTicket)) {
            $data['message'] = 'Invalid Ticket ID';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $api = new Api(API_KEY, API_SECRET);
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

            //Fetch Order Data Using RazorPay ID
            // $OrderData = Check($R_Order_ID, 'razorpay_order_id', 'tbl_order_master');
            // print_r($CheckTicket);
            if ($CheckTicket[0]->razorpay_order_id != $R_Order_ID) {
                $data['message'] = 'Invalid Order Data';
                $data['code'] = HTTP_NOT_ACCEPTABLE;
                $this->response($data, 200);
                exit();
            }

            $no_of_tickets = $this->Ticket_model->No_Of_Tickets($CheckTicket[0]->razorpay_order_id);
            $Amount = $CheckTicket[0]->amount*$no_of_tickets;
            if ($payment->status = 'authorized' && $payment->amount >= $Amount) {
                //     //Update Payment
                $payment->capture(array('amount' => ($Amount * 100), 'currency' => 'INR'));
                $Update_payment = $this->Ticket_model->Update_Ticket_Payment($CheckTicket[0]->razorpay_order_id, $payment);


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

    public function get_selected_post()
    {
        $TicketData = $this->Ticket_model->GetSelectedTicketNumber($this->data['ticket_id']);
        $GameData = $this->Ticket_model->GetSelectedGameNumber($this->data['game_id']);
        $data['message'] = 'Success';
        $data['TicketData'] = $TicketData;
        $data['GameData'] = $GameData;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function get_game_selected_post()
    {
        $GameData = $this->Ticket_model->GetSelectedGameNumber($this->data['game_id']);
        $data['message'] = 'Success';
        $data['GameData'] = $GameData;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function list_post()
    {
        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Game_model->View($this->data['game_id']);
        if (empty($game)) {
            $data['message'] = 'Invalid Game';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $Ticket = $this->Ticket_model->GetUserTicketByGameId($this->data['user_id'], $this->data['game_id']);
        $GameData = $this->Ticket_model->GetSelectedGameNumber($this->data['game_id']);

        $WinnerData[0]['first_five_winner'] = 0;
        $WinnerData[0]['first_row_winner'] = 0;
        $WinnerData[0]['second_row_winner'] = 0;
        $WinnerData[0]['third_row_winner'] = 0;
        $WinnerData[0]['whole_winner'] = 0;

        if ($game->first_five_winner!=0) {
            $WinnerData[0]['first_five_winner'] = 1;
        }

        if ($game->first_row_winner!=0) {
            $WinnerData[0]['first_row_winner'] = 1;
        }

        if ($game->second_row_winner!=0) {
            $WinnerData[0]['second_row_winner'] = 1;
        }

        if ($game->third_row_winner!=0) {
            $WinnerData[0]['third_row_winner'] = 1;
        }

        if ($game->whole_winner!=0) {
            $WinnerData[0]['whole_winner'] = 1;
        }

        $data = [
            'List' => $Ticket,
            'GameData' => $GameData,
            'Wallet' => $user[0]->wallet,
            'Status' => $game->status,
            'WinnerData' => $WinnerData,
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function draw_post()
    {
        $number = $this->Ticket_model->GenerateNumber($this->data['game_id']);
        if ($number) {
            $msg = 'Success';
            $code = HTTP_OK;
            switch ($number) {
                case 101:
                    $number = 0;
                    $msg = 'Game Not Found';
                    $code = HTTP_NOT_FOUND;
                    break;

                case 102:
                    $number = 0;
                    $msg = 'Game Not Started';
                    $code = HTTP_NOT_FOUND;
                    break;

                case 103:
                    $number = 0;
                    $msg = 'Game Ended';
                    $code = HTTP_NOT_FOUND;
                    break;
            }
            $data = [
                'Number' => $number,
                'message' => $msg,
                'code' => $code,
            ];
            $this->response($data, HTTP_OK);
        } else {
            $data = [
                'message' => 'Please try after sometime',
                'code' => HTTP_NOT_FOUND,
            ];
            $this->response($data, HTTP_OK);
        }
    }
}