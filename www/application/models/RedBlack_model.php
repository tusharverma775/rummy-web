<?php

class RedBlack_model extends MY_Model
{
    public function getRoom($RoomId='', $user_id='')
    {
        // $this->db->select('id,main_card,status,added_date');
        $this->db->from('tbl_red_black_room');
        $this->db->where('isDeleted', false);
        if (!empty($RoomId)) {
            $this->db->where('id', $RoomId);
        }
        $this->db->order_by('id', 'asc');
        $Query = $this->db->get();

        $this->db->set('red_black_id', $RoomId); //value that used to update column
        $this->db->where('id', $user_id); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $Query->result();
    }

    public function leave_room($user_id='')
    {
        $this->db->set('red_black_id', ''); //value that used to update column
        $this->db->where('id', $user_id); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $this->db->last_query();
    }

    public function getRoomOnline($RoomId)
    {
        $Query = $this->db->query('SELECT COUNT(`id`) as online FROM `tbl_red_black_bet` WHERE `red_black_id` = (SELECT `id` FROM `tbl_red_black` WHERE `room_id`='.$RoomId.' ORDER BY `id` DESC LIMIT 1)');
        return $Query->row()->online;
    }

    public function getRoomOnlineUser($RoomId)
    {
        $Query = $this->db->query('SELECT * FROM `tbl_users`  WHERE red_black_id = '.$RoomId);
        return $Query->result();
    }

