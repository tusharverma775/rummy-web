<?php

class AnderBahar_model extends MY_Model
{
    public function getRoom($RoomId='', $user_id='')
    {
        // $this->db->select('id,main_card,status,added_date');
        $this->db->from('tbl_ander_baher_room');
        $this->db->where('isDeleted', false);
        if (!empty($RoomId)) {
            $this->db->where('id', $RoomId);
        }
        $this->db->order_by('id', 'asc');
        $Query = $this->db->get();

        $this->db->set('ander_bahar_room_id', $RoomId); //value that used to update column
        $this->db->set('updated_date', date('Y-m-d H:i:s')); //value that used to update column
        $this->db->where('id', $user_id); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $Query->result();
    }

    public function leave_room($user_id='')
    {
        $this->db->set('ander_bahar_room_id', ''); //value that used to update column
        $this->db->where('id', $user_id); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $this->db->last_query();
    }

    public function getRoomOnline($RoomId)
    {
        $Query = $this->db->query('SELECT COUNT(`id`) as online FROM `tbl_ander_baher_bet` WHERE `ander_baher_id` = (SELECT `id` FROM `tbl_ander_baher` WHERE `room_id`='.$RoomId.' ORDER BY `id` DESC LIMIT 1)');
        return $Query->row()->online;
    }

    public function getRoomOnlineUser($RoomId)
    {
        $Query = $this->db->query('SELECT * FROM `tbl_users`  WHERE ander_bahar_room_id = '.$RoomId);
        return $Query->result();
    }

