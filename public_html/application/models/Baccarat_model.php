<?php

class Baccarat_model extends MY_Model
{
    public function getRoom($RoomId='', $user_id='')
    {
        // $this->db->select('id,main_card,status,added_date');
        $this->db->from('tbl_baccarat_room');
        $this->db->where('isDeleted', false);
        if (!empty($RoomId)) {
            $this->db->where('id', $RoomId);
        }
        $this->db->order_by('id', 'asc');
        $Query = $this->db->get();

        $this->db->set('baccarat_id', $RoomId); //value that used to update column
        $this->db->where('id', $user_id); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $Query->result();
    }

    public function leave_room($user_id='')
    {
        $this->db->set('baccarat_id', ''); //value that used to update column
        $this->db->where('id', $user_id); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $this->db->last_query();
    }

    public function getRoomOnline($RoomId)
    {
        $Query = $this->db->query('SELECT COUNT(`id`) as online FROM `tbl_baccarat_bet` WHERE `baccarat_id` = (SELECT `id` FROM `tbl_baccarat` WHERE `room_id`='.$RoomId.' ORDER BY `id` DESC LIMIT 1)');
        return $Query->row()->online;
    }

    public function getRoomOnlineUser($RoomId)
    {
        $Query = $this->db->query('SELECT * FROM `tbl_users`  WHERE baccarat_id = '.$RoomId);
        return $Query->result();
    }

