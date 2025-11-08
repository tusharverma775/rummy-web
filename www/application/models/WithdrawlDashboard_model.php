<?php

class WithdrawlDashboard_model extends MY_Model
{
    public function WithDrawalAmount($status)
    {
       $this->db->select('SUM(coin) as total');
        $this->db->where('isDeleted', FALSE);
        $this->db->where('status', $status);
        $Query = $this->db->get('tbl_withdrawal_log');
        return $Query->row()->total;
    }

    public function PurchaseOnline()
    {
        $this->db->select('SUM(tbl_purchase.coin) as total');
        $this->db->join('tbl_users','tbl_purchase.user_id=tbl_users.id');
        $this->db->where('tbl_purchase.isDeleted', FALSE);
        $this->db->where('tbl_purchase.payment', 1);
        $this->db->where('tbl_users.user_type',0);
        $Query = $this->db->get('tbl_purchase');
        return $Query->row()->total;
    }


    public function RobotCoin()
    {
        $this->db->select('SUM(tbl_wallet_log.coin) as total');
        $this->db->join('tbl_users','tbl_wallet_log.user_id=tbl_users.id');
        $this->db->where('tbl_users.user_type',1);
        $Query = $this->db->get('tbl_wallet_log');
        return $Query->row()->total;
    }

    public function PurchaseOffline()
    {
       $this->db->select('SUM(tbl_wallet_log.coin) as total');
       $this->db->join('tbl_users','tbl_wallet_log.user_id=tbl_users.id');
       $this->db->where('tbl_users.user_type',0);
        $Query = $this->db->get('tbl_wallet_log');
        return $Query->row()->total;
    }

    public function WelcomeBonus()
    {
        $this->db->select('SUM(tbl_welcome_log.coin) as total');
        $Query = $this->db->get('tbl_welcome_log');
        return $Query->row()->total;
    }

    public function PurchaseBonus()
    {
        $this->db->select('SUM(tbl_purcharse_ref.coin) as total');
        $Query = $this->db->get('tbl_purcharse_ref');
        return $Query->row()->total;
    }

    public function WelcomeRefferalBonus()
    {
        $this->db->select('SUM(tbl_welcome_ref.coin) as total');
        $Query = $this->db->get('tbl_welcome_ref');
        return $Query->row()->total;
    }
 
    public function RefferalBonus()
    {
        $this->db->select('SUM(tbl_referral_bonus_log.coin) as total');
        $Query = $this->db->get('tbl_referral_bonus_log');
        return $Query->row()->total;
    }

}