<?php

use phpDocumentor\Reflection\DocBlock\Tags\Return_;

class Setting_model extends MY_Model
{
    public function Setting($select='*')
    {
        $this->db->select($select);
        $this->db->from('tbl_admin');
        $Query = $this->db->get();
        return $Query->row();
    }

    public function GetPermission($select='*')
    {
        $this->db->select($select);
        $this->db->from('tbl_games_on_off');
        $Query = $this->db->get();
        return $Query->row();
    }

    public function update($mobile, $referral_amount, $level_1, $level_2, $level_3, $referral_id, $referral_link, $contact_us, $terms, $privacy_policy, $help_support, $default_otp, $game_for_private, $app_version, $joining_amount, $admin_commission, $whats_no, $bonus, $bonus_amount, $payment_gateway, $symbol, $razor_api_key, $razor_secret_key, $cashfree_client_id, $cashfree_client_secret, $cashfree_stage, $paytm_mercent_id, $paytm_mercent_key, $share_text, $bank_detail_field, $adhar_card_field, $upi_field, $about_us, $refund_policy, $app_message, $app_url, $logo, $payumoney_key, $payumoney_salt, $upi_merchant_id, $upi_secret_key, $upi_id)
    {
        $data = ['updated_date' => date('Y-m-d H:i:s')];

        if (!empty($mobile)) {
            $data['mobile'] = $mobile;
        }
        if (!empty($referral_amount)) {
            $data['referral_amount'] = $referral_amount;
        }
        if (!empty($level_1)) {
            $data['level_1'] = $level_1;
        }
        if (!empty($level_2)) {
            $data['level_2'] = $level_2;
        }
        if (!empty($level_3)) {
            $data['level_3'] = $level_3;
        }
        $data['referral_id'] = $referral_id;
        $data['referral_link'] = $referral_link;
        $data['upi_id'] = $upi_id;
        if (!empty($contact_us)) {
            $data['contact_us'] = $contact_us;
        }
        if (!empty($about_us)) {
            $data['about_us'] = $about_us;
        }
        if (!empty($refund_policy)) {
            $data['refund_policy'] = $refund_policy;
        }
        if (!empty($terms)) {
            $data['terms'] = $terms;
        }
        if (!empty($privacy_policy)) {
            $data['privacy_policy'] = $privacy_policy;
        }
        if (!empty($help_support)) {
            $data['help_support'] = $help_support;
        }
        if (!empty($default_otp)) {
            $data['default_otp'] = $default_otp;
        }
        if (!empty($upi_merchant_id)) {
            $data['upi_merchant_id'] = $upi_merchant_id;
        }
        if (!empty($upi_secret_key)) {
            $data['upi_secret_key'] = $upi_secret_key;
        }
        if (!empty($game_for_private)) {
            $data['game_for_private'] = $game_for_private;
        }
        if (!empty($app_version)) {
            $data['app_version'] = $app_version;
        }

        if (!empty($app_url)) {
            $data['app_url'] = $app_url;
        }

        if (!empty($logo)) {
            $data['logo'] = $logo;
        }
        if (!empty($joining_amount)) {
            $data['joining_amount'] = $joining_amount;
        }
        if (!empty($admin_commission)) {
            $data['admin_commission'] = $admin_commission;
        }
        if (!empty($whats_no)) {
            $data['whats_no'] = $whats_no;
        }
        // if (!empty($bonus)) {
        $data['bonus'] = $bonus;

        $data['bonus_amount'] = $bonus_amount;
        // }
        // if (!empty($payment_gateway)) {
        $data['payment_gateway'] = $payment_gateway;
        // }
        // if (!empty($symbol)) {
        $data['symbol'] = $symbol;
        // }
        if (!empty($payumoney_key)) {
            $data['payumoney_key'] = $payumoney_key;
        }
        if (!empty($payumoney_salt)) {
            $data['payumoney_salt'] = $payumoney_salt;
        }
        if (!empty($razor_api_key)) {
            $data['razor_api_key'] = $razor_api_key;
        }
        if (!empty($razor_secret_key)) {
            $data['razor_secret_key'] = $razor_secret_key;
        }
        if (!empty($cashfree_client_id)) {
            $data['cashfree_client_id'] = $cashfree_client_id;
        }
        if (!empty($cashfree_client_secret)) {
            $data['cashfree_client_secret'] = $cashfree_client_secret;
        }
        if (!empty($cashfree_stage)) {
            $data['cashfree_stage'] = $cashfree_stage;
        }
        if (!empty($paytm_mercent_id)) {
            $data['paytm_mercent_id'] = $paytm_mercent_id;
        }
        if (!empty($paytm_mercent_key)) {
            $data['paytm_mercent_key'] = $paytm_mercent_key;
        }
        if (!empty($share_text)) {
            $data['share_text'] = $share_text;
        }
        if (!empty($bank_detail_field)) {
            $data['bank_detail_field'] = $bank_detail_field;
        }
        if (!empty($adhar_card_field)) {
            $data['adhar_card_field'] = $adhar_card_field;
        }
        if (!empty($upi_field)) {
            $data['upi_field'] = $upi_field;
        }
        if (!empty($app_message)) {
            $data['app_message'] = $app_message;
        }

        if ($this->db->update('tbl_admin', $data)) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function update_jackpot_amount($jackpot_coin)
    {
        $this->db->set('jackpot_coin', 'jackpot_coin'.$jackpot_coin, false);
        if ($jackpot_coin<0) {
            $this->db->set('jackpot_status', 0);
        }
        if ($this->db->update('tbl_admin')) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function update_jackpot_status($jackpot_status)
    {
        $this->db->set('jackpot_status', $jackpot_status);
        if ($this->db->update('tbl_admin')) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function update_rummy_bot_status($bot_status)
    {
        $this->db->set('robot_rummy', $bot_status);
        if ($this->db->update('tbl_admin')) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function update_teenpatti_bot_status($bot_status)
    {
        $this->db->set('robot_teenpatti', $bot_status);
        if ($this->db->update('tbl_admin')) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }

    public function AllTipLog()
    {
        $Query = $this->db->select('tbl_tip_log.*,tbl_users.name')
            ->from('tbl_tip_log')
            ->join('tbl_users', 'tbl_users.id=tbl_tip_log.user_id')
            ->get();
        return $Query->result();
    }

    public function AllCommissionLog()
    {
        $Query = $this->db->select('tbl_game.*,tbl_users.name')
            ->from('tbl_game')
            ->join('tbl_users', 'tbl_users.id=tbl_game.winner_id')
            ->where('tbl_game.winner_id!=', 0)
            ->where('tbl_game.amount!=', 0)
            ->order_by('tbl_game.id', 'DESC')
            ->get();
        return $Query->result();
    }

    public function UpdateGamesStatus($column,$type)
    {
        $this->db->set($column, $type);
        if ($this->db->update('tbl_games_on_off')) {
            return $this->db->last_query();
        } else {
            return false;
        }
    }
}