    public function getActiveGameOnTable($RoomId='')
    {
        // $this->db->select('id,main_card,status,added_date');
        $this->db->from('tbl_baccarat');
        if (!empty($RoomId)) {
            $this->db->where('room_id', $RoomId);
        }
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GetCards($limit='')
    {
        $this->db->from('tbl_cards');
        $this->db->where('cards!=', 'JKR1');
        $this->db->where('cards!=', 'JKR2');
        $this->db->limit($limit);
        $this->db->order_by('id', 'RANDOM');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function GetGameCards($game_id)
    {
        $this->db->from('tbl_baccarat_map');
        $this->db->where('baccarat_id', $game_id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function CreateMap($baccarat_id, $card)
    {
        $ander_data = ['baccarat_id' => $baccarat_id, 'card' => $card, 'added_date' => date('Y-m-d H:i:s')];
        $this->db->insert('tbl_baccarat_map', $ander_data);
        return $this->db->insert_id();
    }

    public function PlaceBet($bet_data)
    {
        $this->db->insert('tbl_baccarat_bet', $bet_data);
        return $this->db->insert_id();
    }

    public function DeleteBet($bet_id, $user_id, $game_id)
    {
        return $this->db->where('baccarat_id', $game_id)->where('user_id', $user_id)->delete('tbl_baccarat_bet');
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
        $this->db->from('tbl_baccarat');
        $this->db->where('id', $id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->row();
    }

    public function Update($data, $game_id)
    {
        $this->db->where('id', $game_id);
        $this->db->update('tbl_baccarat', $data);
        $GameId =  $this->db->affected_rows();
        // echo $this->db->last_query();
        return $GameId;
    }

    public function ViewBet($user_id='', $baccarat_id='', $bet='', $bet_id='', $limit='')
    {
        // echo $bet;
        $this->db->from('tbl_baccarat_bet');

        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }

        if (!empty($baccarat_id)) {
            $this->db->where('baccarat_id', $baccarat_id);
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

    public function TotalBetAmount($baccarat_id, $bet='')
    {
        $this->db->select('SUM(amount) as amount', false);
        $this->db->from('tbl_baccarat_bet');
        $this->db->where('baccarat_id', $baccarat_id);
        if ($bet!=='') {
            $this->db->where('bet', $bet);
        }
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->row()->amount;
    }

    public function MakeWinner($user_id, $bet_id, $amount, $comission, $game_id)
    {
        $admin_winning_amt = round($amount * round($comission/100, 2));
        $user_winning_amt = round($amount - $admin_winning_amt, 2);
        $this->db->set('winning_amount', $amount);
        $this->db->set('user_amount', $user_winning_amt);
        $this->db->set('comission_amount', $admin_winning_amt);
        $this->db->where('id', $bet_id);
        $this->db->update('tbl_baccarat_bet');

        $this->db->set('winning_amount', 'winning_amount+' . $amount, false);
        $this->db->set('user_amount', 'user_amount+' . $user_winning_amt, false);
        $this->db->set('comission_amount', 'comission_amount+' . $admin_winning_amt, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_baccarat');

        $this->db->set('wallet', 'wallet+' . $user_winning_amt, false);
        $this->db->set('winning_wallet', 'winning_wallet+' . $user_winning_amt, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->set('admin_coin', 'admin_coin+' . $admin_winning_amt, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->update('tbl_admin');
        return true;
    }

    public function LastWinningBet($room_id, $limit=10)
    {
        // echo $bet;
        $this->db->from('tbl_baccarat');
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

    public function Create($room_id)
    {
        $ander_data = ['room_id' => $room_id, 'added_date' => date('Y-m-d H:i:s')];
        $this->db->insert('tbl_baccarat', $ander_data);
        return $this->db->insert_id();
    }

    public function AllCards()
    {
        $Query = $this->db->select('cards')
            ->from('tbl_cards')
            ->get();
        return $Query->result();
    }

    public function getJackpotWinners($limit='')
    {
        $que = 'SELECT tbl_baccarat.id,tbl_baccarat.end_datetime as time,SUM(tbl_baccarat_bet.winning_amount) as rewards,(SELECT GROUP_CONCAT(`card`) FROM `tbl_baccarat_map` WHERE `baccarat_id`=tbl_baccarat.id GROUP BY `baccarat_id`) as type,COUNT(tbl_baccarat_bet.id) as winners FROM `tbl_baccarat` JOIN tbl_baccarat_bet ON tbl_baccarat.id=tbl_baccarat_bet.baccarat_id WHERE tbl_baccarat.`winning`=6 AND tbl_baccarat.status=1 GROUP BY tbl_baccarat.id ORDER BY tbl_baccarat.id DESC';
        if (!empty($limit)) {
            $que .= ' LIMIT '.$limit;
        }
        $Query = $this->db->query($que);
        return $Query->result();
    }

    public function getJackpotBigWinners($baccarat_id)
    {
        $Query = $this->db->query('SELECT tbl_baccarat_bet.amount,tbl_baccarat_bet.winning_amount,tbl_users.name,tbl_users.profile_pic FROM `tbl_baccarat_bet` JOIN tbl_users ON tbl_baccarat_bet.user_id=tbl_users.id WHERE tbl_baccarat_bet.`baccarat_id`='.$baccarat_id.' ORDER BY winning_amount DESC LIMIT 1');
        return $Query->result();
    }

    public function AllGames()
    {
        $this->db->select('tbl_baccarat.*,(select count(id) from tbl_baccarat_bet where tbl_baccarat.id=tbl_baccarat_bet.baccarat_id) as total_users');
        $this->db->from('tbl_baccarat');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(10);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function Comission()
    {
        $this->db->from('tbl_baccarat');
        // $this->db->where('isDeleted', false);
        $this->db->where('winning_amount>', 0);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function CardValue($card1, $card2, $card3='000')
    {
        $points = 1;

        $card1_num = (int) substr($card1, 2);

        $card2_num = (int) substr($card2, 2);

        $card3_num = (int) substr($card3, 2);

        $total_points = $card1_num+$card2_num+$card3_num;
        return ($total_points%10);
    }

    public function isPair($card1, $card2)
    {
        $points = 1;

        $card1_num = substr($card1, 2);

        $card2_num = substr($card2, 2);

        return ($card1_num==$card2_num) ? true : false;
    }

    public function getWinner($player, $banker)
    {
        $winner = '';

        if ($player == $banker) {
            $winner = TIE;
        } else {
            $winner = ($player>$banker) ? PLAYER : BANKER;
        }

        return $winner;
    }

    public function getMultiply($winner)
    {
        $multiply = 0;
        switch ($winner) {
            case PLAYER:
                $multiply = PLAYER_MULTIPLE;
                break;

            case BANKER:
                $multiply = BANKER_MULTIPLE;
                break;

            case TIE:
                $multiply = TIE_MULTIPLE;
                break;

            case PLAYER_PAIR:
                $multiply = PLAYER_PAIR_MULTIPLE;
                break;

            case BANKER_PAIR:
                $multiply = BANKER_PAIR_MULTIPLE;
                break;

            default:
                $multiply = 0;
                break;
        }

        return $multiply;
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
        $this->db->set('bacarate_random', $this->input->post('type')); //value that used to update column
        // $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_admin');  //table name
        return $return;
    }
}
