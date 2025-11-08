<?php

use phpDocumentor\Reflection\Types\Object_;
use Restserver\Libraries\REST_Controller;

include APPPATH . '/libraries/REST_Controller.php';
include APPPATH . '/libraries/Format.php';
class Rummy extends REST_Controller
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
        // print_r($this->data['user_id']);
        $this->load->model([
            'Rummy_model',
            'Setting_model',
            'Users_model'
        ]);
    }

    // public function sendNotification($TableId)
    // {
    //     $userdata = $this->Users_model->FreeUserList();

    //     foreach ($userdata as $value) {
    //         if (!empty($value->fcm)) {
    //             $data['msg'] = "New User Joined Table";
    //             $data['title'] = "Teen Patti";
    //             $data['table_id'] = $TableId;
    //             push_notification_android($value->fcm, $data);
    //         }
    //     }
    // }

    public function get_table_post()
    {
        if (empty($this->data['user_id']) || empty($this->data['boot_value'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        // if(!in_array($this->data['no_of_players'],array(2,5)))
        // {
        //     $data['message'] = 'Invalid No. Of Players';
        //     $data['code'] = HTTP_NOT_ACCEPTABLE;
        //     $this->response($data, 200);
        //     exit();
        // }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $isMaster = $this->Rummy_model->getTableMaster($this->data['boot_value']);
        if (empty($isMaster)) {
            $data['message'] = 'Invalid Boot Value';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        // $joining_amount = $this->Setting_model->Setting()->joining_amount;
        if ($user[0]->wallet<$isMaster[0]->boot_value) {
            $data['message'] = 'Required Minimum '.number_format($isMaster[0]->boot_value).' Coins to Play';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($user[0]->rummy_table_id) {
            $table_data = $this->Rummy_model->TableUser($user[0]->rummy_table_id);
            $data['message'] = 'You are Already On Table';
            $data['table_data'] = $table_data;
            $data['code'] = HTTP_OK;
            $this->response($data, 200);
            exit();
        }

        $table_amount = $isMaster[0]->boot_value;

        $tables = $this->Rummy_model->getCustomizeActiveTable($table_amount);
        $seat_position = 1;

        if ($tables) {
            foreach ($tables as $value) {
                if ($value->members<$this->data['no_of_players']) {
                    $TableId = $value->rummy_table_id;
                    $seat_position = $this->Rummy_model->GetSeatOnTable($TableId);
                }
            }
        }

        if (empty($TableId)) {
            $table_data = [
                'boot_value' => $table_amount,
                'added_date' => date('Y-m-d H:i:s'),
                'updated_date' => date('Y-m-d H:i:s')
            ];
            $TableId = $this->Rummy_model->CreateTable($table_data);

            $admin_mobile = $this->Setting_model->Setting()->mobile;
            if (!empty($admin_mobile)) {
                $admin_user = $this->Users_model->UserByMobile($admin_mobile);
                if ($admin_user) {
                    if (!empty($admin_user->fcm)) {
                        $fcm_data['msg'] = PROJECT_NAME;
                        $fcm_data['title'] = "New User On Rummy Table Boot Value ".$table_amount;
                        $return = push_notification_android($admin_user->fcm, $fcm_data);
                        // print_r($return);
                    }
                }
            }
            $robot_rummy = $this->Setting_model->Setting()->robot_rummy;
            if ($robot_rummy==0) {
                $bot = $this->Users_model->GetFreeRummyBot();

                if ($bot) {
                    $table_bot_data = [
                        'table_id' => $TableId,
                        'user_id' => $bot[0]->id,
                        'seat_position' => 2,
                        'added_date' => date('Y-m-d H:i:s'),
                        'updated_date' => date('Y-m-d H:i:s')
                    ];

                    $this->Rummy_model->AddTableUser($table_bot_data);
                }
            }
        }

        $table_user_data = [
            'table_id' => $TableId,
            'user_id' => $user[0]->id,
            'seat_position' => $seat_position,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $this->Rummy_model->AddTableUser($table_user_data);

        $table_data = $this->Rummy_model->TableUser($TableId);

        $data['message'] = 'Success';
        $data['table_data'] = $table_data;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function get_private_table_post()
    {
        if (empty($this->data['user_id']) || empty($this->data['boot_value'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        // if ($user[0]->wallet<10000) {
        //     $data['message'] = 'Required Minimum 10,000 Coins to Play';
        //     $data['code'] = HTTP_NOT_ACCEPTABLE;
        //     $this->response($data, 200);
        //     exit();
        // }

        if ($user[0]->table_id) {
            $data['message'] = 'You are Already On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $table_data = [
            'boot_value' => $this->data['boot_value'],
            'maximum_blind' => 4,
            'chaal_limit' => $this->data['boot_value']*128,
            'pot_limit' => $this->data['boot_value']*1024,
            'private' => 2,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $TableId = $this->Rummy_model->CreateTable($table_data);

        $table_user_data = [
            'table_id' => $TableId,
            'user_id' => $user[0]->id,
            'seat_position' => 1,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $this->Rummy_model->AddTableUser($table_user_data);

        $table_data = $this->Rummy_model->TableUser($TableId);

        $data['message'] = 'Success';
        $data['table_id'] = $TableId;
        $data['table_data'] = $table_data;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function get_table_master_post()
    {
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($user[0]->rummy_table_id) {
            $table_data = $this->Rummy_model->TableUser($user[0]->rummy_table_id);
            $data['message'] = 'You are Already On Table';
            $data['table_data'] = $table_data;
            $data['code'] = 205;
            $this->response($data, 200);
            exit();
        }

        $table_data = $this->Rummy_model->getTableMaster();

        $data['message'] = 'Success';
        $data['table_data'] = $table_data;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function join_table_post()
    {
        if (empty($this->data['user_id']) || empty($this->data['table_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($user[0]->rummy_table_id) {
            $data['message'] = 'You are Already On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Rummy_model->isTable($this->data['table_id'])) {
            $data['message'] = 'Invalid Table Id';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $table = $this->Rummy_model->isTableAvail($this->data['table_id']);
        if (!$table) {
            $data['message'] = 'Invalid Table Id';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($user[0]->wallet<$table->boot_value) {
            $data['message'] = 'Required Minimum '.$table->boot_value.' Coins to Play';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $table_user_data = [
            'table_id' => $this->data['table_id'],
            'user_id' => $user[0]->id,
            'seat_position' => $this->Rummy_model->GetSeatOnTable($this->data['table_id']),
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $this->Rummy_model->AddTableUser($table_user_data);

        $table_data = $this->Rummy_model->TableUser($this->data['table_id']);

        $data['message'] = 'Success';
        $data['table_data'] = $table_data;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function start_game_post()
    {
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $table_data = $this->Rummy_model->TableUser($user[0]->rummy_table_id);

        if (count($table_data)<2) {
            $data['message'] = 'Unable to Create Game, Only One User On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }


        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if ($game) {
            $data['message'] = 'Active Game is Going On';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $table = $this->Rummy_model->isTableAvail($user[0]->rummy_table_id);
        $boot_value = $table->boot_value;

        if (count($table_data)>2) {
            foreach ($table_data as $key => $value) {
                if ($value->wallet<$boot_value) {
                    $table_user_data = [
                        'table_id' => $value->rummy_table_id,
                        'user_id' => $value->user_id
                    ];

                    $this->Rummy_model->RemoveTableUser($table_user_data);
                    $table_data = $this->Rummy_model->TableUser($user[0]->rummy_table_id);
                }
            }
        }

        if (count($table_data)>2) {
            foreach ($table_data as $key => $value) {
                if ($value->user_type==1) {
                    $table_user_data = [
                        'table_id' => $value->table_id,
                        'user_id' => $value->user_id
                    ];

                    $this->Rummy_model->RemoveTableUser($table_user_data);
                    $table_data = $this->Rummy_model->TableUser($user[0]->rummy_table_id);
                }
            }
        }

        $amount = 0;
        $Cards = $this->Rummy_model->GetStartCards((count($table_data)*13)+1);
        $game_data = [
            'table_id' => $user[0]->rummy_table_id,
            'joker' => $Cards[0]->cards,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $GameId = $this->Rummy_model->Create($game_data);

        $table_user_data = [
            'game_id' => $GameId,
            'user_id' => 0,
            'card' => $Cards[1]->cards
        ];

        $this->Rummy_model->StartDropGameCards($table_user_data);

        $end = 2;

        foreach ($table_data as $key => $value) {
            $start = $end;
            $end = $end+13;
            for ($i=$start; $i < $end; $i++) {
                $table_user_data = [
                    'game_id' => $GameId,
                    'user_id' => $value->user_id,
                    'card' => $Cards[$i]->cards,
                    'added_date' => date('Y-m-d H:i:s'),
                    'updated_date' => date('Y-m-d H:i:s'),
                    'isDeleted' => 0
                ];

                $this->Rummy_model->GiveGameCards($table_user_data);
            }

            $this->Rummy_model->AddGameCount($value->user_id);

            $game_log = [
                'game_id' => $GameId,
                'user_id' => $value->user_id,
                'action' => 0,
                'amount' => $amount,
                'added_date' => date('Y-m-d H:i:s')
            ];

            $this->Rummy_model->AddGameLog($game_log);
        }

        $data['message'] = 'Success';
        $data['game_id'] = $GameId;
        $data['table_amount'] = $amount;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function leave_table_post()
    {
        $timeout = '';
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        // $table_data = $this->Rummy_model->TableUser($user[0]->rummy_table_id);

        // foreach ($table_data as $value) {
        // if($value->mobile)
        $table_user_data = [
                'table_id' => $user[0]->rummy_table_id,
                'user_id' => $user[0]->id
            ];

        $this->Rummy_model->RemoveTableUser($table_user_data);
        // }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if ($game) {
            $table = $this->Rummy_model->isTableAvail($user[0]->rummy_table_id);
            $boot_value = $table->boot_value;
            $ChaalCount = $this->Rummy_model->ChaalCount($game->id, $this->data['user_id']);

            $percent = $ChaalCount>0 ? CHAAL_PERCENT : NO_CHAAL_PERCENT;
            $amount = round(($percent / 100) * $boot_value, 2);

            $this->Rummy_model->PackGame($this->data['user_id'], $game->id, $timeout, '', $amount, $percent);
            $this->Rummy_model->MinusWallet($this->data['user_id'], $amount);
            $game_users = $this->Rummy_model->GameUser($game->id);

            if (count($game_users)==1) {
                $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);
                $comission = $this->Setting_model->Setting()->admin_commission;
                $this->Rummy_model->MakeWinner($game->id, $game->amount, $game_users[0]->user_id, $comission);
            }
        }

        $table_users = $this->Rummy_model->TableUser($user[0]->rummy_table_id);

        if (count($table_users)==1) {
            if ($table_users[0]->mobile=="") {
                $table_user_data = [
                    'table_id' => $table_users[0]->table_id,
                    'user_id' => $table_users[0]->user_id
                ];

                $this->Rummy_model->RemoveTableUser($table_user_data);
            }
        }

        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function pack_game_post()
    {
        $timeout = $this->input->post('timeout');
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        // $game_log = $this->Rummy_model->GameLog($game->id,1);

        // $game_users = $this->Rummy_model->GameAllUser($game->id);

        // $chaal = 0;
        // $element = 0;
        // foreach ($game_users as $key => $value) {
        //     if($value->user_id==$game_log[0]->user_id)
        //     {
        //         $element = $key;
        //         break;
        //     }
        // }

        // $index = 0;
        // foreach ($game_users as $key => $value) {

        //     $index = ($key+$element)%count($game_users);
        //     if($key>0)
        //     {
        //         if(!$game_users[$index]->packed)
        //         {
        //             $chaal = $game_users[$index]->user_id;
        //             break;
        //         }
        //     }
        // }

        // echo $chaal;
        // if($chaal==$this->data['user_id'])
        // {
        $table = $this->Rummy_model->isTableAvail($user[0]->rummy_table_id);
        $boot_value = $table->boot_value;
        $ChaalCount = $this->Rummy_model->ChaalCount($game->id, $this->data['user_id']);

        $percent = ($ChaalCount>0) ? CHAAL_PERCENT : NO_CHAAL_PERCENT;
        $amount = round(($percent / 100) * $boot_value, 2);

        // $actual_points = $points*round($table->boot_value/80,2);
        $this->Rummy_model->PackGame($this->data['user_id'], $game->id, $timeout, $this->input->post('json'), $amount, $percent);
        $this->Rummy_model->MinusWallet($this->data['user_id'], $amount);

        $game_users = $this->Rummy_model->GameUser($game->id);
        if (count($game_users)==1) {
            $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);
            $comission = $this->Setting_model->Setting()->admin_commission;
            $this->Rummy_model->MakeWinner($game->id, $game->amount, $game_users[0]->user_id, $comission);
        }

        if ($timeout==1) {
            $table_user_data = [
                    'table_id' => $user[0]->rummy_table_id,
                    'user_id' => $user[0]->id
                ];

            $this->Rummy_model->RemoveTableUser($table_user_data);
        }

        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
        // }

        $data['message'] = 'Invalid Pack';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function get_card_post()
    {
        $user_id = $this->data['user_id'];
        if (empty($user_id)) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($user_id, $this->data['token'])) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $cards = $this->Rummy_model->getMyCards($game->id, $user_id);

        if (count($cards)>13) {
            $data['message'] = 'Please Drop Card And Then Pick One';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $random_card = $this->Rummy_model->GetRamdomGameCard($game->id);

        if ($random_card) {
            $table_user_data = [
                'game_id' => $game->id,
                'user_id' => $user_id,
                'card' => $random_card[0]->cards,
                'added_date' => date('Y-m-d H:i:s'),
                'updated_date' => date('Y-m-d H:i:s'),
                'isDeleted' => 0
            ];

            $this->Rummy_model->GiveGameCards($table_user_data);

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $data['card'] = $random_card;
            $this->response($data, HTTP_OK);
            exit();
        }

        $data['message'] = 'Invalid Chaal';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function drop_card_post()
    {
        $user_id = $this->data['user_id'];
        $card = $this->data['card'];

        if (empty($user_id)) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($user_id, $this->data['token'])) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $cards = $this->Rummy_model->getMyCards($game->id, $user_id);

        if (count($cards)<14) {
            $data['message'] = 'Please Get Or Pick Card First And Then Drop One';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $isCardAvailable = $this->Rummy_model->getMyCards($game->id, $user_id, $card);
        // print_r($isCardAvailable);
        if ($isCardAvailable) {
            $table_user_data = [
                'game_id' => $game->id,
                'user_id' => $user_id,
                'card' => $card
            ];

            $this->Rummy_model->DropGameCards($table_user_data, $this->input->post('json'));

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        }

        $data['message'] = 'Invalid Card';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function get_drop_card_post()
    {
        $user_id = $this->data['user_id'];
        if (empty($user_id)) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($user_id, $this->data['token'])) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $cards = $this->Rummy_model->getMyCards($game->id, $user_id);
        // echo count($cards);
        if (count($cards)>13) {
            $data['message'] = 'Please Drop Card And Then Pick One';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $drop_card = $this->Rummy_model->GetAndDeleteGameDropCard($game->id);

        if ($drop_card) {
            $table_user_data = [
                'game_id' => $game->id,
                'user_id' => $user_id,
                'card' => $drop_card[0]->card,
                'is_drop_card' => 1,
                'added_date' => date('Y-m-d H:i:s'),
                'updated_date' => date('Y-m-d H:i:s'),
                'isDeleted' => 0
            ];

            $this->Rummy_model->GiveGameCards($table_user_data);

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $data['card'] = $drop_card;
            $this->response($data, HTTP_OK);
            exit();
        }

        $data['message'] = 'Invalid Chaal';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function declare_post()
    {
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        if (!$user->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user->rummy_table_id);
        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $arr = json_decode($this->data['json']);
        $points = 0;

        foreach ($arr as $key => $value) {
            if ($value->card_group==0) {
                $points = $points+$this->card_points($value->cards, $game->joker);
            }
        }

        $points = ($points>80) ? 80 : $points;

        $data_declare = [
            'user_id' => $this->data['user_id'],
            'game_id' => $game->id,
            'points' => $points,
            'actual_points' => 0,
            'json' => $this->data['json']
        ];
        $this->Rummy_model->Declare($data_declare);

        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    // public function declare_back_post()
    // {
    //     $user_id = $this->data['user_id'];
    //     if (empty($user_id) || empty($this->data['json'])) {
    //         $data['message'] = 'Invalid Parameter';
    //         $data['code'] = HTTP_NOT_ACCEPTABLE;
    //         $this->response($data, 200);
    //         exit();
    //     }

    //     if(!$this->Users_model->TokenConfirm($user_id,$this->data['token']))
    //     {
    //         $data['message'] = 'Invalid User';
    //         $data['code'] = HTTP_INVALID;
    //         $this->response($data, HTTP_OK);
    //         exit();
    //     }

    //     $user = $this->Users_model->UserProfile($this->data['user_id']);
    //     if (empty($user)) {
    //         $data['message'] = 'Invalid User';
    //         $data['code'] = HTTP_NOT_ACCEPTABLE;
    //         $this->response($data, 200);
    //         exit();
    //     }

    //     if(!$user[0]->rummy_table_id)
    //     {
    //         $data['message'] = 'You Are Not On Table';
    //         $data['code'] = HTTP_NOT_ACCEPTABLE;
    //         $this->response($data, 200);
    //         exit();
    //     }

    //     $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

    //     if(!$game)
    //     {
    //         $data['message'] = 'Game Not Started';
    //         $data['code'] = HTTP_NOT_ACCEPTABLE;
    //         $this->response($data, 200);
    //         exit();
    //     }

    //     $amount = 0;

    //     $game_log = $this->Rummy_model->GameLog($game->id,1);

    //     // $game_users = $this->Rummy_model->GameAllUser($game->id);

    //     $remain_game_users = $this->Rummy_model->GameUser($game->id);
    //     // print_r($remain_game_users);

    //     if($game_log[0]->action!=3)
    //     {
    //         $data['message'] = 'Invalid Declare Back';
    //         $data['code'] = HTTP_NOT_ACCEPTABLE;
    //         $this->response($data, 200);
    //         exit();
    //     }

    //     if(count($remain_game_users)!=2)
    //     {
    //         $data['message'] = 'Show can be done between 2 users only';
    //         $data['code'] = HTTP_NOT_ACCEPTABLE;
    //         $this->response($data, 200);
    //         exit();
    //     }

    //     // $chaal = 0;
    //     // $element = 0;
    //     // foreach ($game_users as $key => $value) {
    //     //     if($value->user_id==$game_log[0]->user_id)
    //     //     {
    //     //         $element = $key;
    //     //         break;
    //     //     }
    //     // }

    //     // $index = 0;
    //     // foreach ($game_users as $key => $value) {

    //     //     $index = ($key+$element)%count($game_users);
    //     //     if($key>0)
    //     //     {
    //     //         if(!$game_users[$index]->packed)
    //     //         {
    //     //             $chaal = $game_users[$index]->user_id;
    //     //             break;
    //     //         }
    //     //     }
    //     // }

    //     // $game_users = $this->Rummy_model->GameUser($game->id);

    //     // if($chaal==$this->data['user_id'])
    //     // {
    //         // $arr = json_decode($game_log[0]->json);
    //         // $count = 0;
    //         // $wrong = 0;
    //         // foreach ($arr as $key => $value) {
    //         //     $count = $count+count($value->cards);
    //         //     if($value->card_group==0)
    //         //     {
    //         //         $wrong = 1;
    //         //         break;
    //         //     }
    //         // }

    //         // if($remain_game_users[0]->user_id!=$game_log[0]->user_id)
    //         // {
    //         //     $other_user_id = $remain_game_users[0]->user_id;
    //         // }
    //         // else
    //         // {
    //         //     $other_user_id = $remain_game_users[1]->user_id;
    //         // }

    //         $arr_card = json_decode($this->data['json']);
    //         $points = 0;
    //         foreach ($arr_card as $key => $value) {
    //             if($value->card_group==0)
    //             {
    //                 $points = $points+$this->card_points($value->cards);
    //             }
    //         }

    //         // echo $other_user_id;
    //         // $user_id = (!$wrong)?$game_log[0]->user_id:$other_user_id;
    //         // $lose_user_id = ($wrong)?$game_log[0]->user_id:$other_user_id;
    //         // exit();
    //         $comission = $this->Setting_model->Setting()->admin_commission;
    //         // $amount = $game->amount;
    //         $this->Rummy_model->MinusWallet($user_id,$points);
    //         $this->Rummy_model->MakeWinner($game->id,$points,$user_id,$comission);



    //         $data_log = [
    //             'user_id' => $this->data['user_id'],
    //             'game_id' => $game->id,
    //             'points' => $points,
    //             'json' => $this->data['json']
    //         ];
    //         $this->Rummy_model->Declare($data_log);
    //         $data['message'] = 'Success';
    //         $data['winner'] = $user_id;
    //         $data['code'] = HTTP_OK;
    //         $this->response($data, HTTP_OK);
    //         exit();
    //     // }

    //     $data['message'] = 'Invalid Show';
    //     $data['code'] = HTTP_NOT_ACCEPTABLE;
    //     $this->response($data, 200);
    //     exit();
    // }

    public function declare_back_post()
    {
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game_log = $this->Rummy_model->GameLog($game->id, 1);

        $remain_game_users = $this->Rummy_model->GameUser($game->id);
        // print_r($remain_game_users);

        if ($game_log[0]->action!=3) {
            $data['message'] = 'Invalid Declare Back';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $arr = json_decode($this->data['json']);
        $points = 0;
        foreach ($arr as $key => $value) {
            if ($value->card_group==0) {
                $points = $points+$this->card_points($value->cards, $game->joker);
            }
        }

        $points = ($points>80) ? 80 : $points;

        $table = $this->Rummy_model->isTableAvail($user[0]->rummy_table_id);
        $actual_points = $points*round($table->boot_value/80, 2);
        $data_log = [
            'user_id' => $this->data['user_id'],
            'game_id' => $game->id,
            'table_id' => $user[0]->rummy_table_id,
            'points' => $points,
            'actual_points' => $actual_points,
            'json' => $this->data['json']
        ];
        $this->Rummy_model->Declare($data_log);

        $declare_log = $this->Rummy_model->GameLog($game->id, '', 3);
        $declare_count = count($declare_log);
        if (count($remain_game_users)<=$declare_count) {
            $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);
            // $table = $this->Rummy_model->isTableAvail($user[0]->rummy_table_id);

            $actual_points = $points*round($table->boot_value/80, 2);

            $this->Rummy_model->MinusWallet($this->data['user_id'], $actual_points);
            $comission = $this->Setting_model->Setting()->admin_commission;
            $this->Rummy_model->MakeWinner($game->id, $game->amount, $declare_log[$declare_count-1]->user_id, $comission);
        }

        $data['message'] = 'Success';
        $data['winner'] = 0;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function card_points($cards, $joker='')
    {
        $sum = 0;
        foreach ($cards as $key => $card) {
            // Joker Point is Zero
            if ($card=='JKR1' || $card=='JKR2') {
                continue;
            }

            $card_value = substr(str_replace('_', '', $card), 2);
            $joker_value = substr(str_replace('_', '', $joker), 2);

            // Middle Joker Point is Zero
            if ($card_value==$joker_value) {
                continue;
            }

            $card_int = (int) $card_value;
            $sum += ($card_int==0) ? 10 : $card_int;
        }
        return $sum;
    }

    public function chat_post()
    {
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        // if(!$user[0]->rummy_table_id)
        // {
        //     $data['message'] = 'You Are Not On Table';
        //     $data['code'] = HTTP_NOT_ACCEPTABLE;
        //     $this->response($data, 200);
        //     exit();
        // }

        // $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        // if(!$game)
        // {
        //     $data['message'] = 'Game Not Started';
        //     $data['code'] = HTTP_NOT_ACCEPTABLE;
        //     $this->response($data, 200);
        //     exit();
        // }

        $game['id'] = 1000;
        $game = (object) $game;

        $chat = $this->input->post('chat');

        if (!empty($chat)) {
            $chat_data = [
                'user_id' => $this->data['user_id'],
                'chat' => $chat,
                'game_id' => $game->id
            ];

            $this->Rummy_model->Chat($chat_data);
        }

        $chat_list = $this->Rummy_model->ChatList($game->id);
        $data['message'] = 'Success';
        $data['list'] = $chat_list;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function tip_post()
    {
        if (empty($this->data['user_id'] && $this->data['tip'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($user[0]->wallet<$this->data['tip']) {
            $data['message'] = 'Insufficiant Tip Coins';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($this->Users_model->TipAdmin($this->data['tip'], $this->data['user_id'], $user[0]->rummy_table_id)) {
            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        }

        $data['message'] = 'Invalid Tip';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function card_value_post()
    {
        $user_id = $this->data['user_id'];
        $token = $this->data['token'];
        $card_1 = $this->input->post('card_1');
        $card_2 = $this->input->post('card_2');
        $card_3 = $this->input->post('card_3');
        $card_4 = $this->input->post('card_4');
        $card_5 = $this->input->post('card_5');
        $card_6 = $this->input->post('card_6');

        if (empty($user_id) || empty($card_1) || empty($card_2) || empty($card_3)) {
            $data['message'] = 'Minimum 3 cards Needed For Grouping';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($user_id, $token)) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $active_game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);
        $joker = "";
        if ($active_game) {
            // $game_id = $active_game->id;
            $joker = $active_game->joker;
            // $data['active_game_id'] = $active_game->id;
            // $data['game_status'] = 1;
        }

        $card_value = $this->Rummy_model->CardValue('', $card_1, $card_2, $card_3, $card_4, $card_5, $card_6);
        if ($card_value) {
            // echo $joker;
            // print_r($card_value);
            if ($card_value[0]==0) {
                $card_value = $this->Rummy_model->CardValue($joker, $card_1, $card_2, $card_3, $card_4, $card_5, $card_6);
            }

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $data['card_value'] = $card_value;
            $this->response($data, HTTP_OK);
            exit();
        }

        $data['message'] = 'Invalid Card Value';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function status_post()
    {
        $user_id = $this->input->post('user_id');

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = 403;
            $this->response($data, 200);
            exit();
        }

        $table_id = $user[0]->rummy_table_id;

        if (!empty($table_id)) {
            $table_data = $this->Rummy_model->TableUser($table_id);
            // $data['table_users'] = $table_data;

            $table_new_data = array();
            for ($i=0; $i < 6; $i++) {
                $table_new_data[$i]['id'] = 0;
                $table_new_data[$i]['table_id'] = 0;
                $table_new_data[$i]['user_id'] = 0;
                $table_new_data[$i]['seat_position'] = $i+1;
                $table_new_data[$i]['added_date'] = 0;
                $table_new_data[$i]['updated_date'] = 0;
                $table_new_data[$i]['isDeleted'] = 0;
                $table_new_data[$i]['name'] = 0;
                $table_new_data[$i]['mobile'] = 0;
                $table_new_data[$i]['profile_pic'] = 0;
                $table_new_data[$i]['wallet'] = 0;
            }

            foreach ($table_data as $t => $u) {
                $table_new_data[$u->seat_position-1] = $u;
            }

            $data['table_users'] = $table_new_data;

            // $table_updated_users = $table_data;
            // // foreach ($table_data as $key => $value) {
            // //     $table_updated_users[] = $value;
            // // }

            // $found = false;
            // $plus_position = 0;
            // foreach($table_updated_users as $k=>$v)
            // {
            //     $found = $found || $v->user_id===$user_id;
            //     if(!$found)
            //     {
            //         $plus_position=$k+1;
            //         unset($table_updated_users[$k]);
            //         $table_updated_users[] = $v;
            //     }
            //     //else break can be added for performance issues
            // }
            // // echo $plus_position;
            // $i=0;
            // foreach ($table_updated_users as $ke => $va) {
            //     $table_final_users[$i] = $va;
            //     $table_final_users[$i]->seat_position = ($va->seat_position+$plus_position)%5;
            //     $i++;
            // }

            // $data['table_final_users'] = $table_final_users;
            $table = $this->Rummy_model->isTableAvail($table_id);
            $data['table_detail'] = $table;
            $data['active_game_id'] = 0;
            $data['game_status'] = 0;
            $data['table_amount'] = $table->boot_value;
            $active_game = $this->Rummy_model->getActiveGameOnTable($table_id);
            if ($active_game) {
                // $game_id = $active_game->id;
                $data['active_game_id'] = $active_game->id;
                $data['game_status'] = 1;
            }
        }

        $game_id = $this->input->post('game_id');
        if (empty($game_id)) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->View($game_id);
        if (empty($game)) {
            $data['message'] = 'Invalid Game';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game_log = $this->Rummy_model->GameLog($game_id, 1);

        $game_users = $this->Rummy_model->GameAllUser($game_id);

        $chaal = 0;
        $element = 0;
        foreach ($game_users as $key => $value) {
            if ($value->user_id==$game_log[0]->user_id) {
                $element = $key;
                break;
            }
        }

        $index = 0;
        foreach ($game_users as $key => $value) {
            $index = ($key+$element)%count($game_users);
            if ($key>0) {
                if (!$game_users[$index]->packed) {
                    $chaal = $game_users[$index]->user_id;
                    break;
                }
            }
        }

        $data['game_log'] = $game_log;
        $data['all_users'] = $table_data;
        $data['declare'] = false;
        $data['declare_user_id'] = 0;
        if ($game_log[0]->action==3) {
            $data['declare'] = true;
            $data['declare_user_id'] = $game_log[0]->user_id;
            // $data['game_users'] = $this->Rummy_model->GameAllUser($game_id);
        }
        // else
        // {
        $data['game_users'] = $this->Rummy_model->GameOnlyUser($game_id);
        // }
        $data['chaal'] = $chaal;
        $data['game_amount'] = $game->amount;
        $data['last_card'] = $this->Rummy_model->LastGameCard($game->id);
        $chaalCount = $this->Rummy_model->ChaalCount($game->id, $chaal);
        $percent = $chaalCount>0 ? CHAAL_PERCENT : NO_CHAAL_PERCENT;
        $data['cut_point'] = round(($percent / 100) * MAX_POINTS, 2);
        // if ($chaalCount>3) {
        //     $this->Rummy_model->getMyCards($game->id, $chaal);
        // }

        if (!empty($user_id)) {
            // $data['cards'] = $this->Rummy_model->getMyCards($game->id,$user_id);
            $data['drop_card'] = $this->Rummy_model->GetGameDropCard($game->id);
            $data['joker'] = $game->joker;
        }

        $data['message'] = 'Success';
        if ($game->winner_id>0) {
            $chaal = 0;
            $data['chaal'] = $chaal;
            $data['message'] = 'Game Completed';
            $game_users_cards = array();
            foreach ($data['game_users'] as $key => $value) {
                $declare_log = $this->Rummy_model->GameLog($game->id, 1, '', $value->user_id);
                $game_users_cards[$key]['user'] = $value;
                $game_users_cards[$key]['user']->win = ($game->winner_id==$value->user_id) ? $game->user_winning_amt : $declare_log[0]->amount;
                $game_users_cards[$key]['user']->result = $declare_log[0]->action;
                $game_users_cards[$key]['user']->score = $declare_log[0]->points;
                $game_users_cards[$key]['user']->cards = json_decode($this->Rummy_model->GameLogJson($game->id, $value->user_id));
            }
            $data['game_users_cards'] = $game_users_cards;
            $data['game_status'] = 2;
            $data['winner_user_id'] = $game->winner_id;
        }
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function my_card_post()
    {
        $user_id = $this->input->post('user_id');
        $token = $this->input->post('token');

        if (!$this->Users_model->TokenConfirm($user_id, $token)) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!empty($user_id)) {
            $data['cards'] = $this->Rummy_model->getMyCards($game->id, $user_id);
        }

        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function user_card_post()
    {
        $user_id = $this->input->post('user_id');
        $token = $this->input->post('token');

        if (!$this->Users_model->TokenConfirm($user_id, $token)) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game_users = $this->Rummy_model->GameAllUser($game->id);

        foreach ($game_users as $key => $value) {
            $cards = $this->Rummy_model->getMyCards($game->id, $value->user_id);
            $game_users[$key]->cards = $cards;
            $game_users[$key]->joker = $game->joker;
        }

        $data['message'] = 'Success';
        $data['game_users'] = $game_users;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function swap_card_post()
    {
        $user_id = $this->data['user_id'];
        $my_card = $this->data['my_card'];
        $new_card = $this->data['new_card'];

        if (empty($user_id)) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($user_id, $this->data['token'])) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $cards = $this->Rummy_model->getMyCards($game->id, $user_id);

        // if (count($cards)<=RUMMY_CARDS) {
        //     $data['message'] = 'Please Get Or Pick Card First And Then Drop One';
        //     $data['code'] = HTTP_NOT_ACCEPTABLE;
        //     $this->response($data, 200);
        //     exit();
        // }

        $isCardAvailable = $this->Rummy_model->getMyCards($game->id, $user_id, $my_card);

        if ($isCardAvailable) {
            $this->Rummy_model->SwapCards($user_id, $game->id, $my_card, $new_card);

            $data['message'] = 'Success';
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        }

        $data['message'] = 'Invalid Card';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function get_available_cards_post()
    {
        $user_id = $this->data['user_id'];

        if (empty($user_id)) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($user_id, $this->data['token'])) {
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

        if (!$user[0]->rummy_table_id) {
            $data['message'] = 'You Are Not On Table';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $game = $this->Rummy_model->getActiveGameOnTable($user[0]->rummy_table_id);

        if (!$game) {
            $data['message'] = 'Game Not Started';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $data['cards_list'] = $this->Rummy_model->GetGameCard($game->id);
        $data['joker'] = $game->joker;
        $this->response($data, HTTP_OK);
        exit();

        $data['message'] = 'Invalid Card';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }
}