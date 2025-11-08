<?php

class RummyDeal_model extends MY_Model
{
    public function getTableMaster($boot_value='')
    {
        $this->db->select('tbl_rummy_deal_table_master.*,COUNT(tbl_users.id) AS online_members');
        $this->db->from('tbl_rummy_deal_table_master');
        $this->db->join('tbl_rummy_deal_table', 'tbl_rummy_deal_table_master.boot_value=tbl_rummy_deal_table.boot_value AND tbl_rummy_deal_table.isDeleted=0', 'left');
        $this->db->join('tbl_users', 'tbl_users.rummy_deal_table_id=tbl_rummy_deal_table.id AND tbl_users.isDeleted=0', 'left');
        // $this->db->where('', false);
        // $this->db->where('tbl_users.table_id!=', 0);
        if (!empty($boot_value)) {
            $this->db->where('tbl_rummy_deal_table_master.boot_value', $boot_value);
        }
        $this->db->where('tbl_rummy_deal_table_master.isDeleted', 0);
        $this->db->group_by('tbl_rummy_deal_table_master.boot_value');
        $this->db->order_by('tbl_rummy_deal_table_master.boot_value');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function getActiveTable()
    {
        $this->db->select('tbl_users.rummy_deal_table_id,COUNT(tbl_users.id) AS members,tbl_rummy_deal_table.private,tbl_rummy_deal_table.boot_value');
        $this->db->from('tbl_users');
        $this->db->join('tbl_rummy_deal_table', 'tbl_users.rummy_deal_table_id=tbl_rummy_deal_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        // $this->db->where('tbl_rummy_deal_table.private', false);
        $this->db->where('tbl_users.rummy_deal_table_id!=', 0);
        $this->db->group_by('tbl_users.rummy_deal_table_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function getPublicActiveTable()
    {
        $this->db->select('tbl_rummy_deal_table.*,COUNT(tbl_users.id) AS online_members');
        $this->db->from('tbl_rummy_deal_table');
        $this->db->join('tbl_users', 'tbl_users.rummy_deal_table_id=tbl_rummy_deal_table.id AND tbl_users.isDeleted=0', 'LEFT');
        // $this->db->where('tbl_rummy_deal_table.private', false);
        $this->db->where('tbl_rummy_deal_table.winning_amount', 0);
        $this->db->where('tbl_rummy_deal_table.boot_value!=', 0);
        $this->db->group_by('tbl_users.rummy_deal_table_id');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function isTable($TableId)
    {
        $this->db->select('rummy_deal_table_id');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('rummy_deal_table_id', $TableId);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function isTableAvail($TableId='', $invitation_code='')
    {
        $this->db->from('tbl_rummy_deal_table');
        $this->db->where('isDeleted', false);
        if (!empty($TableId)) {
            $this->db->where('id', $TableId);
        }
        if (!empty($invitation_code)) {
            $this->db->where('invitation_code', $invitation_code);
        }
        $Query = $this->db->get();
        return $Query->row();
    }

    public function GetSeatOnTable($TableId)
    {
        $sql = "SELECT * FROM ( SELECT 1 AS mycolumn UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 ) a WHERE mycolumn NOT in ( SELECT seat_position FROM `tbl_rummy_deal_table_user` WHERE table_id=" . $TableId . " AND isDeleted=0 ) LIMIT 1";
        $Query = $this->db->query($sql, false);
        return $Query->row()->mycolumn;
    }

    public function TableUser($TableId)
    {
        $this->db->select('tbl_rummy_deal_table_user.*,tbl_users.name,tbl_users.mobile,tbl_users.profile_pic,tbl_users.wallet,tbl_users.user_type,tbl_users.fcm');
        $this->db->from('tbl_rummy_deal_table_user');
        $this->db->join('tbl_users', 'tbl_rummy_deal_table_user.user_id=tbl_users.id');
        $this->db->where('tbl_rummy_deal_table_user.isDeleted', false);
        $this->db->where('tbl_rummy_deal_table_user.table_id', $TableId);
        $this->db->order_by('tbl_rummy_deal_table_user.seat_position', 'asc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function TableUserCardPosition($TableId)
    {
        $this->db->select('tbl_rummy_deal_table_user.*,tbl_users.name,tbl_users.mobile,tbl_users.profile_pic,tbl_users.wallet,tbl_users.user_type');
        $this->db->from('tbl_rummy_deal_table_user');
        $this->db->join('tbl_users', 'tbl_rummy_deal_table_user.user_id=tbl_users.id');
        $this->db->where('tbl_rummy_deal_table_user.isDeleted', false);
        $this->db->where('tbl_rummy_deal_table_user.table_id', $TableId);
        $this->db->order_by('tbl_rummy_deal_table_user.card_position', 'DESC');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameUser($game_id)
    {
        $this->db->from('tbl_rummy_deal_card');
        $this->db->where('packed', false);
        $this->db->where('game_id', $game_id);
        $this->db->group_by('user_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameUserCard($game_id, $user_id)
    {
        $this->db->from('tbl_rummy_deal_card');
        $this->db->where('packed', false);
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->row();
    }

    public function getGameBot($game_id)
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->join('tbl_rummy_deal_card', 'tbl_rummy_deal_card.user_id=tbl_users.id');
        $this->db->where('tbl_users.mobile', "");
        $this->db->where('tbl_rummy_deal_card.packed', false);
        $this->db->where('tbl_rummy_deal_card.game_id', $game_id);
        $Query = $this->db->get();
        return $Query->row()->id;
    }

    public function isLeaveTable($user_id)
    {
        $return = false;
        $this->db->from('tbl_rummy_deal_log');
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
        $this->db->select('tbl_rummy_deal_card.*,tbl_users.name,tbl_users.user_type');
        $this->db->from('tbl_rummy_deal_card');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_rummy_deal_card.user_id');
        $this->db->where('tbl_rummy_deal_card.game_id', $game_id);
        $this->db->group_by('tbl_rummy_deal_card.user_id');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function GameOnlyUser($game_id)
    {
        $this->db->select('tbl_rummy_deal_card.user_id,tbl_rummy_deal_card.packed,tbl_users.name');
        $this->db->from('tbl_rummy_deal_card');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_rummy_deal_card.user_id');
        $this->db->where('tbl_rummy_deal_card.game_id', $game_id);
        $this->db->group_by('tbl_rummy_deal_card.user_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameLog($game_id, $limit = '', $action = '', $user_id = '', $timeout = '')
    {
        $this->db->from('tbl_rummy_deal_log');
        $this->db->where('game_id', $game_id);
        $this->db->order_by('id', 'DESC');
        if (!empty($action)) {
            $this->db->where('action', $action);
        }
        if (!empty($user_id)) {
            $this->db->where('user_id', $user_id);
        }
        if (!empty($timeout)) {
            $this->db->where('timeout', $timeout);
        }
        if (!empty($limit)) {
            $this->db->limit($limit);
        }
        $Query = $this->db->get();
        return $Query->result();
    }

    public function GameLogJson($game_id, $user_id)
    {
        $this->db->from('tbl_rummy_deal_log');
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
        $this->db->from('tbl_rummy_deal_log');
        $this->db->where('game_id', $game_id);
        $this->db->where_in('action', [0, 2]);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $Query = $this->db->get();
        return $Query->row();
    }

    public function ChaalCount($game_id, $user_id)
    {
        $this->db->from('tbl_rummy_deal_log');
        $this->db->where('game_id', $game_id);
        $this->db->where('action', 2);
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get();
        return $Query->num_rows();
    }

    public function Invested($table_id, $seat_position)
    {
        $Query = $this->db->query("SELECT sum(tbl_rummy_deal_log.amount) as amount,tbl_rummy_deal_table_user.seat_position,tbl_rummy_deal_table_user.user_id FROM `tbl_rummy_deal_table_user` JOIN tbl_rummy_deal ON tbl_rummy_deal_table_user.table_id=tbl_rummy_deal.table_id JOIN tbl_rummy_deal_log ON tbl_rummy_deal.id=tbl_rummy_deal_log.game_id AND tbl_rummy_deal_log.user_id=tbl_rummy_deal_table_user.user_id WHERE tbl_rummy_deal_table_user.table_id='".$table_id."' AND tbl_rummy_deal_table_user.seat_position='".$seat_position."' AND tbl_rummy_deal_log.amount>0");
        return $Query->row();
    }

    public function getActiveGameOnTable($TableId)
    {
        $this->db->from('tbl_rummy_deal');
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
    public function getLastGameOnTable($TableId)
    {
        $this->db->from('tbl_rummy_deal');
        $this->db->where('isDeleted', false);
        $this->db->where('winner_id!=', 0);
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
        $this->db->from('tbl_rummy_deal');
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
        // $this->db->update('tbl_rummy_deal_card');  //table name

        $this->db->select('id,card,SUBSTRING(card, 1, 2) as card_group', false);
        $this->db->from('tbl_rummy_deal_card');
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

    public function GetCards($limit)
    {
        $this->db->from('tbl_cards_rummy');
        $this->db->where('cards!=', 'JKR1');
        $this->db->where('cards!=', 'JKR2');
        $this->db->order_by('RAND()');
        $this->db->limit($limit);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function ChatList($rummy_deal_table_id, $limit='')
    {
        $this->db->select('tbl_chat.*,tbl_users.name,tbl_users.profile_pic');
        $this->db->from('tbl_chat');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_chat.user_id');
        $this->db->where('tbl_users.rummy_deal_table_id', $rummy_deal_table_id);
        $this->db->order_by('tbl_chat.id', 'DESC');
        if (!empty($limit)) {
            $this->db->limit($limit);
        }
        $Query = $this->db->get();
        return $Query->result();
    }

    public function Create($data)
    {
        $this->db->insert('tbl_rummy_deal', $data);
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
        $this->db->insert('tbl_rummy_deal_table', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function getCustomizeActiveTable($boot_value)
    {
        $this->db->select('tbl_users.rummy_deal_table_id,COUNT(tbl_users.id) AS members');
        $this->db->from('tbl_users');
        $this->db->join('tbl_rummy_deal_table', 'tbl_users.rummy_deal_table_id=tbl_rummy_deal_table.id');
        $this->db->where('tbl_users.isDeleted', false);
        // $this->db->where('tbl_table.private', 2);
        $this->db->where('tbl_rummy_deal_table.boot_value', $boot_value);
        $this->db->where('tbl_users.rummy_deal_table_id!=', 0);
        $this->db->group_by('tbl_users.rummy_deal_table_id');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function AddTableUser($data)
    {
        $this->db->insert('tbl_rummy_deal_table_user', $data);
        $TableId =  $this->db->insert_id();

        $this->db->set('rummy_deal_table_id', $data['table_id']); //value that used to update column
        $this->db->where('id', $data['user_id']); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return $TableId;
    }

    public function RemoveTableUser($data)
    {
        $this->db->set('isDeleted', 1); //value that used to update column
        $this->db->where('user_id', $data['user_id']); //which row want to upgrade
        $this->db->where('table_id', $data['table_id']); //which row want to upgrade
        $this->db->update('tbl_rummy_deal_table_user');  //table name

        $this->db->set('rummy_deal_table_id', 0); //value that used to update column
        $this->db->where('id', $data['user_id']); //which row want to upgrade
        $this->db->update('tbl_users');  //table name

        return true;
    }

    public function PackGame($user_id, $game_id, $timeout = 0, $json = '')
    {
        $this->db->set('packed', 1); //value that used to update column
        $this->db->where('user_id', $user_id); //which row want to upgrade
        $this->db->where('game_id', $game_id); //which row want to upgrade
        $this->db->update('tbl_rummy_deal_card');  //table name

        // $this->db->select('seen');
        // $this->db->from('tbl_game_card');
        // $this->db->where('game_id', $game_id);
        // $this->db->where('user_id', $user_id);
        // $Query = $this->db->get();
        // $seen = $Query->row()->seen;
        $seen = 0;

        $data = [
            'user_id' => $user_id,
            'game_id' => $game_id,
            'seen' => $seen,
            'json' => $json,
            'timeout' => (isset($timeout)) ? $timeout : 0,
            'action' => 1,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_rummy_deal_log', $data);
        return true;
    }

    public function MakeWinner($game_id, $amount, $user_id, $admin_winning_amt=0)
    {
        // $this->db->set('wallet', 'wallet+' . $amount, false);
        // $this->db->where('id', $user_id);
        // $this->db->update('tbl_users');
        // echo $this->db->affected_rows();
        // echo $this->db->last_query();
        // exit;

        $this->db->set('winner_id', $user_id);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy_deal');
        // return true;
        // $amount = ($win_amount * 0.98);


        // $amount = ($win_amount * 0.02);
        // $this->db->set('admin_coin', 'admin_coin+' . $admin_winning_amt, false);
        // $this->db->set('updated_date', date('Y-m-d H:i:s'));
        // $this->db->update('tbl_admin');
        return true;
    }

    public function Chaal($game_id, $amount, $user_id)
    {
        $this->db->set('wallet', 'wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->set('amount', 'amount+' . $amount, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy_deal');

        $this->db->select('seen');
        $this->db->from('tbl_rummy_deal_card');
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
        $this->db->insert('tbl_rummy_deal_log', $data);

        return true;
    }

    public function Show($game_id, $amount, $user_id)
    {
        $this->db->set('wallet', 'wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->set('amount', 'amount+' . $amount, false);
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy_deal');

        $this->db->select('seen');
        $this->db->from('tbl_rummy_deal_card');
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
        $this->db->insert('tbl_rummy_deal_log', $data);

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
        $this->db->insert('tbl_rummy_deal_card', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function DropGameCards($where, $json='', $timeout=0)
    {
        $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
        $this->db->update('tbl_rummy_deal_card', $data, $where);
        $where['added_date'] = date('Y-m-d H:i:s');
        $where['updated_date'] = date('Y-m-d H:i:s');
        $this->db->insert('tbl_rummy_deal_card_drop', $where);
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
        // $this->db->update('tbl_rummy_deal_card', $data, $where);
        // $where['added_date'] = date('Y-m-d H:i:s');
        // $where['updated_date'] = date('Y-m-d H:i:s');
        // $this->db->insert('tbl_rummy_deal_card_drop', $where);
        // $inserted_id =  $this->db->insert_id();

        $this->db->set('total_points', 'total_points' . $data['points'], false);
        $this->db->where('table_id', $data['table_id']);
        $this->db->where('user_id', $data['user_id']);
        $this->db->update('tbl_rummy_deal_table_user');
        // echo $this->db->last_query();

        $this->db->select('total_points');
        $this->db->where('table_id', $data['table_id']);
        $this->db->where('user_id', $data['user_id']);
        $total_points = $this->db->get('tbl_rummy_deal_table_user')->row()->total_points;

        $log_data = [
            'user_id' => $data['user_id'],
            'game_id' => $data['game_id'],
            'points' => $data['points'],
            'total_points' => $total_points,
            'action' => 3,
            'json' => $data['json'],
            'added_date' => date('Y-m-d H:i:s')
        ];
        $inserted_id = $this->AddGameLog($log_data);

        return $inserted_id;
    }

    public function rejoin_log($table_id, $user_id)
    {
        $this->db->select('tbl_rummy_deal_log.*');
        $this->db->from('tbl_rummy_deal_log');
        $this->db->join('tbl_rummy_deal', 'tbl_rummy_deal.id=tbl_rummy_deal_log.game_id');
        $this->db->where('tbl_rummy_deal.table_id', $table_id);
        $this->db->where('tbl_rummy_deal_log.user_id', $user_id);
        $this->db->where('tbl_rummy_deal_log.amount!=', 0);
        $this->db->where('tbl_rummy_deal_log.points!=', 0);
        $this->db->order_by('tbl_rummy_deal_log.id', 'DESC');
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function Rejoin($data)
    {
        $this->db->select('total_points');
        $this->db->where('table_id', $data['table_id']);
        $this->db->where('total_points<', 101);
        $this->db->order_by('total_points', 'DESC');
        $this->db->limit(1);
        $total_points = $this->db->get('tbl_rummy_deal_table_user')->row()->total_points;

        $this->db->set('amount', 'amount+'.$data['amount'], false);
        $this->db->where('id', $data['game_id']);
        $this->db->update('tbl_rummy_deal');

        $this->db->set('total_points', $total_points);
        $this->db->where('table_id', $data['table_id']);
        $this->db->where('user_id', $data['user_id']);
        $this->db->update('tbl_rummy_deal_table_user');
        // echo $this->db->last_query();

        $this->db->set('total_points', $total_points);
        $this->db->set('amount', $data['amount']);
        $this->db->where('game_id', $data['game_id']);
        $this->db->where('user_id', $data['user_id']);
        $this->db->where('action', 3);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->update('tbl_rummy_deal_log');
    }

    public function AddGameLog($data)
    {
        $this->db->insert('tbl_rummy_deal_log', $data);
        $TableId =  $this->db->insert_id();

        return $TableId;
    }

    public function getTotalPoints($table_id, $user_id)
    {
        $this->db->select('total_points');
        $this->db->where('table_id', $table_id);
        $this->db->where('user_id', $user_id);
        return $this->db->get('tbl_rummy_deal_table_user')->row()->total_points;
    }

    public function Update($data, $game_id)
    {
        $this->db->where('id', $game_id);
        $this->db->update('tbl_rummy_deal', $data);
        $GameId =  $this->db->affected_rows();
        // echo $this->db->last_query();
        return $GameId;
    }

    public function View($id)
    {
        $this->db->select('tbl_rummy_deal.*');
        $this->db->from('tbl_rummy_deal');
        $this->db->where('isDeleted', false);
        $this->db->where('tbl_rummy_deal.id', $id);

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
        $return = $this->db->update('tbl_rummy_deal');  //table name

        return $return;
    }

    public function DeleteTable($id)
    {
        $return = false;
        $this->db->set('isDeleted', true); //value that used to update column
        $this->db->where('id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_rummy_deal_table');  //table name

        $this->db->set('rummy_deal_table_id', 0); //value that used to update column
        $this->db->where('rummy_deal_table_id', $id); //which row want to upgrade
        $return = $this->db->update('tbl_users');  //table name

        return $return;
    }

    public function CardValue($joker, $card1, $card2, $card3, $card4='', $card5='', $card6='', $card7='', $card8='', $card9='', $card10='')
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
        if (!empty($card7)) {
            $card7_color = substr($card7, 0, 2);
            $card7_num = substr($card7, 2);
        }
        if (!empty($card8)) {
            $card8_color = substr($card8, 0, 2);
            $card8_num = substr($card8, 2);
        }
        if (!empty($card9)) {
            $card9_color = substr($card9, 0, 2);
            $card9_num = substr($card9, 2);
        }
        if (!empty($card10)) {
            $card10_color = substr($card10, 0, 2);
            $card10_num = substr($card10, 2);
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
        if (isset($card7_num) && $card7_num==$joker_num) {
            $card7_color = 'JK';
            $card7_num = 0;
        }
        if (isset($card8_num) && $card8_num==$joker_num) {
            $card8_color = 'JK';
            $card8_num = 0;
        }
        if (isset($card9_num) && $card9_num==$joker_num) {
            $card9_color = 'JK';
            $card9_num = 0;
        }
        if (isset($card10_num) && $card10_num==$joker_num) {
            $card10_color = 'JK';
            $card10_num = 0;
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
        if (isset($card7_color) && $card7_color=='JK') {
            $card7_color = $color_group;
            $joker_count++;
        }
        if (isset($card8_color) && $card8_color=='JK') {
            $card8_color = $color_group;
            $joker_count++;
        }
        if (isset($card9_color) && $card9_color=='JK') {
            $card9_color = $color_group;
            $joker_count++;
        }
        if (isset($card10_color) && $card10_color=='JK') {
            $card10_color = $color_group;
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

            if (isset($card7_num)) {
                $card7_num = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $card7_num
                );
                $card7_num = (int) $card7_num;
            }

            if (isset($card8_num)) {
                $card8_num = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $card8_num
                );
                $card8_num = (int) $card8_num;
            }

            if (isset($card9_num)) {
                $card9_num = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $card9_num
                );
                $card9_num = (int) $card9_num;
            }

            if (isset($card10_num)) {
                $card10_num = str_replace(
                    array("J", "Q", "K", "A"),
                    array(11, 12, 13, 14),
                    $card10_num
                );
                $card10_num = (int) $card10_num;
            }

            if (isset($card10_num)) {
                $arr = [$card1_num, $card2_num, $card3_num, $card4_num, $card5_num, $card6_num, $card7_num, $card8_num, $card9_num, $card10_num];
            } elseif (isset($card9_num)) {
                $arr = [$card1_num, $card2_num, $card3_num, $card4_num, $card5_num, $card6_num, $card7_num, $card8_num, $card9_num];
            } elseif (isset($card8_num)) {
                $arr = [$card1_num, $card2_num, $card3_num, $card4_num, $card5_num, $card6_num, $card7_num, $card8_num];
            } elseif (isset($card7_num)) {
                $arr = [$card1_num, $card2_num, $card3_num, $card4_num, $card5_num, $card6_num, $card7_num];
            } elseif (isset($card6_num)) {
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
        $Query = $this->db->select('SUM(tbl_rummy_deal.amount) as Total_Win,tbl_rummy_deal.winner_id,tbl_users.name,tbl_users.profile_pic')
            ->from('tbl_rummy_deal')
            ->join('tbl_users', 'tbl_users.id=tbl_rummy_deal.winner_id')
            ->where('tbl_rummy_deal.winner_id!=', 0)
            ->group_by('tbl_rummy_deal.winner_id')
            ->order_by('SUM(tbl_rummy_deal.amount)', 'desc')
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
        $this->db->from('tbl_rummy_deal_card');
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
        $this->db->where('`cards` NOT IN (SELECT `joker` FROM `tbl_rummy_deal` WHERE `id`='.$game_id.')', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_deal_card` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_deal_card_drop` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->limit(1);
        $this->db->order_by('RAND()');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function GetGameTableCardCount($game_id)
    {
        $this->db->select('cards');
        $this->db->from('tbl_cards_rummy');
        $this->db->where('`cards` NOT IN (SELECT `joker` FROM `tbl_rummy_deal` WHERE `id`='.$game_id.')', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_deal_card` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_deal_card_drop` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->order_by('RAND()');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return count($Query->result());
    }

    public function deleteDropCard($game_id)
    {
        $where = ['game_id' => $game_id];
        $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
        $this->db->update('tbl_rummy_deal_card_drop', $data, $where);
    }

    public function GetGameDropCard($game_id)
    {
        $this->db->select('card');
        $this->db->from('tbl_rummy_deal_card_drop');
        $this->db->where('game_id', $game_id);
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function GetTablePoints($table_id)
    {
        $this->db->select('tbl_rummy_deal_log.game_id,tbl_rummy_deal_log.user_id,tbl_rummy_deal_log.points,tbl_rummy_deal_log.total_points,tbl_users.name');
        $this->db->from('tbl_rummy_deal_log');
        $this->db->join('tbl_rummy_deal', 'tbl_rummy_deal_log.game_id=tbl_rummy_deal.id');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_rummy_deal_log.user_id');
        $this->db->where('tbl_rummy_deal.table_id', $table_id);
        $this->db->where('tbl_rummy_deal_log.action', 3);
        $this->db->order_by('tbl_rummy_deal_log.id', 'ASC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function GetAndDeleteGameDropCard($game_id)
    {
        $this->db->select('id,card');
        $this->db->from('tbl_rummy_deal_card_drop');
        $this->db->where('isDeleted', false);
        $this->db->where('game_id', $game_id);
        $this->db->limit(1);
        $this->db->order_by('id', 'DESC');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        $result = $Query->result();

        if ($result) {
            $where = ['id' => $result[0]->id];
            $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
            $this->db->update('tbl_rummy_deal_card_drop', $data, $where);
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
        $Update = $this->db->update('tbl_rummy_deal_card', $data);
        if ($Update) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function ShareWallet($table_id, $user_id, $to_user_id)
    {
        $data = [
            'user_id' => $user_id,
            'to_user_id' => $to_user_id,
            'table_id' => $table_id,
            'status' => 0,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_share_wallet', $data);

        return $this->db->insert_id();
    }

    public function GetShareWallet($table_id)
    {
        $this->db->select('tbl_share_wallet.*,tbl_users.name,to_user.name as to_name');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_share_wallet.user_id');
        $this->db->join('tbl_users as to_user', 'to_user.id=tbl_share_wallet.to_user_id');
        $this->db->where('tbl_share_wallet.table_id', $table_id);
        $this->db->where('tbl_share_wallet.status', 0);
        $query = $this->db->get('tbl_share_wallet');
        return $query->result_array();
    }

    public function GetShareWalletLimit($table_id, $limit = '')
    {
        $this->db->select('tbl_share_wallet.*,tbl_users.name');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_share_wallet.user_id');
        $this->db->where('tbl_share_wallet.table_id', $table_id);
        $this->db->order_by('tbl_share_wallet.id', 'DESC');
        if (!empty($limit)) {
            $this->db->limit($limit);
        }
        $query = $this->db->get('tbl_share_wallet');
        return $query->result_array();
    }

    public function GetShareWalletById($share_wallet_id)
    {
        $this->db->where('id', $share_wallet_id);
        $this->db->where('status', 0);
        $query = $this->db->get('tbl_share_wallet');
        return $query->row();
    }

    public function UpdateShareWallet($id, $status)
    {
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $this->db->where('status', 0);
        $this->db->update('tbl_share_wallet');

        return $this->db->affected_rows();
    }

    public function TotalAmountOnTable($table_id)
    {
        $Query = $this->db->select('SUM(amount) as Total_amount')
            ->where('table_id', $table_id)
            ->get('tbl_rummy_deal');
        // echo $this->db->last_query();
        // exit;
        return $Query->row()->Total_amount;
    }

    public function AddToWallet($amount, $user_id)
    {
        $this->db->set('wallet', 'wallet+' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');
    }

    public function updateTotalWinningAmtTable($amount, $user_winning_amt, $admin_winning_amt, $table_id, $winner_id)
    {
        $this->db->set('winning_amount', $amount, false);
        $this->db->set('user_amount', $user_winning_amt, false);
        $this->db->set('commission_amount', $admin_winning_amt, false);
        $this->db->set('winner_id', $winner_id);
        $this->db->where('id', $table_id);
        $this->db->update('tbl_rummy_deal_table');

        $this->db->set('admin_coin', 'admin_coin+' . $admin_winning_amt, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->update('tbl_admin');
    }

    public function StartGame($table_id, $user_id, $to_user_id)
    {
        $data = [
            'user_id' => $user_id,
            'to_user_id' => $to_user_id,
            'table_id' => $table_id,
            'status' => 0,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_start_game', $data);

        return $this->db->insert_id();
    }

    public function GetStartGame($table_id, $status='')
    {
        $this->db->select('tbl_start_game.*,tbl_users.name,to_user.name as to_name');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_start_game.user_id');
        $this->db->join('tbl_users as to_user', 'to_user.id=tbl_start_game.to_user_id');
        $this->db->where('tbl_start_game.table_id', $table_id);
        if ($status!='') {
            $this->db->where('tbl_start_game.status', $status);
        }
        $this->db->where('tbl_start_game.added_date=(SELECT `added_date` FROM `tbl_start_game` WHERE `table_id`='.$table_id.' ORDER BY id DESC LIMIT 1)', '', false);
        $query = $this->db->get('tbl_start_game');
        // echo $this->db->last_query();
        return $query->result_array();
    }

    public function GetStartGameLimit($table_id, $limit = '')
    {
        $this->db->select('tbl_start_game.*,tbl_users.name');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_start_game.user_id');
        $this->db->where('tbl_start_game.table_id', $table_id);
        $this->db->order_by('tbl_start_game.id', 'DESC');
        if (!empty($limit)) {
            $this->db->limit($limit);
        }
        $query = $this->db->get('tbl_start_game');
        return $query->result_array();
    }

    public function GetStartGameById($share_wallet_id)
    {
        $this->db->where('id', $share_wallet_id);
        $this->db->where('status', 0);
        $query = $this->db->get('tbl_start_game');
        return $query->row();
    }

    public function UpdateStartGame($id, $status)
    {
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        $this->db->where('status', 0);
        $this->db->update('tbl_start_game');

        return $this->db->affected_rows();
    }

    public function UpdateTableUserCard($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_rummy_deal_table_user', $data);
        $GameId =  $this->db->affected_rows();
        // echo $this->db->last_query();
        return $GameId;
    }

    public function GetGameCard($game_id)
    {
        $this->db->select('cards');
        $this->db->from('tbl_cards_rummy');
        $this->db->where('`cards` NOT IN (SELECT `joker` FROM `tbl_rummy_deal` WHERE `id`='.$game_id.')', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_deal_card` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->where('`cards` NOT IN (SELECT `card` FROM `tbl_rummy_deal_card_drop` WHERE `game_id`='.$game_id.' AND isDeleted=0)', null, false);
        $this->db->order_by('RAND()');
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // exit;
        return $Query->result();
    }

    public function SwapCards($user_id, $game_id, $my_card, $new_card)
    {
        $where = ['game_id' => $game_id,'card' => $my_card];
        $data = ['isDeleted' => 1, 'updated_date' => date('Y-m-d H:i:s')];
        $this->db->update('tbl_rummy_deal_card', $data, $where);

        $table_user_data = [
            'game_id' => $game_id,
            'user_id' => $user_id,
            'card' => $new_card,
            'added_date' => date('Y-m-d H:i:s'),
            'updated_date' => date('Y-m-d H:i:s'),
            'isDeleted' => 0
        ];

        $this->db->insert('tbl_rummy_deal_card', $table_user_data);
        $TableId =  $this->db->insert_id();
        // echo $this->db->last_query();
        // exit;
        return $TableId;
    }

    public function AllGames()
    {
        $this->db->select('tbl_rummy_deal.*,tbl_users.name');
        $this->db->from('tbl_rummy_deal');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_rummy_deal.winner_id', 'left');
        $this->db->order_by('tbl_rummy_deal.id', 'DESC');
        $this->db->limit(10);
        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function Comission()
    {
        $this->db->select('tbl_rummy_deal.*');
        $this->db->from('tbl_rummy_deal');
        // $this->db->where('isDeleted', false);
        $this->db->where('winning_amount>', 0);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function LastGameCard($game_id)
    {
        $this->db->from('tbl_rummy_deal_card');
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
        $this->db->update('tbl_rummy_deal_card', $data, $where);
        $where['added_date'] = date('Y-m-d H:i:s');
        $where['updated_date'] = date('Y-m-d H:i:s');
        $this->db->insert('tbl_rummy_deal_card_drop', $where);
        $inserted_id =  $this->db->insert_id();

        return $inserted_id;
    }
}