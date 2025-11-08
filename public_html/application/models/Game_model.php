<?php

class Game_model extends MY_Model
{
    public function getActiveTable()
    {
        $this->db->select('tbl_users.table_id,COUNT(tbl_users.id) AS members,tbl_table.private,tbl_table.boot_value');
        $this->db->from('tbl_users');
        $this->db->join('tbl_table', 'tbl_users.table_id=tbl_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        // $this->db->where('tbl_table.private', false);
        $this->db->where('tbl_users.table_id!=', 0);
        $this->db->group_by('tbl_users.table_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function getPublicActiveTable()
    {
        $this->db->select('tbl_users.table_id,COUNT(tbl_users.id) AS members');
        $this->db->from('tbl_users');
        $this->db->join('tbl_table', 'tbl_users.table_id=tbl_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->where('tbl_table.private', false);
        $this->db->where('tbl_users.table_id!=', 0);
        $this->db->group_by('tbl_users.table_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function getCustomizeActiveTable($boot_value)
    {
        $this->db->select('tbl_users.table_id,COUNT(tbl_users.id) AS members');
        $this->db->from('tbl_users');
        $this->db->join('tbl_table', 'tbl_users.table_id=tbl_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        // $this->db->where('tbl_table.private', 2);
        $this->db->where('tbl_table.boot_value', $boot_value);
        $this->db->where('tbl_users.table_id!=', 0);
        $this->db->group_by('tbl_users.table_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function getTableMaster($boot_value='')
    {
        $this->db->select('tbl_table_master.*,COUNT(tbl_users.id) AS online_members,(tbl_table_master.boot_value)*50 as min_amount');
        $this->db->from('tbl_table_master');
        $this->db->join('tbl_table', 'tbl_table_master.boot_value=tbl_table.boot_value AND tbl_table.isDeleted=0', 'left');
        $this->db->join('tbl_users', 'tbl_users.table_id=tbl_table.id AND tbl_users.isDeleted=0', 'left');
        $this->db->where('tbl_table_master.isDeleted', false);
        // $this->db->where('tbl_users.table_id!=', 0);
        if (!empty($boot_value)) {
            $this->db->where('tbl_table_master.boot_value', $boot_value);
        }
        $this->db->group_by('tbl_table_master.boot_value');
        $this->db->order_by('tbl_table_master.boot_value');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function isTable($TableId)
    {
        $this->db->select('table_id');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('table_id', $TableId);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function isTableAvail($TableId)
    {
        $this->db->from('tbl_table');
        $this->db->where('isDeleted', false);
        $this->db->where('id', $TableId);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function GetSeatOnTable($TableId)
    {
        $sql = "SELECT * FROM ( SELECT 1 AS mycolumn UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 ) a WHERE mycolumn NOT in ( SELECT seat_position FROM `tbl_table_user` WHERE table_id=" . $TableId . " AND isDeleted=0 ) LIMIT 1";
        $Query = $this->db->query($sql, false);
        return $Query->row()->mycolumn;
    }

    public function TableUser($TableId)
    {
        $this->db->select('tbl_table_user.*,tbl_users.user_type,tbl_users.name,tbl_users.mobile,tbl_users.profile_pic,tbl_users.wallet');
        $this->db->from('tbl_table_user');
        $this->db->join('tbl_users', 'tbl_table_user.user_id=tbl_users.id');
        $this->db->where('tbl_table_user.isDeleted', false);
        $this->db->where('tbl_table_user.table_id', $TableId);
        $this->db->order_by('tbl_table_user.seat_position', 'asc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameUser($game_id)
    {
        $this->db->from('tbl_game_card');
        $this->db->where('packed', false);
        $this->db->where('game_id', $game_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameUserCard($game_id, $user_id)
    {
        $this->db->from('tbl_game_card');
        $this->db->where('packed', false);
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function getGameBot($game_id)
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->join('tbl_game_card', 'tbl_game_card.user_id=tbl_users.id');
        $this->db->where('tbl_users.user_type', 1);
        $this->db->where('tbl_game_card.packed', false);
        $this->db->where('tbl_game_card.game_id', $game_id);
        $Query = $this->db->get();
        return $Query->row()->id ?? false;
    }

    public function isLeaveTable($user_id)
    {
        $return = false;
        $this->db->from('tbl_game_log');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();

        $last_log = $Query->row();

        if ($last_log->action == 1 && $last_log->timeout == 1) {
            $return = true;
        }

        return $return;
    }

    public function GameAllUser($game_id)
    {
        $this->db->select('tbl_game_card.*,tbl_users.name');
        $this->db->from('tbl_game_card');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_game_card.user_id');
        $this->db->where('tbl_game_card.game_id', $game_id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function GameOnlyUser($game_id)
    {
        $this->db->select('user_id,packed,seen');
        $this->db->from('tbl_game_card');
        $this->db->where('game_id', $game_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameLog($game_id, $limit = '')
    {
        $this->db->from('tbl_game_log');
        $this->db->where('game_id', $game_id);
        $this->db->order_by('id', 'DESC');
        if (!empty($limit)) {
            $this->db->limit($limit);
        }
        $Query = $this->db->get();
        return $Query->result();
    }

    public function LastChaalAmount($game_id)
    {
        $this->db->from('tbl_game_log');
        $this->db->where('game_id', $game_id);
        $this->db->where_in('action', [0, 2]);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $Query = $this->db->get();
        return $Query->row()->amount;
    }

    public function LastChaal($game_id)
    {
        $this->db->from('tbl_game_log');
        $this->db->where('game_id', $game_id);
        $this->db->where_in('action', [0, 2]);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function ChaalCount($game_id, $user_id)
    {
        $this->db->from('tbl_game_log');
        $this->db->where('game_id', $game_id);
        $this->db->where('action', 2);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        return $Query->num_rows();
    }

    public function getActiveGameOnTable($TableId)
    {
        $this->db->from('tbl_game');
        $this->db->where('isDeleted', false);
        $this->db->where('winner_id', 0);
        $this->db->where('table_id', $TableId);
        $this->db->order_by('id', 'desc');
        $this->db->limit(1);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
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

    public function getMyCards($game_id, $user_id)
    {
        $this->db->set('seen', 1); //value that used to update column
        $this->db->where('user_id', $user_id); //which row want to upgrade
        $this->db->where('game_id', $game_id); //which row want to upgrade
        $this->db->update('tbl_game_card');  //table name

        $this->db->select('card1,card2,card3');
        $this->db->from('tbl_game_card');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GetCards($limit, $robot_card_selected)
    {
        $this->db->from('tbl_cards');
        $this->db->order_by('id', 'RANDOM');
        if (!empty($robot_card_selected)) {
            $this->db->where_not_in('cards', $robot_card_selected);
        }
        $this->db->limit($limit);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GetRobotCards($limit)
    {
        $this->db->from('tbl_robot_cards');
        $this->db->order_by('id', 'RANDOM');
        $this->db->limit($limit);
        $this->db->where('isDeleted', 0);
        $Query = $this->db->get();
        return $Query->result();
    }


    public function ChatList($game_id)
    {
        $this->db->from('tbl_chat');
        $this->db->where('game_id', $game_id);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function Create($data)
    {
        $this->db->insert('tbl_game', $data);
        $GameId =  $this->db->insert_id();

        return $GameId;
    }

    public function Chat($data)
    {
        $this->db->insert('tbl_chat', $data);
        return $this->db->insert_id();
    }

    public function CreateTable($data)
    {
        $this->db->insert('tbl_table', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function AddTableUser($data)
    {
        $this->db->insert('tbl_table_user', $data);
        $TableId =  $this->db->insert_id();

        $this->db->set('table_id', $data['table_id']); //value that used to update column
        $this->db->where('id', $data['user_id']); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $TableId;
    }

    public function RemoveTableUser($data)
    {
        $this->db->set('isDeleted', 1); //value that used to update column
        $this->db->where('user_id', $data['user_id']); //which row want to upgrade
        $this->db->where('table_id', $data['table_id']); //which row want to upgrade
        $this->db->update('tbl_table_user');  //table name

        $this->db->set('table_id', 0); //value that used to update column
        $this->db->where('id', $data['user_id']); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return true;
    }

    public function PackGame($user_id, $game_id, $timeout = 0)
    {
        $this->db->set('packed', 1); //value that used to update column
        $this->db->where('user_id', $user_id); //which row want to upgrade
        $this->db->where('game_id', $game_id); //which row want to upgrade
        $this->db->update('tbl_game_card');  //table name

        $this->db->select('seen');
        $this->db->from('tbl_game_card');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        $seen_row = $Query->row();
        $seen = 0;
        if ($seen_row) {
            $seen = $seen_row->seen;
        }

        $data = [
            'user_id' => $user_id,
            'game_id' => $game_id,
            'seen' => $seen,
            'timeout' => $timeout,
            'action' => 1,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_game_log', $data);
        return true;
    }

    public function MakeWinner($game_id, $win_amount, $user_id, $comission)
    {
        $admin_winning_amt = round($win_amount * round($comission/100, 2), 2);
        $user_winning_amt = round($win_amount - $admin_winning_amt, 2);

        $this->db->set('winner_id', $user_id);
        $this->db->set('user_winning_amt', $user_winning_amt);
        $this->db->set('admin_winning_amt', $admin_winning_amt);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $game_id);
        $this->db->update('tbl_game');


        $this->db->set('wallet', 'wallet+' . $user_winning_amt, false);
        $this->db->set('winning_wallet', 'winning_wallet+' . $user_winning_amt, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');
        // return true;


        $this->db->set('admin_coin', 'admin_coin+' . $admin_winning_amt, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->update('tbl_admin');
        return true;
    }

    public function Chaal($game_id, $amount, $user_id)
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

        $this->db->set('amount', 'amount+' . $amount, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_game');

        $this->db->select('seen');
        $this->db->from('tbl_game_card');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        $seen = $Query->row()->seen;

        $data = [
            'user_id' => $user_id,
            'game_id' => $game_id,
            'seen' => $seen,
            'action' => 2,
            'amount' => $amount,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_game_log', $data);

        return true;
    }

    public function isCardSeen($game_id, $user_id)
    {
        $this->db->select('seen');
        $this->db->from('tbl_game_card');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        return ($Query->num_rows()) ? $Query->row()->seen : 0;
    }

    public function Show($game_id, $amount, $user_id)
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

        $this->db->set('amount', 'amount+' . $amount, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_game');

        $this->db->select('seen');
        $this->db->from('tbl_game_card');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        $seen = $Query->row()->seen;

        $data = [
            'user_id' => $user_id,
            'game_id' => $game_id,
            'seen' => $seen,
            'action' => 3,
            'amount' => $amount,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_game_log', $data);

        return true;
    }

    public function SlideShow($game_id, $user_id, $prev_id)
    {
        $data = [
            'user_id' => $user_id,
            'prev_id' => $prev_id,
            'game_id' => $game_id,
            'status' => 0,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_slide_show', $data);

        return $this->db->insert_id();
    }

    public function GetSlideShow($game_id)
    {
        $this->db->select('tbl_slide_show.*,tbl_users.name');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_slide_show.user_id');
        $this->db->where('tbl_slide_show.game_id', $game_id);
        $this->db->where('tbl_slide_show.status', 0);
        $query = $this->db->get('tbl_slide_show');
        return $query->result_array();
    }

    public function GetSlideShowById($slide_id)
    {
        $this->db->where('id', $slide_id);
        $this->db->where('status', 0);
        $query = $this->db->get('tbl_slide_show');
        return $query->row();
    }

    public function UpdateSlideShow($id, $status)
    {
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $this->db->update('tbl_slide_show');

        return $this->db->affected_rows();
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

    public function AddGameCount($user_id)
    {
        $this->db->set('game_played', 'game_played+1', false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        return $this->db->affected_rows();
    }

    public function GiveGameCards($data)
    {
        $this->db->insert('tbl_game_card', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function AddGameLog($data)
    {
        $this->db->insert('tbl_game_log', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function Update($data, $game_id)
    {
        $this->db->where('id', $game_id);
        $this->db->update('tbl_game', $data);
        $GameId =  $this->db->affected_rows();
        // echo $this->db->last_query();
        return $GameId;
    }

    public function View($id)
    {
        $this->db->select('tbl_game.*');
        $this->db->from('tbl_game');
        $this->db->where('isDeleted', false);
        $this->db->where('tbl_game.id', $id);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->row();
    }

    public function Delete($id)
    {
        $return = false;
        $this->db->set('isDeleted', true); //value that used to update column
        $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_game');  //table name

        return $return;
    }

    public function DeleteTable($id)
    {
        $return = false;
        $this->db->set('isDeleted', true); //value that used to update column
        $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_table');  //table name

        $this->db->set('table_id', 0); //value that used to update column
        $this->db->where('table_id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_users');  //table name

        return $return;
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

    public function getPotWinnerPosition($user1, $user2)
    {
        $winner = '';

        if ($user1[0] == $user2[0]) {
            switch ($user1[0]) {
                case 6:
                    $winner = ($user1[1] > $user2[1]) ? 0 : 1;
                    break;

                case 5:
                case 4:
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
                            $winner = 0;
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

    public function Leaderboard()
    {
        $Query = $this->db->select('SUM(tbl_game.amount) as Total_Win,tbl_game.winner_id,tbl_users.name,tbl_users.profile_pic')
            ->from('tbl_game')
            ->join('tbl_users', 'tbl_users.id=tbl_game.winner_id')
            ->where('tbl_game.winner_id!=', 0)
            ->group_by('tbl_game.winner_id')
            ->order_by('SUM(tbl_game.amount)', 'desc')
            ->limit(50)
            ->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
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

    public function ChangeCard($game_id, $User_id, $Position, $Card)
    {
        $data = [
            $Position => $Card
        ];
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $User_id);
        $Update = $this->db->update('tbl_game_card', $data);
        if ($Update) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function AllGames()
    {
        $this->db->select('tbl_game.*,tbl_users.name');
        $this->db->from('tbl_game');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_game.winner_id', 'left');
        $this->db->order_by('tbl_game.id', 'DESC');
        $this->db->limit(10);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function Comission()
    {
        $this->db->from('tbl_game');
        // $this->db->where('isDeleted', false);
        $this->db->where('amount>', 0);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }
}