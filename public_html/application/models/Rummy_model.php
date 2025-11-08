<?php

class Rummy_model extends MY_Model
{
    public function getActiveTable()
    {
        $this->db->select('tbl_users.rummy_table_id,COUNT(tbl_users.id) AS members,tbl_rummy_table.private,tbl_rummy_table.boot_value');
        $this->db->from('tbl_users');
        $this->db->join('tbl_rummy_table', 'tbl_users.rummy_table_id=tbl_rummy_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        // $this->db->where('tbl_rummy_table.private', false);
        $this->db->where('tbl_users.rummy_table_id!=', 0);
        $this->db->group_by('tbl_users.rummy_table_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function getPublicActiveTable()
    {
        $this->db->select('tbl_users.rummy_table_id,COUNT(tbl_users.id) AS members');
        $this->db->from('tbl_users');
        $this->db->join('tbl_rummy_table', 'tbl_users.rummy_table_id=tbl_rummy_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->where('tbl_rummy_table.private', false);
        $this->db->where('tbl_users.rummy_table_id!=', 0);
        $this->db->group_by('tbl_users.rummy_table_id');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function getTableMaster($boot_value='')
    {
        $this->db->select('tbl_rummy_table_master.*,COUNT(tbl_users.id) AS online_members');
        $this->db->from('tbl_rummy_table_master');
        $this->db->join('tbl_rummy_table', 'tbl_rummy_table_master.boot_value=tbl_rummy_table.boot_value AND tbl_rummy_table.isDeleted=0', 'left');
        $this->db->join('tbl_users', 'tbl_users.rummy_table_id=tbl_rummy_table.id AND tbl_users.isDeleted=0', 'left');
        // $this->db->where('', false);
        // $this->db->where('tbl_users.table_id!=', 0);
        if (!empty($boot_value)) {
            $this->db->where('tbl_rummy_table_master.boot_value', $boot_value);
        }
        $this->db->where('tbl_rummy_table_master.isDeleted', 0);
        $this->db->group_by('tbl_rummy_table_master.boot_value');
        $this->db->order_by('tbl_rummy_table_master.boot_value');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function getCustomizeActiveTable($boot_value)
    {
        $this->db->select('tbl_users.rummy_table_id,COUNT(tbl_users.id) AS members');
        $this->db->from('tbl_users');
        $this->db->join('tbl_rummy_table', 'tbl_users.rummy_table_id=tbl_rummy_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        // $this->db->where('tbl_table.private', 2);
        $this->db->where('tbl_rummy_table.boot_value', $boot_value);
        $this->db->where('tbl_users.rummy_table_id!=', 0);
        $this->db->group_by('tbl_users.rummy_table_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function isTable($TableId)
    {
        $this->db->select('rummy_table_id');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('rummy_table_id', $TableId);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function isTableAvail($TableId)
    {
        $this->db->from('tbl_rummy_table');
        $this->db->where('isDeleted', false);
        $this->db->where('id', $TableId);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function GetSeatOnTable($TableId)
    {
        $sql = "SELECT * FROM ( SELECT 1 AS mycolumn UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 ) a WHERE mycolumn NOT in ( SELECT seat_position FROM `tbl_rummy_table_user` WHERE table_id=" . $TableId . " AND isDeleted=0 ) LIMIT 1";
        $Query = $this->db->query($sql, false);
        // echo $this->db->last_query();
        if ($Query->row()) {
            return $Query->row()->mycolumn;
        } else {
            return false;
        }
    }

    public function TableUser($TableId)
    {
        $this->db->select('tbl_rummy_table_user.*,tbl_users.name,tbl_users.mobile,tbl_users.profile_pic,tbl_users.wallet,tbl_users.user_type');
        $this->db->from('tbl_rummy_table_user');
        $this->db->join('tbl_users', 'tbl_rummy_table_user.user_id=tbl_users.id');
        $this->db->where('tbl_rummy_table_user.isDeleted', false);
        $this->db->where('tbl_rummy_table_user.table_id', $TableId);
        $this->db->order_by('tbl_rummy_table_user.seat_position', 'asc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameUser($game_id)
    {
        $this->db->from('tbl_rummy_card');
        $this->db->where('packed', false);
        $this->db->where('game_id', $game_id);
        $this->db->group_by('user_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameUserCard($game_id, $user_id)
    {
        $this->db->from('tbl_rummy_card');
        $this->db->where('packed', false);
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        return $Query->row();
    }

    public function getGameBot($game_id)
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->join('tbl_rummy_card', 'tbl_rummy_card.user_id=tbl_users.id');
        $this->db->where('tbl_users.mobile', "");
        $this->db->where('tbl_rummy_card.packed', false);
        $this->db->where('tbl_rummy_card.game_id', $game_id);
        $Query = $this->db->get();
        return $Query->row()->id;
    }

    public function isLeaveTable($user_id)
    {
        $return = false;
        $this->db->from('tbl_rummy_log');
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
        $this->db->select('tbl_rummy_card.*,tbl_users.name,tbl_users.user_type');
        $this->db->from('tbl_rummy_card');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_rummy_card.user_id');
        $this->db->where('tbl_rummy_card.game_id', $game_id);
        $this->db->group_by('tbl_rummy_card.user_id');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function GameOnlyUser($game_id)
    {
        $this->db->select('tbl_rummy_card.user_id,tbl_rummy_card.packed,tbl_users.name');
        $this->db->from('tbl_rummy_card');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_rummy_card.user_id');
        $this->db->where('tbl_rummy_card.game_id', $game_id);
        $this->db->group_by('tbl_rummy_card.user_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameLog($game_id, $limit = '', $status = '', $user_id = '', $timeout = '')
    {
        $this->db->from('tbl_rummy_log');
        $this->db->where('game_id', $game_id);
        if (!empty($status)) {
            $this->db->where('action', $status);
        }
        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }
        if (!empty($timeout)) {
            $this->db->where('timeout', $timeout);
        }
        $this->db->order_by('id', 'DESC');
        if (!empty($limit)) {
            $this->db->limit($limit);
        }
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameLogJson($game_id, $user_id)
    {
        $this->db->from('tbl_rummy_log');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $Query = $this->db->get();
        $data = $Query->row();
        if ($data) {
            return $data->json;
        } else {
            return  [];
        }
    }

    public function LastChaal($game_id)
    {
        $this->db->from('tbl_rummy_log');
        $this->db->where('game_id', $game_id);
        $this->db->where_in('action', [0, 2]);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function ChaalCount($game_id, $user_id)
    {
        $this->db->from('tbl_rummy_log');
        $this->db->where('game_id', $game_id);
        $this->db->where('action', 2);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->num_rows();
    }

    public function getActiveGameOnTable($TableId)
    {
        $this->db->from('tbl_rummy');
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
        $this->db->from('tbl_rummy');
        $this->db->where('isDeleted', false);
        $this->db->where('table_id', $TableId);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function getMyCards($game_id, $user_id, $card='')
    {
        // $this->db->set('seen', 1); //value that used to update column
        // $this->db->where('user_id', $user_id); //which row want to upgrade
        // $this->db->where('game_id', $game_id); //which row want to upgrade
        // $this->db->update('tbl_rummy_card');  //table name

        $this->db->select('id,card,SUBSTRING(card, 1, 2) as card_group', false);
        $this->db->from('tbl_rummy_card');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('isDeleted', false);

        if (!empty($card)) {
            $this->db->where('card', $card);
        }
        // $this->db->order_by('card_group', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GetGameCard($game_id)
    {
        $this->db->select('cards');
        $this->db->from('tbl_cards_rummy');
        $this->db->where('`cards` NOT IN (SELECT `joker` FROM `tbl_rummy` WHERE `id`='.$game_id.')', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_card` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_card_drop` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->order_by('RAND()');
        $Query = $this->db->get();
        echo $this->db->last_query();
        exit;
        return $Query->result();
    }

    public function SwapCards($user_id, $game_id, $my_card, $new_card)
    {
        $where = ['game_id' => $game_id,'card' => $my_card, 'user_id' => $user_id];
        $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
        $this->db->update('tbl_rummy_card', $data, $where);

        $table_user_data = [
            'game_id' => $game_id,
            'user_id' => $user_id,
            'card' => $new_card,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s'),
            'isDeleted' => 0
        ];

        $this->db->insert('tbl_rummy_card', $table_user_data);
        $TableId =  $this->db->insert_id();
        // echo $this->db->last_query();
        // exit;
        return $TableId;
    }

    public function GetStartCards($limit)
    {
        $this->db->from('tbl_cards_rummy');
        $this->db->where('cards!=', 'JKR1');
        $this->db->where('cards!=', 'JKR2');
        $this->db->order_by('RAND()');
        $this->db->limit($limit);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GetCards($limit)
    {
        $this->db->from('tbl_cards_rummy');
        $this->db->order_by('RAND()');
        $this->db->limit($limit);
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
        $this->db->insert('tbl_rummy', $data);
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
        $this->db->insert('tbl_rummy_table', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function AddTableUser($data)
    {
        $this->db->insert('tbl_rummy_table_user', $data);
        $TableId =  $this->db->insert_id();

        $this->db->set('rummy_table_id', $data['table_id']); //value that used to update column
        $this->db->where('id', $data['user_id']); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $TableId;
    }

    public function RemoveTableUser($data)
    {
        $this->db->set('isDeleted', 1); //value that used to update column
        $this->db->where('user_id', $data['user_id']); //which row want to upgrade
        $this->db->where('table_id', $data['table_id']); //which row want to upgrade
        $this->db->update('tbl_rummy_table_user');  //table name

        $this->db->set('rummy_table_id', 0); //value that used to update column
        $this->db->where('id', $data['user_id']); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return true;
    }

    public function PackGame($user_id, $game_id, $timeout = 0, $json = '', $amount = 0, $percent = 0)
    {
        $this->db->set('packed', 1); //value that used to update column
        $this->db->where('user_id', $user_id); //which row want to upgrade
        $this->db->where('game_id', $game_id); //which row want to upgrade
        $this->db->update('tbl_rummy_card');  //table name

        $this->db->set('amount', 'amount+' . $amount, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy');

        $points = round(($percent / 100) * MAX_POINTS);
        $seen = 0;
        $data = [
            'user_id' => $user_id,
            'game_id' => $game_id,
            'seen' => $seen,
            'json' => $json,
            'points' => $points,
            'amount' => -$amount,
            'timeout' => (isset($timeout)) ? $timeout : 0,
            'action' => 1,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_rummy_log', $data);
        return true;
    }

    public function MakeWinner($game_id, $win_amount, $user_id, $comission)
    {
        $admin_winning_amt = round($win_amount * round($comission/100, 2), 2);
        $user_winning_amt = round($win_amount - $admin_winning_amt, 2);
        $this->db->set('wallet', 'wallet+' . $user_winning_amt, false);
        $this->db->set('winning_wallet', 'winning_wallet+' . $user_winning_amt, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');
        // echo $this->db->affected_rows();
        // echo $this->db->last_query();
        // exit;

        $this->db->set('winner_id', $user_id);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->set('user_winning_amt', $user_winning_amt);
        $this->db->set('admin_winning_amt', $admin_winning_amt);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy');
        // return true;
        // $amount = ($win_amount * 0.98);


        // $amount = ($win_amount * 0.02);
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

        $this->db->set('winning_wallet', 'winning_wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->where('winning_wallet>', 0);
        $this->db->update('tbl_users');

        $this->db->set('amount', 'amount+' . $amount, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy');

        $this->db->select('seen');
        $this->db->from('tbl_rummy_card');
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
        $this->db->insert('tbl_rummy_log', $data);

        return true;
    }

    public function Show($game_id, $amount, $user_id)
    {
        $this->db->set('wallet', 'wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->set('winning_wallet', 'winning_wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->where('winning_wallet>', 0);
        $this->db->update('tbl_users');

        $this->db->set('amount', 'amount+' . $amount, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy');

        $this->db->select('seen');
        $this->db->from('tbl_rummy_card');
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
        $this->db->insert('tbl_rummy_log', $data);

        return true;
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
        $this->db->insert('tbl_rummy_card', $data);
        $TableId =  $this->db->insert_id();
        // echo $this->db->last_query();
        return $TableId;
    }

    public function DropGameCards($where, $json='', $timeout=0)
    {
        $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
        $this->db->update('tbl_rummy_card', $data, $where);
        $where['added_date'] = date('Y-m-d H:i:s');
        $where['updated_date'] = date('Y-m-d H:i:s');
        $this->db->insert('tbl_rummy_card_drop', $where);
        $inserted_id =  $this->db->insert_id();

        $log_data = [
            'user_id' => $where['user_id'],
            'game_id' => $where['game_id'],
            'json' => $json,
            'timeout' => $timeout,
            'action' => 2,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->AddGameLog($log_data);

        return $inserted_id;
    }

    public function Declare($data)
    {
        // $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
        // $this->db->update('tbl_rummy_card', $data, $where);
        // $where['added_date'] = date('Y-m-d H:i:s');
        // $where['updated_date'] = date('Y-m-d H:i:s');
        // $this->db->insert('tbl_rummy_card_drop', $where);
        // $inserted_id =  $this->db->insert_id();

        $this->db->set('amount', 'amount+' . $data['actual_points'], false);
        $this->db->where('id', $data['game_id']);
        $this->db->update('tbl_rummy');

        $log_data = [
            'user_id' => $data['user_id'],
            'game_id' => $data['game_id'],
            'points' => $data['points'],
            'amount' => -$data['actual_points'],
            'action' => 3,
            'json' => $data['json'],
            'added_date' => date('Y-m-d H:i:s')
        ];
        $inserted_id = $this->AddGameLog($log_data);

        return $inserted_id;
    }

    public function AddGameLog($data)
    {
        $this->db->insert('tbl_rummy_log', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function Update($data, $game_id)
    {
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy', $data);
        $GameId =  $this->db->affected_rows();
        // echo $this->db->last_query();
        return $GameId;
    }

    public function View($id)
    {
        $this->db->select('tbl_rummy.*');
        $this->db->from('tbl_rummy');
        $this->db->where('isDeleted', false);
        $this->db->where('tbl_rummy.id', $id);

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
        $return = $this->db->update('tbl_rummy');  //table name

        return $return;
    }

    public function DeleteTable($id)
    {
        $return = false;
        $this->db->set('isDeleted', true); //value that used to update column
        $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_rummy_table');  //table name

        $this->db->set('rummy_table_id', 0); //value that used to update column
        $this->db->where('rummy_table_id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_users');  //table name

        return $return;
    }

    public function CardValue($joker, $card1, $card2, $card3, $card4='', $card5='', $card6='', $card7='', $card8='', $card9='')
    {
        $rule = 0;
        $value = 0;
        $value2 = 0;
        $value3 = 0;

        // $joker_color = substr($joker, 0, 2);
        $joker_num = substr(trim($joker, '_'), 2);

        $card1_color = substr($card1, 0, 2);
        $card1_num = substr($card1, 2);
        $card1_num_set = $card1_num;
        $card1_color_set = $card1_color;

        $card2_color = substr($card2, 0, 2);
        $card2_num = substr($card2, 2);
        $card2_num_set = $card2_num;
        $card2_color_set = $card2_color;

        $card3_color = substr($card3, 0, 2);
        $card3_num = substr($card3, 2);
        $card3_num_set = $card3_num;
        $card3_color_set = $card3_color;

        if (!empty($card4)) {
            $card4_color = substr($card4, 0, 2);
            $card4_num = substr($card4, 2);
            $card4_num_set = $card4_num;
            $card4_color_set = $card4_color;
        }
        if (!empty($card5)) {
            $card5_color = substr($card5, 0, 2);
            $card5_num = substr($card5, 2);
        }
        if (!empty($card6)) {
            $card6_color = substr($card6, 0, 2);
            $card6_num = substr($card6, 2);
        }

        // Check Wild Joker And Convert To Joker Card
        if ($card1_num==$joker_num) {
            $card1_color = 'JK';
            $card1_num = 0;
            $card1_num_set = $card1_num;
        }
        if ($card2_num==$joker_num) {
            $card2_color = 'JK';
            $card2_num = 0;
            $card2_num_set = $card2_num;
        }
        if ($card3_num==$joker_num) {
            $card3_color = 'JK';
            $card3_num = 0;
            $card3_num_set = $card3_num;
        }
        if (isset($card4_num) && $card4_num==$joker_num) {
            $card4_color = 'JK';
            $card4_num = 0;
            $card4_num_set = $card4_num;
        }
        if (isset($card5_num) && $card5_num==$joker_num) {
            $card5_color = 'JK';
            $card5_num = 0;
        }
        if (isset($card6_num) && $card6_num==$joker_num) {
            $card6_color = 'JK';
            $card6_num = 0;
        }
        // END Check Wild Joker And Convert To Joker Card

        // $set = ($card1_color!='JK')?$card1_num_set:(($card2_color!='JK')?$card2_num_set:$card3_num_set);
        // Color Group Code
        if ($card1_color!='JK') {
            $set = $card1_num_set;
            $color_group = $card1_color;
        } elseif ($card2_color!='JK') {
            $set = $card2_num_set;
            $color_group = $card2_color;
        } elseif ($card3_color!='JK') {
            $set = $card3_num_set;
            $color_group = $card3_color;
        } elseif (isset($card4_color) && $card4_color!='JK') {
            $set = $card4_num_set;
            $color_group = $card4_color;
        } elseif (isset($card5_color) && $card5_color!='JK') {
            $color_group = $card5_color;
        } else {
            $color_group = $card6_color;
        }
        //END Color Group Code

        // Convert Joker to Vurtual Card
        $joker_count = 0;
        if ($card1_color=='JK') {
            $card1_num_set = $set;
            $card1_color = $color_group;
            $card1_color_set = '';
            $joker_count++;
        }
        if ($card2_color=='JK') {
            $card2_num_set = $set;
            $card2_color = $color_group;
            $card2_color_set = '';
            $joker_count++;
        }
        if ($card3_color=='JK') {
            $card3_num_set = $set;
            $card3_color = $color_group;
            $card3_color_set = '';
            $joker_count++;
        }
        if (isset($card4_color) && $card4_color=='JK') {
            $card4_num_set = $set;
            $card4_color = $color_group;
            $card4_color_set = '';
            $joker_count++;
        }
        if (isset($card5_color) && $card5_color=='JK') {
            $card5_color = $color_group;
            $joker_count++;
        }
        if (isset($card6_color) && $card6_color=='JK') {
            $card6_color = $color_group;
            $joker_count++;
        }
        //END Convert Joker to Vurtual Card

        if (isset($card4_num_set) && ($card1_num_set == $card2_num_set) && ($card2_num_set == $card3_num_set) && ($card3_num_set == $card4_num_set)) {
            if (empty($card5)) {
                $set = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $set
                );

                if (($card1_color_set == $card2_color_set && ($card1_color_set!='' || $card2_color_set!='')) || ($card2_color_set == $card3_color_set && ($card2_color_set!='' || $card3_color_set!='')) ||
                ($card3_color_set == $card4_color_set && ($card3_color_set!='' || $card4_color_set!='')) ||
                ($card1_color_set == $card4_color_set && ($card1_color_set!='' || $card4_color_set!=''))) {
                    $rule = 0;
                    $value = 0;
                } else {
                    $set = (int) $set;
                    $rule = 6;
                    $value = $set;
                }
            }
        } elseif (($card1_num_set == $card2_num_set) && ($card2_num_set == $card3_num_set)) {
            if (empty($card4)) {
                $set = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $set
                );
                if (($card1_color_set == $card2_color_set && ($card1_color_set!='' || $card2_color_set!='')) || ($card2_color_set == $card3_color_set && ($card2_color_set!='' || $card3_color_set!='')) ||
                ($card1_color_set == $card3_color_set && ($card1_color_set!='' || $card3_color_set!=''))) {
                    $rule = 0;
                    $value = 0;
                } else {
                    $set = (int) $set;
                    $rule = 6;
                    $value = $set;
                }
            }
        } else {
            $color = false;

            if (($card1_color == $card2_color) && ($card2_color == $card3_color)) {
                if (isset($card6_color) && $card5_color!=$card6_color) {
                    return array($rule, $value);
                } elseif (isset($card5_color) && $card4_color!=$card5_color) {
                    return array($rule, $value);
                } elseif (isset($card4_color) && $card3_color!=$card4_color) {
                    return array($rule, $value);
                }
                $color = true;
            } else {
                return array($rule, $value);
            }

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

            if (isset($card4_num)) {
                $card4_num = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $card4_num
                );
                $card4_num = (int) $card4_num;
            }

            if (isset($card5_num)) {
                $card5_num = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $card5_num
                );
                $card5_num = (int) $card5_num;
            }

            if (isset($card6_num)) {
                $card6_num = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $card6_num
                );
                $card6_num = (int) $card6_num;
            }

            if (isset($card6_num)) {
                $arr = [$card1_num, $card2_num, $card3_num, $card4_num, $card5_num, $card6_num];
            } elseif (isset($card5_num)) {
                $arr = [$card1_num, $card2_num, $card3_num, $card4_num, $card5_num];
            } elseif (isset($card4_num)) {
                $arr = [$card1_num, $card2_num, $card3_num, $card4_num];
            } else {
                $arr = [$card1_num, $card2_num, $card3_num];
            }
            sort($arr);
            // print_r($arr);

            $sequence = false;
            $ace_joker_count = $joker_count;
            // echo $joker_count;
            $total_card = count($arr);
            foreach ($arr as $key => $val) {
                // echo $val;
                if ($val!=0 && $total_card>($key+1)) {
                    if (($val+1)==$arr[$key+1]) {
                        // echo $arr[$key+1];
                        $sequence = true;
                    } elseif (($val+2)==$arr[$key+1] && $joker_count>0) {
                        $joker_count--;
                        $sequence = true;
                    } else {
                        $sequence = false;
                        break;
                    }
                }
            }

            if ($sequence && $color) {
                $value = $arr[0];
                $rule = ($value==0) ? 4 : 5;
            }

            if ($rule==0) {
                if (in_array(14, $arr)) {
                    $arr = str_replace(14, 1, $arr);
                    sort($arr);
                    // print_r($arr);
                    $total_card = count($arr);
                    foreach ($arr as $key => $val) {
                        // echo $val;
                        if ($val!=0 && $total_card>($key+1)) {
                            if (($val+1)==$arr[$key+1]) {
                                // echo $arr[$key+1];
                                $sequence = true;
                            } elseif (($val+2)==$arr[$key+1] && $ace_joker_count>0) {
                                $ace_joker_count--;
                                $sequence = true;
                            } else {
                                $sequence = false;
                                break;
                            }
                        }
                    }
                }

                if ($sequence && $color) {
                    $value = $arr[0];
                    $rule = ($value==0) ? 4 : 5;
                }
            }
        }
        return array($rule, $value);
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

    public function Leaderboard()
    {
        $Query = $this->db->select('SUM(tbl_rummy.amount) as Total_Win,tbl_rummy.winner_id,tbl_users.name,tbl_users.profile_pic')
            ->from('tbl_rummy')
            ->join('tbl_users', 'tbl_users.id=tbl_rummy.winner_id')
            ->where('tbl_rummy.winner_id!=', 0)
            ->group_by('tbl_rummy.winner_id')
            ->order_by('SUM(tbl_rummy.amount)', 'desc')
            ->limit(50)
            ->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function AllCards()
    {
        $Query = $this->db->select('cards')
            ->from('tbl_cards_rummy')
            ->get();
        return $Query->result();
    }

    public function GameCard($game_id)
    {
        $this->db->select('card');
        $this->db->from('tbl_rummy_card');
        $this->db->where('game_id', $game_id);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function GetRamdomGameCard($game_id)
    {
        $this->db->select('cards');
        $this->db->from('tbl_cards_rummy');
        $this->db->where('`cards` NOT IN (SELECT `joker` FROM `tbl_rummy` WHERE `id`='.$game_id.')', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_card` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_card_drop` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->order_by('RAND()');
        $this->db->limit(1);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function GetGameDropCard($game_id)
    {
        $this->db->select('card');
        $this->db->from('tbl_rummy_card_drop');
        $this->db->where('game_id', $game_id);
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function GetAndDeleteGameDropCard($game_id)
    {
        $this->db->select('id,card');
        $this->db->from('tbl_rummy_card_drop');
        $this->db->where('isDeleted', false);
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        $result = $Query->result();

        if ($result) {
            $where = ['id' => $result[0]->id];
            $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
            $this->db->update('tbl_rummy_card_drop', $data, $where);
        }
        // echo $this->db->last_query();
        // exit;
        return $result;
    }

    public function ChangeCard($game_id, $User_id, $Position, $Card)
    {
        $data = [
            $Position => $Card
        ];
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $User_id);
        $Update = $this->db->update('tbl_rummy_card', $data);
        if ($Update) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function AllGames()
    {
        $this->db->select('tbl_rummy.*,tbl_users.name');
        $this->db->from('tbl_rummy');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_rummy.winner_id', 'left');
        $this->db->order_by('tbl_rummy.id', 'DESC');
        $this->db->limit(10);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function Comission()
    {
        $this->db->select('tbl_rummy.*');
        $this->db->from('tbl_rummy');
        // $this->db->where('isDeleted', false);
        $this->db->where('winning_amount>', 0);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function LastGameCard($game_id)
    {
        $this->db->from('tbl_rummy_card');
        $this->db->where('packed', false);
        $this->db->where('game_id', $game_id);
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        return $Query->row();
    }

    public function StartDropGameCards($where)
    {
        $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
        $this->db->update('tbl_rummy_card', $data, $where);
        $where['added_date'] = date('Y-m-d H:i:s');
        $where['updated_date'] = date('Y-m-d H:i:s');
        $this->db->insert('tbl_rummy_card_drop', $where);
        $inserted_id =  $this->db->insert_id();

        return $inserted_id;
    }
}