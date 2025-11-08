<?php

class Users_model extends MY_Model
{
    public function AllBotUserList()
    {
        $this->db->from('tbl_bot_users');
        $this->db->where('tbl_bot_users.isDeleted', false);
        $this->db->order_by('rand()');
        $this->db->limit(6);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function AllUserList()
    {
        $this->db->select('tbl_users.*,tbl_user_category.name as user_category');
        $this->db->from('tbl_users');
        $this->db->join('tbl_user_category', 'tbl_users.user_category_id=tbl_user_category.id', 'LEFT');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->order_by('tbl_users.id', 'asc');
        // $this->db->limit(10);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function WelcomeBonus($id = '')
    {
        if (!empty($id)) {
            $this->db->where('id', $id);
        }
        $Query = $this->db->get('tbl_welcome_reward');
        return $Query->result();
    }

    public function WelcomeBonusLog($user_id)
    {
        $this->db->select('*,DATE(added_date) as date');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get('tbl_welcome_log');
        return $Query->result();
    }

    public function AddWelcomeBonus($amount, $user_id)
    {
        $this->db->set('wallet', 'wallet+' . $amount, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $data = [
            'user_id' => $user_id,
            'coin' => $amount,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_welcome_log', $data);
        return $this->db->insert_id();
    }

    public function UpdateWelcomeBonus($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_welcome_reward', $data);
        return $this->db->affected_rows();
    }

    public function FreeUserList()
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->where('tbl_users.table_id', false);
        $this->db->order_by('tbl_users.id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function AllRedeemList()
    {
        $this->db->select('tbl_redeem.*,tbl_users.name');
        $this->db->from('tbl_redeem');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_redeem.user_id');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->order_by('tbl_redeem.id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function RedeemList($user_id)
    {
        $this->db->select('id,amount,payment_method,status,reason,added_date');
        $this->db->from('tbl_redeem');
        $this->db->where('isDeleted', false);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function WinningList($user_id)
    {
        $this->db->from('tbl_game_rewards');
        $this->db->where('isDeleted', false);
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function TodayUserList()
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->where('date(tbl_users.created_date)', date("Y-m-d"));
        $Query = $this->db->get();
        return $Query->result();
    }

    public function InsertOTP($MobileNo, $OTP)
    {
        $this->db->where('mobile', $MobileNo);
        $Query = $this->db->get('tbl_otp');
        $OTPRecord = $Query->row();
        if ($OTPRecord) {
            //update otp
            $data = [
                'otp' => $OTP,
                'added_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $OTPRecord->id);
            if ($this->db->update('tbl_otp', $data)) {
                return $OTPRecord->id;
            } else {
                return false;
            }
        } else {
            //insert otp
            $data = [
                'otp' => $OTP,
                'mobile' => $MobileNo
            ];
            if ($this->db->insert('tbl_otp', $data)) {
                return $this->db->insert_id();
            } else {
                return false;
            }
        }
    }

    public function OTPConfirm($Id, $OTP, $MobileNo)
    {
        $this->db->where('id', $Id);
        $this->db->where('otp', $OTP);
        $this->db->where('mobile', $MobileNo);
        $Query = $this->db->get('tbl_otp');
        return $Query->row();
    }

    public function TokenConfirm($user_id, $token)
    {
        $this->db->where('id', $user_id);
        $this->db->where('token', $token);
        $this->db->where('status', 0);
        $this->db->where('isDeleted', 0);
        $Query = $this->db->get('tbl_users');
        return $Query->row();
    }

    public function UserByMobile($MobileNo)
    {
        $this->db->where('isDeleted', false);
        $this->db->where('mobile', $MobileNo);
        $Query = $this->db->get('tbl_users');
        return $Query->row();
    }

    public function UpdateUser($UserId, $fcm)
    {
        $data = [
            'fcm' => $fcm
        ];
        $this->db->where('id', $UserId);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    public function Delete($UserId)
    {
        $data = [
            'isDeleted' => 1
        ];
        $this->db->where('id', $UserId);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    public function UpdateAppVersion($UserId, $app_version)
    {
        $data = [
            'app_version' => $app_version
        ];
        $this->db->where('id', $UserId);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    public function UpdateToken($UserId, $token)
    {
        $data = [
            'token' => $token
        ];
        $this->db->where('id', $UserId);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    public function UpdateUserWallet($data, $UserId)
    {
        $this->db->where('id', $UserId);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    public function AddBot($data)
    {
        $this->db->insert('tbl_users', $data);
        $user_id = $this->db->insert_id();
        $this->WalletLog($data['wallet'], 1, $user_id);
        return $user_id;
    }

    public function getAdhar($user)
    {
        $this->db->select('*');
        $this->db->where('id', $user);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get('tbl_users');
        return $Query->row()->adhar_card;
    }

    public function UpdateUserPic($UserId, $name, $profile_pic = '', $bank_detail = '', $adhar_card = '', $upi = '')
    {
        $data = [
            'name' => $name,
            'bank_detail' => $bank_detail,
            'adhar_card' => $adhar_card,
            'upi' => $upi,
            'updated_date' => date('Y-m-d H:i:s')
        ];

        if (!empty($profile_pic)) {
            $data['profile_pic'] = $profile_pic;
        }
        $this->db->where('id', $UserId);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    public function Update($UserId, $data)
    {
        $this->db->where('id', $UserId);
        $this->db->update('tbl_users', $data);
        return $this->db->affected_rows();
    }

    public function UpdateUserBankDetails($UserId, $data)
    {
        $this->db->where('user_id', $UserId);
        $this->db->update('tbl_users_bank_details', $data);
        return $this->db->affected_rows();
    }

    public function InsertUserBankDetails($data)
    {
        $this->db->insert('tbl_users_bank_details', $data);
        return $this->db->insert_id();
    }

    public function UpdateUserKyc($UserId, $data)
    {
        $this->db->where('user_id', $UserId);
        $this->db->update('tbl_users_kyc', $data);
        return $this->db->affected_rows();
    }

    public function InsertUserKyc($data)
    {
        $this->db->insert('tbl_users_kyc', $data);
        return $this->db->insert_id();
    }

    public function ChangeStatus($id, $status)
    {
        $data = [
            'status' => $status
        ];
        $this->db->where('id', $id);
        $this->db->update('tbl_users', $data);

        return $this->db->affected_rows();
    }

    public function RegisterUser($MobileNo, $Name, $profile_pic, $gender = 'm', $token = '', $password = '', $bonus_amount='')
    {
        if (empty($profile_pic)) {
            $profile_pic = ($gender == 'f') ? 'f_' . rand(1, 3) . '.png' : 'm_' . rand(1, 10) . '.png';
        }
        if ($bonus_amount=='') {
            $bonus_amount = 25000;
        }

        $data = [
            'mobile' => $MobileNo,
            'name' => $Name,
            'gender' => $gender,
            'profile_pic' => $profile_pic,
            'token' => $token,
            'password' => $password,
            'wallet' => $bonus_amount,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_users', $data);
        $UserId =  $this->db->insert_id();

        $this->WalletLog($bonus_amount, 0, $UserId);

        return $UserId;
    }

    public function RegisterUserEmail($Email, $Name, $source, $profile_pic, $gender = 'm', $token = '')
    {
        if (empty($profile_pic)) {
            $profile_pic = ($gender == 'f') ? 'f_' . rand(1, 3) . '.png' : 'm_' . rand(1, 10) . '.png';
        }

        $data = [
            'email' => $Email,
            'name' => $Name,
            'source' => $source,
            'gender' => $gender,
            'profile_pic' => $profile_pic,
            'token' => $token,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_users', $data);
        $UserId =  $this->db->insert_id();

        return $UserId;
    }

    public function AddRedeem($data)
    {
        $this->db->insert('tbl_redeem', $data);
        $ReedemId =  $this->db->insert_id();

        $this->db->set('wallet', 'wallet-' . $data['amount'], false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $data['user_id']);
        $this->db->update('tbl_users');

        $this->db->set('winning_wallet', 'winning_wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->where('winning_wallet>', 0);
        $this->db->update('tbl_users');

        return $ReedemId;
    }

    public function UpdateWallet($referer_id, $amount, $user_id)
    {
        $this->db->set('referred_by', $referer_id);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->set('wallet', 'wallet+' . $amount, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $referer_id);
        $this->db->update('tbl_users');

        $data = [
            'user_id' => $referer_id,
            'coin' => $amount,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_referral_bonus_log', $data);

        return true;
    }

    public function UpdateWalletOrder($amount, $user_id)
    {
        $this->db->set('wallet', 'wallet+' . $amount, false);
        // $this->db->set('winning_wallet', 'winning_wallet+' . $amount, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        return true;
    }

    public function UpdateWalletSpin($user_id, $coin)
    {
        $this->db->set('wallet', 'wallet+' . $coin, false);
        $this->db->set('spin_remaining', 'spin_remaining-1', false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        return true;
    }

    public function UpdateSpin($user_id, $spin_count, $user_category_id)
    {
        $this->db->set('spin_remaining', 'spin_remaining+' . $spin_count, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->set('user_category_id', $user_category_id);
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        return true;
    }

    public function ExtraWalletLog($user_id, $amount, $type)
    {
        $data = [
            'user_id' => $user_id,
            'coin' => $amount,
            'type' => $type,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_extra_wallet_log', $data);
        return $this->db->insert_id();
    }

    public function WalletLog($amount, $bonus, $user_id)
    {
        $data = [
            'user_id' => $user_id,
            'bonus' => $bonus,
            'coin' => $amount,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_wallet_log', $data);
        return $this->db->insert_id();
    }

    public function View_WalletLog($user_id)
    {
        $this->db->where('user_id', $user_id);
        $Query = $this->db->get('tbl_wallet_log');
        return $Query->result();
    }

    public function TipAdmin($amount, $user_id, $table_id, $gift_id, $to_user_id)
    {
        $this->db->set('wallet', 'wallet-' . $amount, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');

        $this->db->set('winning_wallet', 'winning_wallet-' . $amount, false);
        $this->db->where('id', $user_id);
        $this->db->where('winning_wallet>', 0);
        $this->db->update('tbl_users');

        $this->db->set('admin_coin', 'admin_coin+' . $amount, false);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->update('tbl_admin');

        $data = [
            'user_id' => $user_id,
            'to_user_id' => $to_user_id,
            'gift_id' => $gift_id,
            'table_id' => $table_id,
            'coin' => $amount,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_tip_log', $data);
        return $this->db->insert_id();
    }

    public function GiftList($table_id)
    {
        $curr = date('Y-m-d H:i:s');
        $last_min = date('Y-m-d H:i:s', strtotime('-30 seconds'));

        $this->db->select('tbl_tip_log.*,tbl_gift.image');
        $this->db->where('gift_id!=', 0);
        $this->db->where('table_id', $table_id);
        $this->db->where('tbl_tip_log.added_date >=', $last_min);
        $this->db->where('tbl_tip_log.added_date <=', $curr);
        $Query = $this->db->join('tbl_gift', 'tbl_gift.id=tbl_tip_log.gift_id');
        $Query = $this->db->get('tbl_tip_log');
        // echo $this->db->last_query();
        return $Query->result();
    }

    public function UpdateReferralCode($user_id, $referralId)
    {
        if (!$referralId) {
            $referralId = 'TEENPATTI';
        }
        $this->db->set('referral_code', $referralId . $user_id);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('id', $user_id);
        $this->db->update('tbl_users');
    }

    public function LoginUser($MobileNo, $Password)
    {
        $this->db->where('mobile', $MobileNo);
        $this->db->where('password', $Password);
        $user = $this->db->get('tbl_users');

        return $user->result();
    }

    public function UserProfile($id)
    {
        $this->db->select('tbl_users.*,tbl_user_category.name as user_category');
        $this->db->from('tbl_users');
        $this->db->join('tbl_user_category', 'tbl_users.user_category_id=tbl_user_category.id', 'LEFT');
        $this->db->where('tbl_users.id', $id);
        $this->db->where('tbl_users.isDeleted', false);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function UserKyc($id)
    {
        $this->db->from('tbl_users_kyc');
        $this->db->where('user_id', $id);
        $this->db->where('isDeleted', false);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function UserBankDetails($id)
    {
        $this->db->from('tbl_users_bank_details');
        $this->db->where('user_id', $id);
        $this->db->where('isDeleted', false);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function AddPurchaseReferLog($data)
    {
        $this->db->insert('tbl_purcharse_ref', $data);
        return $this->db->insert_id();
    }

    public function AddWelcomeReferLog($data)
    {
        $this->db->insert('tbl_welcome_ref', $data);
        return $this->db->insert_id();
    }

    public function GetFreeBot()
    {
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('status', false);
        $this->db->where('table_id', 0);
        $this->db->where('wallet>=', 10000);
        $this->db->where('user_type', 1);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function GetFreeRummyBot()
    {
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('status', false);
        $this->db->where('rummy_table_id', 0);
        $this->db->where('wallet>=', 100);
        $this->db->where('user_type', 1);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function Setting()
    {
        $this->db->from('tbl_admin');
        $this->db->where('isDeleted', false);

        $Query = $this->db->get();
        return $Query->row();
    }

    public function UpdateSetting($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_admin', $data);
        return $this->db->affected_rows();
    }

    public function UserWallet($user_id)
    {
        $this->db->select('tbl_users.wallet');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('tbl_users.id', $user_id);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->row();
    }

    public function UserProfileByMobile($MobileNo)
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('tbl_users.mobile', $MobileNo);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function UserProfileByEmail($Email)
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('tbl_users.email', $Email);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function IsValidReferral($referral_code)
    {
        $this->db->select('tbl_users.*');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', false);
        $this->db->where('tbl_users.referral_code', $referral_code);

        $Query = $this->db->get();
        // echo $this->db->last_query();
        // die();
        return $Query->result();
    }

    public function View_Wins($user_id)
    {
        $Query = $this->db->where('isDeleted', false)
            ->where('winner_id', $user_id)
            ->get('tbl_game');
        return $Query->result();
    }

    public function View_Purchase($user_id)
    {
        $Query = $this->db->where('isDeleted', false)
            ->where('user_id', $user_id)
            ->where('payment', 1)
            ->get('tbl_purchase');
        return $Query->result();
    }

    public function View_AllPurchase($user_id)
    {
        $Query = $this->db->query("SELECT * FROM (
            SELECT `coin`,`price`,`updated_date`, 'ONLINE PURCHASE' as type,`user_id` FROM `tbl_purchase` WHERE `payment`=1
            UNION
            SELECT `coin`,0 as price,`added_date`, IF(`bonus`=1,'BONUS','ADMIN PURCHASE') as type,`user_id` FROM `tbl_wallet_log`
            ) as a WHERE user_id='".$user_id."'ORDER BY updated_date DESC");
        return $Query->result();
    }

    public function View_Reffer($user_id)
    {
        $Query = $this->db->where('isDeleted', false)
            ->where('referred_by', $user_id)
            ->get('tbl_users');
        return $Query->result();
    }

    public function Purchase_History()
    {
        $Query = $this->db->select('tbl_purchase.*,tbl_users.name')
            ->from('tbl_purchase')
            ->join('tbl_users', 'tbl_users.id=tbl_purchase.user_id')
            ->where('tbl_purchase.payment', true)
            ->where('tbl_purchase.isDeleted', false)
            ->where('tbl_users.isDeleted', false)
            ->get();
        return $Query->result();
    }

    public function View_Purchase_Reffer($user_id='')
    {
        $this->db->select('tbl_purcharse_ref.*,tbl_users.name')
            ->from('tbl_purcharse_ref')
            ->join('tbl_users', 'tbl_users.id=tbl_purcharse_ref.user_id')
            ->where('tbl_users.isDeleted', false);

        if (!empty($user_id)) {
            $this->db->where('tbl_purcharse_ref.user_id', $user_id);
        }

        $Query = $this->db->get();
        return $Query->result();
    }

    public function View_Welcome_Reffer($user_id)
    {
        $Query = $this->db->select('tbl_welcome_ref.*,tbl_users.name')
            ->from('tbl_welcome_ref')
            ->join('tbl_users', 'tbl_users.id=tbl_welcome_ref.bonus_user_id')
            ->where('tbl_users.isDeleted', false)
            ->where('tbl_welcome_ref.user_id', $user_id)
            ->get();
        return $Query->result();
    }

    public function ActiveUser()
    {
        $Query = $this->db->select('tbl_users.*')
            ->from('tbl_users')
            ->where('tbl_users.isDeleted', false)
            ->where('DATE(tbl_users.updated_date)>', 'DATE_SUB(CURRENT_TIMESTAMP, INTERVAL +2 DAY)', false)
            ->order_by('tbl_users.id', 'desc')
            ->get();
        return $Query->result();
    }

    public function WalletAmount($user_id)
    {
        $this->db->select('tbl_ander_baher_bet.*,tbl_ander_baher.room_id');
        $this->db->from('tbl_ander_baher_bet');
        $this->db->join('tbl_ander_baher', 'tbl_ander_baher.id=tbl_ander_baher_bet.ander_baher_id');
        $this->db->where('tbl_ander_baher_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function DragonWalletAmount($user_id)
    {
        $this->db->select('tbl_dragon_tiger_bet.*,tbl_dragon_tiger.room_id');
        $this->db->from('tbl_dragon_tiger_bet');
        $this->db->join('tbl_dragon_tiger', 'tbl_dragon_tiger.id=tbl_dragon_tiger_bet.dragon_tiger_id');
        $this->db->where('tbl_dragon_tiger_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function TeenPattiLog($user_id)
    {
        $Query = $this->db->query('SELECT tbl_game_log.`game_id`,SUM(tbl_game_log.`amount`) as invest,IFNULL((SELECT user_winning_amt FROM `tbl_game` WHERE winner_id='.$user_id.' AND id=`game_id`),0) as winning_amount,tbl_game_log.added_date,tbl_table.private as table_type FROM `tbl_game_log` JOIN tbl_game on tbl_game_log.game_id=tbl_game.id join tbl_table on tbl_table.id=tbl_game.table_id  WHERE tbl_game_log.`user_id`='.$user_id.' GROUP BY tbl_game_log.`game_id`');
        // $this->db->get();
        return $Query->result();
    }

    public function RummyLog($user_id)
    {
        $Query = $this->db->query('SELECT * FROM
        (SELECT `game_id`,`user_id`,`action`,`amount`,`added_date` FROM `tbl_rummy_log` WHERE `amount`!=0 AND `user_id`='.$user_id.'
        UNION
        SELECT `id`,`winner_id`,10,`user_winning_amt`,`added_date` FROM `tbl_rummy` WHERE  `winner_id`='.$user_id.') rummy
        ORDER BY added_date ASC');
        // $this->db->get();
        return $Query->result();
    }

    public function RummyDealLog($user_id)
    {
        $Query = $this->db->query('SELECT * FROM
        (SELECT `game_id`,`user_id`,`action`,`amount`,`added_date` FROM `tbl_rummy_deal_log` WHERE `amount`!=0 AND `user_id`='.$user_id.'
        UNION
        SELECT `id`,`winner_id`,10,`user_winning_amt`,`added_date` FROM `tbl_rummy_deal` WHERE  `winner_id`='.$user_id.') rummy
        ORDER BY added_date DESC');
        // $this->db->get();
        return $Query->result();
    }

    public function RummyPoolLog($user_id)
    {
        $Query = $this->db->query('SELECT * FROM
        (SELECT `game_id`,`user_id`,`action`,`amount`,`added_date` FROM `tbl_rummy_pool_log` WHERE `amount`!=0 AND `user_id`='.$user_id.'
        UNION
        SELECT `id`,`winner_id`,10,`user_winning_amt`,`added_date` FROM `tbl_rummy_pool` WHERE  `winner_id`='.$user_id.') rummy
        ORDER BY added_date DESC');
        // $this->db->get();
        return $Query->result();
    }

    public function SevenUpAmount($user_id)
    {
        $this->db->select('tbl_seven_up_bet.*,tbl_seven_up.room_id');
        $this->db->from('tbl_seven_up_bet');
        $this->db->join('tbl_seven_up', 'tbl_seven_up.id=tbl_seven_up_bet.seven_up_id');
        $this->db->where('tbl_seven_up_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function ColorPredictionAmount($user_id)
    {
        $this->db->select('tbl_color_prediction_bet.*,tbl_color_prediction.room_id');
        $this->db->from('tbl_color_prediction_bet');
        $this->db->join('tbl_color_prediction', 'tbl_color_prediction.id=tbl_color_prediction_bet.color_prediction_id');
        $this->db->where('tbl_color_prediction_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function CarRouletteAmount($user_id)
    {
        $this->db->select('tbl_car_roulette_bet.*,tbl_car_roulette.room_id');
        $this->db->from('tbl_car_roulette_bet');
        $this->db->join('tbl_car_roulette', 'tbl_car_roulette.id=tbl_car_roulette_bet.car_roulette_id');
        $this->db->where('tbl_car_roulette_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function AnimalRouletteAmount($user_id)
    {
        $this->db->select('tbl_animal_roulette_bet.*,tbl_animal_roulette.room_id');
        $this->db->from('tbl_animal_roulette_bet');
        $this->db->join('tbl_animal_roulette', 'tbl_animal_roulette.id=tbl_animal_roulette_bet.animal_roulette_id');
        $this->db->where('tbl_animal_roulette_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function JackpotAmount($user_id)
    {
        $this->db->select('tbl_jackpot_bet.*,tbl_jackpot.room_id');
        $this->db->from('tbl_jackpot_bet');
        $this->db->join('tbl_jackpot', 'tbl_jackpot.id=tbl_jackpot_bet.jackpot_id');
        $this->db->where('tbl_jackpot_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function Poker($user_id)
    {
        $this->db->select('*');
        $this->db->from('tbl_poker');
        $this->db->where('winner_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function HeadTailAmount($user_id)
    {
        $this->db->select('tbl_head_tail_bet.*,tbl_head_tail.room_id');
        $this->db->from('tbl_head_tail_bet');
        $this->db->join('tbl_head_tail', 'tbl_head_tail.id=tbl_head_tail_bet.head_tail_id');
        $this->db->where('tbl_head_tail_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function RedBlack($user_id)
    {
        $this->db->select('tbl_red_black_bet.*,tbl_red_black.room_id');
        $this->db->from('tbl_red_black_bet');
        $this->db->join('tbl_red_black', 'tbl_red_black.id=tbl_red_black_bet.red_black_id');
        $this->db->where('tbl_red_black_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }
    public function BaccaratLog($user_id)
    {
        $this->db->select('tbl_baccarat_bet.*,tbl_baccarat.room_id');
        $this->db->from('tbl_baccarat_bet');
        $this->db->join('tbl_baccarat', 'tbl_baccarat.id=tbl_baccarat_bet.baccarat_id');
        $this->db->where('tbl_baccarat_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }
    public function JhandiMunda($user_id)
    {
        $this->db->select('tbl_jhandi_munda_bet.*,tbl_jhandi_munda.room_id');
        $this->db->from('tbl_jhandi_munda_bet');
        $this->db->join('tbl_jhandi_munda', 'tbl_jhandi_munda.id=tbl_jhandi_munda_bet.jhandi_munda_id');
        $this->db->where('tbl_jhandi_munda_bet.user_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function getHistory($user_id)
    {
        $this->db->select('tbl_ludo.*,tbl_users.name');
        $this->db->from('tbl_ludo');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_ludo.winner_id');
        $this->db->where('tbl_ludo.winner_id', $user_id);
        $Query = $this->db->get();
        return $Query->result();
    }

    public function UpdateOfflineUsers()
    {
        $this->db->query('UPDATE `tbl_users` SET `ander_bahar_room_id`=0,`dragon_tiger_room_id`=0,`jackpot_room_id`=0,`seven_up_room_id`=0,`color_prediction_room_id`=0,`car_roulette_room_id`=0,`animal_roulette_room_id`=0 WHERE TIME_TO_SEC(TIMEDIFF(NOW(), updated_date))>30');
        return $this->db->affected_rows();
    }

    public function getOnlineUsers($room_id, $game_column)
    {
        $this->db->where($game_column.'>', 0);
        $this->db->where('tbl_users.isDeleted', false);
        $Query = $this->db->get('tbl_users');
        return $Query->num_rows();
    }

    public function GetUsers($postData=null)
    {
        // print_r($_GET);die;
        $response = array();

        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value

        ## Total number of records without filtering
        $this->db->select('tbl_users.*,tbl_user_category.name as user_category');
        $this->db->from('tbl_users');
        $this->db->join('tbl_user_category', 'tbl_users.user_category_id=tbl_user_category.id', 'LEFT');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->order_by('tbl_users.id', 'asc');
        $totalRecords = $this->db->get()->num_rows();

        $this->db->select('tbl_users.*,tbl_user_category.name as user_category');
        $this->db->from('tbl_users');
        $this->db->join('tbl_user_category', 'tbl_users.user_category_id=tbl_user_category.id', 'LEFT');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->order_by('tbl_users.id', 'asc');
        // $this->db->where($defaultWhere);
        if ($searchValue) {
            $this->db->group_start();
            $this->db->like('tbl_users.name', $searchValue, 'after');
            $this->db->like('tbl_users.name', $searchValue, 'after');
            $this->db->or_like('tbl_users.mobile', $searchValue, 'after');
            $this->db->or_like('tbl_users.bank_detail', $searchValue, 'after');
            $this->db->or_like('tbl_users.adhar_card', $searchValue, 'after');
            $this->db->or_like('tbl_users.upi', $searchValue, 'after');
            $this->db->or_like('tbl_users.email', $searchValue, 'after');
            $this->db->or_like('tbl_user_category.name', $searchValue, 'after');
            $this->db->or_like('tbl_users.wallet', $searchValue, 'after');
            $this->db->or_like('tbl_users.added_date', $searchValue, 'after');
            $this->db->group_end();
        }

        $totalRecordwithFilter = $this->db->get()->num_rows();
        $this->db->select('tbl_users.*,tbl_user_category.name as user_category');
        $this->db->from('tbl_users');
        $this->db->join('tbl_user_category', 'tbl_users.user_category_id=tbl_user_category.id', 'LEFT');
        $this->db->where('tbl_users.isDeleted', false);
        $this->db->order_by($columnName, $columnSortOrder);
        if ($searchValue) {
            $this->db->group_start();
            $this->db->like('tbl_users.name', $searchValue, 'after');
            $this->db->or_like('tbl_users.mobile', $searchValue, 'after');
            $this->db->or_like('tbl_users.bank_detail', $searchValue, 'after');
            $this->db->or_like('tbl_users.adhar_card', $searchValue, 'after');
            $this->db->or_like('tbl_users.upi', $searchValue, 'after');
            $this->db->or_like('tbl_users.email', $searchValue, 'after');
            $this->db->or_like('tbl_user_category.name', $searchValue, 'after');
            $this->db->or_like('tbl_users.wallet', $searchValue, 'after');
            $this->db->or_like('tbl_users.added_date', $searchValue, 'after');
            $this->db->group_end();
        }
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();
        $data = array();

        $i = $start+1;
        // echo '<pre>';print_r($records);die;
        foreach ($records as $record) {
            $status = '<select class="form-control" onchange="ChangeStatus('.$record->id.',this.value)">
            <option value="0"'.(($record->status == 0) ? 'selected' : '').'>Active</option>
            <option value="1" '.(($record->status == 1) ? 'selected' : '').'>Block</option>
        </select>';
            $action = '<button data-toggle="modal" data-target="#exampleModal" class="btn btn-info"
            data-toggle="tooltip" data-placement="top" title="View Wins"><span
                class="fa fa-eye"></span></button>
                | <button data-toggle="modal" data-target="#exampleModal" class="btn btn-info"
                data-toggle="tooltip" data-placement="top" title="View Ladger Report"><span class="ti-wallet"></span></button>
        | <a href="'.base_url('backend/user/edit/' . $record->id).'" class="btn btn-info"
            data-toggle="tooltip" data-placement="top" title="Edit"><span
                class="fa fa-credit-card" ></span></a>
        | <button data-toggle="modal" data-target="#exampleModal" class="btn btn-info"
            data-toggle="tooltip" data-placement="top" title="Edit"><span
                class="fa fa-edit" ></span></button>';
        
            $data[] = array(
              "id"=>$i,
              "name"=>$record->name,
              "bank_detail"=>$record->bank_detail,
              "adhar_card"=>$record->adhar_card,
              "upi"=>$record->upi,
              "mobile"=>($record->mobile=='') ? $record->email : $record->mobile,
              "user_type"=>$record->user_type==1 ? 'BOT' : 'REAL',
              "user_category"=>$record->user_category,
              "wallet"=>$record->wallet,
              "winning_wallet"=>$record->winning_wallet,
              "on_table"=>($record->table_id > 0) ? 'Yes' : 'No',
              "status"=>$status,
              "added_date"=>date("d-m-Y", strtotime($record->added_date)),
              "action"=>$action,
           );
            $i++;
        }

        ## Response
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordwithFilter,
           "aaData" => $data,
        );

        return $response;
    }

    public function GetLadgerReports($id, $postData=null)
    {
        $response = array();
        # Total number of records without filtering
        $totalRecordwithoutFilter=$this->TotalRecordsWithoutFilter($id, $postData);
        # Total number of records with filtering
        $totalRecordwithFilter=$this->TotalRecordsWithFilter($id, $postData);
        $records=$this->GetAllLogs($id, $postData);
        $data = array();
        $start = $postData['start'];
        $draw = $postData['draw'];
        $i = $start+1;
        // echo '<pre>';print_r($records);die;
        $total = $records[0]->user_wallet;
        foreach ($records as $record) {
            $amount = $record->winning_amount-$record->amount;
            $total=$total+$amount;
            $data[] = array(
              "id"=>$i,
              "game"=>$record->game,
              "amount"=>$amount,
              "wallet"=>$total,
              "added_date"=>date("d-m-Y h:i:s A", strtotime($record->added_date)),
           );
            $i++;
        }

        ## Response
        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecordwithoutFilter,
           "iTotalDisplayRecords" => $totalRecordwithFilter,
           "aaData" => $data,
        );

        return $response;
    }

    public function TotalRecordsWithFilter($id, $postData)
    {
        ## Read value
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $sql='SELECT main_table.*,tbl_users.wallet as user_wallet FROM (SELECT 
   "Andar Bahar" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_ander_baher_bet where user_id="'.$id.'"
    UNION
   SELECT 
   "Dragon & Tiger" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_dragon_tiger_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Baccarat" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_baccarat_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Seven Up Down" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_seven_up_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Car Roulette" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_car_roulette_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Color Predection" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_color_prediction_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Animal Roulette" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_animal_roulette_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Head Tail" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_head_tail_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Red Vs Black" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_red_black_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Dragon & Tiger" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_jhandi_munda_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Roulette" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_roulette_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Poker" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
   FROM tbl_poker where winner_id="'.$id.'"
   UNION
   SELECT 
   "Teen Patti" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
   FROM tbl_game where winner_id="'.$id.'"
   UNION
   SELECT 
   "JackPot" as game,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_jackpot_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Rummy" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
   FROM tbl_rummy where winner_id="'.$id.'"
   UNION
   SELECT 
   "Deal Rummy" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
   FROM tbl_rummy_deal where winner_id="'.$id.'"
   UNION
   SELECT 
   "Pool Rummy" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
   FROM tbl_rummy_pool where winner_id="'.$id.'"
   UNION
   SELECT 
   "Ludo" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
   FROM tbl_ludo where winner_id="'.$id.'"
   UNION
   SELECT 
   "Wallet Log" as game,user_id,"0" as winning_amount,added_date,coin as amount,"" as user_amount
   FROM tbl_wallet_log where user_id="'.$id.'"
   UNION
   SELECT 
   "Purchase" as game,user_id,"0" as winning_amount,added_date,coin as amount,"" as user_amount
   FROM tbl_purchase where user_id="'.$id.'"
   ) as main_table join tbl_users on tbl_users.id=main_table.user_id Where tbl_users.isDeleted=0';
        if ($searchValue) {
            $sql .= ' and game like "%' . $searchValue . '%"';
        }
        $query=$this->db->query($sql);
        // $this->db->where($defaultWhere);

        return $totalRecordwithFilter = $query->num_rows();
    }
    public function TotalRecordsWithoutFilter($id, $postData)
    {
        $query=$this->db->query('SELECT main_table.*,tbl_users.wallet as user_wallet FROM (SELECT 
        "Andar Bahar" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_ander_baher_bet where user_id="'.$id.'"
         UNION
        SELECT 
        "Dragon & Tiger" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_dragon_tiger_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Baccarat" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_baccarat_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Seven Up Down" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_seven_up_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Car Roulette" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_car_roulette_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Color Predection" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_color_prediction_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Animal Roulette" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_animal_roulette_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Head Tail" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_head_tail_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Red Vs Black" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_red_black_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Dragon & Tiger" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_jhandi_munda_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Roulette" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_roulette_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Poker" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
        FROM tbl_poker where winner_id="'.$id.'"
        UNION
        SELECT 
        "Teen Patti" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
        FROM tbl_game where winner_id="'.$id.'"
        UNION
        SELECT 
        "JackPot" as game,user_id,winning_amount,added_date,amount,user_amount
        FROM tbl_jackpot_bet where user_id="'.$id.'"
        UNION
        SELECT 
        "Rummy" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
        FROM tbl_rummy where winner_id="'.$id.'"
        UNION
        SELECT 
        "Deal Rummy" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
        FROM tbl_rummy_deal where winner_id="'.$id.'"
        UNION
        SELECT 
        "Pool Rummy" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
        FROM tbl_rummy_pool where winner_id="'.$id.'"
        UNION
        SELECT 
        "Ludo" as game,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,"" as user_amount
        FROM tbl_ludo where winner_id="'.$id.'"
        UNION
        SELECT 
        "Wallet Log" as game,user_id,coin as winning_amount,added_date,0 as amount,"" as user_amount
        FROM tbl_wallet_log where user_id="'.$id.'"
        UNION
        SELECT 
        "Purchase" as game,user_id,coin as winning_amount,added_date,"0" as amount,"" as user_amount
        FROM tbl_purchase where user_id="'.$id.'"
        ) as main_table join tbl_users on tbl_users.id=main_table.user_id');

        return $query->num_rows();
    }

    public function GetAllLogs($id, $postData=[])
    {
        ## Read value
        //    $draw = $postData['draw'];
        //    $start = $postData['start'];
        //    $rowperpage = $postData['length']; // Rows display per page
        //    $columnIndex = $postData['order'][0]['column']; // Column index
        //    $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        //    $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        //    $searchValue = $postData['search']['value']; // Search value
        $sql='SELECT main_table.*,tbl_users.wallet as user_wallet FROM (SELECT 
   "Andar Bahar" as game,ander_baher_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_ander_baher_bet where user_id="'.$id.'"
    UNION
   SELECT 
   "Dragon & Tiger" as game,dragon_tiger_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_dragon_tiger_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Baccarat" as game,baccarat_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_baccarat_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Seven Up Down" as game,seven_up_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_seven_up_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Car Roulette" as game,car_roulette_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_car_roulette_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Color Predection" as game,color_prediction_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_color_prediction_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Animal Roulette" as game,animal_roulette_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_animal_roulette_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Head Tail" as game,head_tail_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_head_tail_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Red Vs Black" as game,red_black_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_red_black_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Dragon & Tiger" as game,dragon_tiger_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_dragon_tiger_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Roulette" as game,id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_roulette_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Jhandi Munda" as game,jhandi_munda_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_jhandi_munda_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Poker" as game,poker_table_id as reff_id,id as bet_id,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,user_winning_amt as user_amount
   FROM tbl_poker where winner_id="'.$id.'"
   UNION
   SELECT 
   "Teen Patti" as game,table_id as reff_id,id as bet_id,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,user_winning_amt as user_amount
   FROM tbl_game where winner_id="'.$id.'"
   UNION
   SELECT 
   "JackPot" as game,jackpot_id as reff_id,id as bet_id,user_id,winning_amount,added_date,amount,user_amount
   FROM tbl_jackpot_bet where user_id="'.$id.'"
   UNION
   SELECT 
   "Rummy" as game,table_id as reff_id,id as bet_id,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,user_winning_amt as user_amount
   FROM tbl_rummy where winner_id="'.$id.'"
   UNION
   SELECT 
   "Deal Rummy" as game,table_id as reff_id,id as bet_id,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,user_winning_amt as user_amount
   FROM tbl_rummy_deal where winner_id="'.$id.'"
   UNION
   SELECT 
   "Pool Rummy" as game,table_id as reff_id,id as bet_id,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,user_winning_amt as user_amount
   FROM tbl_rummy_pool where winner_id="'.$id.'"
   UNION
   SELECT 
   "Ludo" as game,ludo_table_id as reff_id,id as bet_id,winner_id as user_id,user_winning_amt as winning_amount,added_date,amount,user_winning_amt as user_amount
   FROM tbl_ludo where winner_id="'.$id.'"
   UNION
   SELECT 
   "Register Bonus" as game,id as reff_id,id as bet_id,user_id,"" as winning_amount,added_date,"0" as amount,coin as user_amount
   FROM tbl_wallet_log where user_id="'.$id.'"
   UNION
   SELECT 
   "Purchase" as game,id as reff_id,id as bet_id,user_id,"0" as winning_amount,added_date,"0" as amount,coin as user_amount
   FROM tbl_purchase where payment=1 and user_id="'.$id.'"
   UNION
   SELECT 
   "Welcome Bonus" as game,id as reff_id,id as bet_id,user_id,"0" as winning_amount,added_date,"0" as amount,coin as user_amount
   FROM tbl_welcome_log where user_id="'.$id.'"
   UNION
   SELECT 
   "Withdrawl" as game,id as reff_id,id as bet_id,user_id,"0" as winning_amount,created_date as added_date,coin as amount,"0" as user_amount
   FROM tbl_withdrawal_log where (status=0 OR status=1) and user_id="'.$id.'"
   UNION
   SELECT 
   "Tip" as game,id as reff_id,id as bet_id,user_id,"0" as winning_amount, added_date,"0" as amount,coin as user_amount
   FROM tbl_tip_log where user_id="'.$id.'"
   UNION
   SELECT 
   "Refferal Bonus" as game,id as reff_id,id as bet_id,user_id,"0" as winning_amount, added_date,"0" as amount,coin as user_amount
   FROM tbl_referral_bonus_log where user_id="'.$id.'"
   UNION
   SELECT 
   "Extra Bonus" as game,id as reff_id,id as bet_id,user_id,"0" as winning_amount, added_date,"0" as amount,coin as user_amount
   FROM tbl_extra_wallet_log where user_id="'.$id.'"
   ) as main_table join tbl_users on tbl_users.id=main_table.user_id where tbl_users.isDeleted=0 ';

//    if ($searchValue) {
//     $sql .= ' and game like "%' . $searchValue . '%"';
        // }
        $sql.=' order by added_date desc limit 100';
        // $sql.=' order by '.$columnName.' '.$columnSortOrder;
        // $sql.=' limit '.$start.','.$rowperpage.'';
        $query=$this->db->query($sql);

        // $this->db->order_by($columnName, $columnSortOrder);
        return $records = $query->result();
    }
}