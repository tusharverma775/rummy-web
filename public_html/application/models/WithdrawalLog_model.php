<?php

use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class WithdrawalLog_model extends MY_Model
{

  public function AllRedeemList()
    {
        $this->db->select('*');
        $this->db->from('tbl_redeem');
        $this->db->where('isDeleted', false);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function Insert($data)
    {
        if ($this->db->insert('tbl_redeem', $data))
            return $this->db->last_query();
        else
            return false;
    }

    public function getRedeem($id)
    {
        $Query = $this->db->where('id', $id)
            ->get('tbl_redeem');
        if ($Query)
            return $Query->row();
        else
            return false;
    }

    public function update($Redeem_id, $data)
    {
        $this->db->where('id', $Redeem_id);
        if ($this->db->update('tbl_redeem', $data))
            return $this->db->last_query();
        else
            return false;
    }

    public function Delete($id)
    {
        $Query = $this->db->set('isDeleted', 1)
            ->where('id', $id)
            ->update('tbl_redeem');
        if ($Query)
            return $this->db->last_query();
        else
            return false;
    }

    public function WithDraw($user_id, $Redeem_id, $coin, $mobile)
    {
        $Deducted = $this->db->set('wallet', "wallet-$coin", FALSE)
            ->set('winning_wallet', "winning_wallet-$coin", FALSE)
            ->set('updated_date', date('Y-m-d H:i:s'))
            ->where('id', $user_id)
            ->update('tbl_users');
        if ($Deducted) {
            $data = [
                'user_id' => $user_id,
                'redeem_id' => $Redeem_id,
                'mobile' => $mobile,
                'coin' => $coin,
            ];
            $this->db->insert('tbl_withdrawal_log', $data);
            return $this->db->insert_id();
        }
    }
    
	    public function WithDrawal_log($user_id)
    {
        return $Query = $this->db->select('tbl_withdrawal_log.*,tbl_users.name as user_name,tbl_users.mobile as user_mobile,tbl_users.bank_detail,tbl_users.adhar_card,tbl_users.upi')
            ->from('tbl_withdrawal_log')
            ->join('tbl_users', 'tbl_users.id=tbl_withdrawal_log.user_id')
            ->where('tbl_withdrawal_log.isDeleted', FALSE)
            ->where('tbl_withdrawal_log.user_id', $user_id)
            ->get()
            ->result();
    }

    public function WithDrawal_list($status)
    {
        return $Query = $this->db->select('tbl_withdrawal_log.*,tbl_users.name as user_name,tbl_users.mobile as user_mobile,tbl_users.bank_detail,tbl_users.adhar_card,tbl_users.upi')
            ->from('tbl_withdrawal_log')
            ->join('tbl_users', 'tbl_users.id=tbl_withdrawal_log.user_id')
            ->where('tbl_withdrawal_log.isDeleted', FALSE)
            ->where('tbl_withdrawal_log.status', $status)
            ->get()
            ->result();
    }

    public function ChangeStatus($id, $status)
    {
        $this->db->where('id', $id)
            ->set('status', $status)
            ->update('tbl_withdrawal_log');
            
        if($status==2)
        {
            $Query = $this->db->where('isDeleted', FALSE)
            ->where('id', $id)
            ->get('tbl_withdrawal_log')->row();

            $this->db->set('wallet', "wallet+$Query->coin", FALSE)
            ->set('winning_wallet', "winning_wallet+$Query->coin", FALSE)
            ->set('updated_date', date('Y-m-d H:i:s'))
            ->where('id', $Query->user_id)
            ->update('tbl_users');
        }
        return $this->db->last_query();
    }

}