    public function getActiveGameOnTable($RoomId='')
    {
        // $this->db->select('id,main_card,status,added_date');
        $this->db->from('tbl_ander_baher');
        if (!empty($RoomId)) {
            $this->db->where('room_id', $RoomId);
        }
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GetCards($limit, $not_equal_to='', $equal_to='')
    {
        $this->db->from('tbl_cards');

        if (!empty($not_equal_to)) {
            $this->db->where("cards NOT LIKE '%$not_equal_to'", "", false);
        }

        if (!empty($equal_to)) {
            $this->db->where("cards LIKE '%$equal_to'", "", false);
        }

        $this->db->limit($limit);
        $this->db->order_by('id', 'RANDOM');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function GetGameCards($game_id)
    {
        $this->db->from('tbl_ander_baher_map');
        $this->db->where('ander_bahar_id', $game_id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function CreateMap($ander_bahar_id, $card)
    {
        $ander_data = ['ander_bahar_id' => $ander_bahar_id, 'card' => $card, 'added_date' => date('Y-m-d H:i:s')];
        $this->db->insert('tbl_ander_baher_map', $ander_data);
        return $this->db->insert_id();
    }

    public function PlaceBet($bet_data)
    {
        $this->db->insert('tbl_ander_baher_bet', $bet_data);
        return $this->db->insert_id();
    }

    public function DeleteBet($bet_id, $user_id, $game_id)
    {
        return $this->db->where('ander_baher_id', $game_id)->where('user_id', $user_id)->delete('tbl_ander_baher_bet');
    }

    public function MinusWallet($user_id, $amount)
    {
        $this->db->set('wallet', 'wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->select('winning_wallet');
        $this->db->from('tbl_users');
        $this->db->where('id', $user_id);
        $Query = $this->db->get();
        $winning_wallet = $Query->row()->winning_wallet;

        $winning_wallet_minus = ($winning_wallet>$amount) ? $amount : $winning_wallet;

        if ($winning_wallet_minus>0) {
            $this->db->set('winning_wallet', 'winning_wallet-' . $winning_wallet_minus, false);
            $this->db->where('id', $user_id);
            $this->db->update('tbl_users');
        }

        return $this->db->affected_rows();
    }

    public function AddWallet($user_id, $amount)
    {
        $this->db->set('wallet', 'wallet+' . $amount, false);
        $this->db->set('winning_wallet', 'winning_wallet+' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        return $this->db->affected_rows();
    }

    public function View($id)
    {
        $this->db->from('tbl_ander_baher');
        $this->db->where('id', $id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->row();
    }

    public function Update($data, $game_id)
    {
        $this->db->where('id', $game_id);
        $this->db->update('tbl_ander_baher', $data);
        $GameId =  $this->db->affected_rows();
        // echo $this->db->last_query();
        return $GameId;
    }

    public function ViewBet($user_id='', $ander_baher_id='', $bet='', $bet_id='', $limit='')
    {
        // echo $bet;
        $this->db->from('tbl_ander_baher_bet');

        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }

        if (!empty($ander_baher_id)) {
            $this->db->where('ander_baher_id', $ander_baher_id);
        }

        if ($bet!=='') {
            $this->db->where('bet', $bet);
        }

        if ($bet_id!='') {
            $this->db->where('id', $bet_id);
        }

        if ($limit!='') {
            $this->db->limit($limit);
        }

        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function LastWinningBet($room_id, $limit=10)
    {
        // echo $bet;
        $this->db->from('tbl_ander_baher');
        $this->db->where('status', 1);
        if (!empty($room_id)) {
            $this->db->where('room_id', $room_id);
        }
        if (!empty($limit)) {
            $this->db->limit($limit);
        }

        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function TotalBetAmount($ander_baher_id, $bet='', $user_id='')
    {
        $this->db->select('SUM(amount) as amount', false);
        $this->db->from('tbl_ander_baher_bet');
        $this->db->where('ander_baher_id', $ander_baher_id);
        if ($user_id!='') {
            $this->db->where('user_id', $user_id);
        }
        if ($bet!=='') {
            $this->db->where('bet', $bet);
        }
        $Query = $this->db->get();
        // echo $this->db->last_query();
        if ($Query->row()) {
            return $Query->row()->amount;
        }
        return '0';
    }

    public function MakeWinner($user_id, $bet_id, $amount, $comission, $game_id)
    {
        $admin_winning_amt = round($amount * round($comission/100, 2));
        $user_winning_amt = round($amount - $admin_winning_amt, 2);
        $this->db->set('winning_amount', $amount);
        $this->db->set('user_amount', $user_winning_amt);
        $this->db->set('comission_amount', $admin_winning_amt);
        $this->db->where('id', $bet_id);
        $this->db->update('tbl_ander_baher_bet');

        $this->db->set('winning_amount', 'winning_amount+' . $amount, false);
        $this->db->set('user_amount', 'user_amount+' . $user_winning_amt, false);
        $this->db->set('comission_amount', 'comission_amount+' . $admin_winning_amt, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_ander_baher');

        $this->db->set('wallet', 'wallet+' . $user_winning_amt, false);
        $this->db->set('winning_wallet', 'winning_wallet+' . $user_winning_amt, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->set('admin_coin', 'admin_coin+' . $admin_winning_amt, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->update('tbl_admin');
        return true;
    }

    public function isTableAvail($TableId)
    {
        $this->db->from('tbl_table');
        $this->db->where('isDeleted', false);
        $this->db->where('id', $TableId);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function getAllGameOnTable($TableId)
    {
        $this->db->from('tbl_game');
        $this->db->where('isDeleted', false);
        $this->db->where('table_id', $TableId);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function Create($room_id, $card)
    {
        $ander_data = ['room_id' => $room_id,'main_card' => $card, 'added_date' => date('Y-m-d H:i:s')];
        $this->db->insert('tbl_ander_baher', $ander_data);
        return $this->db->insert_id();
    }

    public function GiveGameCards($data)
    {
        $this->db->insert('tbl_game_card', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function Delete($id)
    {
        $return = false;
        $this->db->set('isDeleted', true); //value that used to update column
        $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_game');  //table name

        return $return;
    }

    public function AllCards()
    {
        $Query = $this->db->select('cards')
            ->from('tbl_cards')
            ->get();
        return $Query->result();
    }

    public function GameCard($game_id)
    {
        $this->db->select('card1,card2,card3');
        $this->db->from('tbl_game_card');
        $this->db->where('game_id', $game_id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function Comission()
    {
        $this->db->select('tbl_ander_baher.*');
        $this->db->from('tbl_ander_baher');
        // $this->db->where('isDeleted', false);
        $this->db->where('winning_amount>', 0);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function AllGames()
    {
        $this->db->select('tbl_ander_baher.*,(select count(id) from tbl_ander_baher_bet where tbl_ander_baher.id=tbl_ander_baher_bet.ander_baher_id) as total_users');
        $this->db->from('tbl_ander_baher');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(10);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function getRandomFlag($column)
    {
        $this->db->select($column);
        $this->db->from('tbl_admin');
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        return $Query->row();
    }
    public function ChangeStatus()
    {
        $return = false;
        $this->db->set('ander_bahar_random', $this->input->post('type')); //value that used to update column
        // $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_admin');  //table name
        return $return;
    }
}