    public function getActiveGameOnTable($RoomId='')
    {
        // $this->db->select('id,main_card,status,added_date');
        $this->db->from('tbl_red_black');
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
        $this->db->from('tbl_red_black_map');
        $this->db->where('red_black_id', $game_id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function CreateMap($red_black_id, $card)
    {
        $ander_data = ['red_black_id' => $red_black_id, 'card' => $card, 'added_date' => date('Y-m-d H:i:s')];
        $this->db->insert('tbl_red_black_map', $ander_data);
        return $this->db->insert_id();
    }

    public function PlaceBet($bet_data)
    {
        $this->db->insert('tbl_red_black_bet', $bet_data);
        return $this->db->insert_id();
    }

    public function DeleteBet($bet_id, $user_id, $game_id)
    {
        return $this->db->where('red_black_id', $game_id)->where('user_id', $user_id)->delete('tbl_red_black_bet');
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
        $this->db->from('tbl_red_black');
        $this->db->where('id', $id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->row();
    }

    public function Update($data, $game_id)
    {
        $this->db->where('id', $game_id);
        $this->db->update('tbl_red_black', $data);
        $GameId =  $this->db->affected_rows();
        // echo $this->db->last_query();
        return $GameId;
    }

    public function ViewBet($user_id='', $red_black_id='', $bet='', $bet_id='', $limit='')
    {
        // echo $bet;
        $this->db->from('tbl_red_black_bet');

        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }

        if (!empty($red_black_id)) {
            $this->db->where('red_black_id', $red_black_id);
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

    public function TotalBetAmount($red_black_id, $bet='')
    {
        $this->db->select('SUM(amount) as amount', false);
        $this->db->from('tbl_red_black_bet');
        $this->db->where('red_black_id', $red_black_id);
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
        $this->db->update('tbl_red_black_bet');

        $this->db->set('winning_amount', 'winning_amount+' . $amount, false);
        $this->db->set('user_amount', 'user_amount+' . $user_winning_amt, false);
        $this->db->set('comission_amount', 'comission_amount+' . $admin_winning_amt, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_red_black');

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
        $this->db->from('tbl_red_black');
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
        $this->db->insert('tbl_red_black', $ander_data);
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
        $que = 'SELECT tbl_red_black.id,tbl_red_black.end_datetime as time,SUM(tbl_red_black_bet.winning_amount) as rewards,(SELECT GROUP_CONCAT(`card`) FROM `tbl_red_black_map` WHERE `red_black_id`=tbl_red_black.id GROUP BY `red_black_id`) as type,COUNT(tbl_red_black_bet.id) as winners FROM `tbl_red_black` JOIN tbl_red_black_bet ON tbl_red_black.id=tbl_red_black_bet.red_black_id WHERE tbl_red_black.`winning`=6 AND tbl_red_black.status=1 GROUP BY tbl_red_black.id ORDER BY tbl_red_black.id DESC';
        if (!empty($limit)) {
            $que .= ' LIMIT '.$limit;
        }
        $Query = $this->db->query($que);
        return $Query->result();
    }

    public function getJackpotBigWinners($red_black_id)
    {
        $Query = $this->db->query('SELECT tbl_red_black_bet.amount,tbl_red_black_bet.winning_amount,tbl_users.name,tbl_users.profile_pic FROM `tbl_red_black_bet` JOIN tbl_users ON tbl_red_black_bet.user_id=tbl_users.id WHERE tbl_red_black_bet.`red_black_id`='.$red_black_id.' ORDER BY winning_amount DESC LIMIT 1');
        return $Query->result();
    }

    public function AllGames()
    {
        $this->db->select('tbl_red_black.*,(select count(id) from tbl_red_black_bet where tbl_red_black.id=tbl_red_black_bet.red_black_id) as total_users');
        $this->db->from('tbl_red_black');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(10);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function Comission()
    {
        $this->db->from('tbl_red_black');
        // $this->db->where('isDeleted', false);
        $this->db->where('winning_amount>', 0);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function CardValue($card1, $card2, $card3)
    {
        $rule = 1;
        $value = 0;
        $value2 = 0;
        $value3 = 0;

        $card1_color = substr($card1, 0, 2);
        $card1_num = substr($card1, 2);

        $card2_color = substr($card2, 0, 2);
        $card2_num = substr($card2, 2);

        $card3_color = substr($card3, 0, 2);
        $card3_num = substr($card3, 2);

        if (($card1_num == $card2_num) && ($card2_num == $card3_num)) {
            $card1_num = str_replace(
                array("J", "Q", "K", "A"),
                array(11, 12, 13, 14),
                $card1_num
            );
            $card1_num = (int) $card1_num;
            $rule = 6;
            $value = $card1_num;
        } else {
            $card1_num = str_replace(
                array("J", "Q", "K", "A"),
                array(11, 12, 13, 14),
                $card1_num
            );
            $card2_num = str_replace(
                array("J", "Q", "K", "A"),
                array(11, 12, 13, 14),
                $card2_num
            );
            $card3_num = str_replace(
                array("J", "Q", "K", "A"),
                array(11, 12, 13, 14),
                $card3_num
            );

            $card1_num = (int) $card1_num;
            $card2_num = (int) $card2_num;
            $card3_num = (int) $card3_num;

            $arr = [$card1_num, $card2_num, $card3_num];
            sort($arr);

            $sequence = false;
            if (($arr[0] == $arr[1] - 1) && ($arr[1] == $arr[2] - 1)) {
                $sequence = true;
            }

            //Exception for A23
            if ($arr[0]==2 && $arr[1]==3 && $arr[2]==14) {
                $sequence = true;
                $arr[2] = 3;
            }

            $color = false;
            if (($card1_color == $card2_color) && ($card2_color == $card3_color)) {
                $color = true;
            }

            if ($sequence && $color) {
                $rule = 5;
                $value = $arr[2];
            } elseif ($sequence) {
                $rule = 4;
                $value = $arr[2];
            } elseif ($color) {
                $rule = 3;
                $value = $arr[2];
            } else {
                if (($card1_num == $card2_num) || ($card2_num == $card3_num) ||
                    ($card1_num == $card3_num)
                ) {
                    $rule = 2;
                    if ($card1_num == $card2_num) {
                        $value = $card1_num;
                        $value2 = $card3_num;
                    } elseif ($card2_num == $card3_num) {
                        $value = $card2_num;
                        $value2 = $card1_num;
                    } elseif ($card1_num == $card3_num) {
                        $value = $card3_num;
                        $value2 = $card2_num;
                    }
                } else {
                    $rule = 1;
                    $value = $arr[2];
                    $value2 = $arr[1];
                    $value3 = $arr[0];
                }
            }
        }
        return array($rule, $value, $value2, $value3);
    }

    public function getWinnerPosition($user1, $user2)
    {
        $winner = '';

        if ($user1[0] == $user2[0]) {
            switch ($user1[0]) {
                case 6:
                    $winner = ($user1[1] > $user2[1]) ? 0 : 1;
                    break;

                case 5:
                case 4:
                    if ($user1[1] == $user2[1]) {
                        $winner = 2;
                    } else {
                        //Exception for A23
                        $user1[1] = ($user1[1]==14) ? 15 : $user1[1];
                        $user2[1] = ($user2[1]==14) ? 15 : $user2[1];

                        $user1[1] = ($user1[1]==3) ? 14 : $user1[1];
                        $user2[1] = ($user2[1]==3) ? 14 : $user2[1];

                        $winner = ($user1[1] > $user2[1]) ? 0 : 1;
                    }
                    break;
                case 3:
                    if ($user1[1] == $user2[1]) {
                        $winner = 2;
                    } else {
                        $winner = ($user1[1] > $user2[1]) ? 0 : 1;
                    }
                    break;

                case 2:
                    if ($user1[1] == $user2[1]) {
                        if ($user1[2] == $user2[2]) {
                            $winner = 2;
                        } else {
                            $winner = ($user1[2] > $user2[2]) ? 0 : 1;
                        }
                    } else {
                        $winner = ($user1[1] > $user2[1]) ? 0 : 1;
                    }
                    break;

                case 1:

                    if ($user1[1] == $user2[1]) {
                        if ($user1[2] == $user2[2]) {
                            if ($user1[3] == $user2[3]) {
                                $winner = 2;
                            } else {
                                $winner = ($user1[3] > $user2[3]) ? 0 : 1;
                            }
                        } else {
                            $winner = ($user1[2] > $user2[2]) ? 0 : 1;
                        }
                    } else {
                        $winner = ($user1[1] > $user2[1]) ? 0 : 1;
                    }
                    break;
            }
        } else {
            $winner = ($user1[0] > $user2[0]) ? 0 : 1;
        }

        return $winner;
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
        $this->db->set('red_black_random', $this->input->post('type')); //value that used to update column
        // $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_admin');  //table name
        return $return;
    }
}