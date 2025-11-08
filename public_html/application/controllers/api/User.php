<?php

use Restserver\Libraries\REST_Controller;

include APPPATH . '/libraries/REST_Controller.php';
include APPPATH . '/libraries/Format.php';
class User extends REST_Controller
{
    private $data;
    private $UserData;
    private $UserId;
    public function __construct()
    {
        parent::__construct();
        $header = $this->input->request_headers('token');

        if (!isset($header['Token'])) {
            $data['message'] = 'Invalid Request';
            $data['code'] = HTTP_UNAUTHORIZED;
            $this->response($data, HTTP_OK);
            exit();
        }

        if ($header['Token'] != getToken()) {
            $data['message'] = 'Invalid Authorization';
            $data['code'] = HTTP_METHOD_NOT_ALLOWED;
            $this->response($data, HTTP_OK);
            exit();
        }

        $this->data = $this->input->post();

        $this->load->model([
            'Users_model',
            'Game_model',
            'Setting_model'
        ]);
    }

    public function send_otp_post()
    {
        $mobile = $this->data['mobile'];
        $user = $this->Users_model->UserProfileByMobile($mobile);
        if ($user) {
            $data['message'] = 'Mobile Already Exist, Please Login';
            $data['code'] = HTTP_NOT_FOUND;
            $this->response($data, HTTP_OK);
            exit();
        } else {
            $otp = rand(1000, 9999);

            // $otp = 9988;
            $otp_id = $this->Users_model->InsertOTP($mobile, $otp);
            $msg = "Yout OTP code is : ".$otp;
            // Send_SMS($mobile,$msg);
            Send_OTP($mobile, $otp);
            $data['message'] = 'Success';
            $data['otp_id'] = $otp_id;
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        }
    }

    public function only_send_otp_post()
    {
        $mobile = $this->data['mobile'];
        // $user = $this->Users_model->UserProfileByMobile($mobile);
        // if ($user) {
        //     $data['message'] = 'Mobile Already Exist, Please Login';
        //     $data['code'] = HTTP_NOT_FOUND;
        //     $this->response($data, HTTP_OK);
        //     exit();
        // } else {
        $otp = rand(1000, 9999);

        // $otp = 9988;
        $otp_id = $this->Users_model->InsertOTP($mobile, $otp);
        $msg = "Yout OTP code is : ".$otp;
        // Send_SMS($mobile,$msg);
        Send_OTP($mobile, $otp);
        $data['message'] = 'Success';
        $data['otp_id'] = $otp_id;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
        // }
    }

    public function register_post()
    {
        // if($this->Users_model->OTPConfirm($this->data['otp_id'], $this->data['otp'], $this->data['mobile']) || $this->data['otp']==$this->Setting_model->Setting()->default_otp)
        if ($this->Users_model->OTPConfirm($this->data['otp_id'], $this->data['otp'], $this->data['mobile']) || $this->data['otp']==DEFAULT_OTP) {
            $token = md5(uniqid(rand(), true));
            $user = $this->Users_model->UserProfileByMobile($this->data['mobile']);
            if ($user) {
                if ($user[0]->status==1) {
                    $data['message'] = 'You are blocked, Please contact to admin';
                    $data['code'] = HTTP_NOT_FOUND;
                    $this->response($data, HTTP_OK);
                    exit();
                }

                $this->Users_model->UpdateToken($user[0]->id, $token);
                $data['message'] = 'Mobile Already Exist';
                $data['user'] = $user;
                $data['token'] = $token;
                $data['code'] = 201;
                $this->response($data, HTTP_OK);
                exit();
            } else {
                $referral_user = array();
                if (!empty($this->data['referral_code'])) {
                    $referral_user = $this->Users_model->IsValidReferral($this->data['referral_code']);
                    if (empty($referral_user)) {
                        $data['message'] = 'Referral Code is Not Valid';
                        $data['code'] = HTTP_NOT_FOUND;
                        $this->response($data, HTTP_OK);
                        exit();
                    }
                }

                $profile_pic = '';

                if (!empty($this->data['profile_pic'])) {
                    $img = $this->data['profile_pic'];
                    $img = str_replace(' ', '+', $img);
                    $img_data = base64_decode($img);
                    $profile_pic = uniqid().'.jpg';
                    $file = './data/post/'.$profile_pic;
                    file_put_contents($file, $img_data);
                }

                $gender = (strtolower(trim($this->input->post('gender')))=='female') ? 'f' : 'm';
                $setting = $this->Users_model->Setting();
                $user_id = $this->Users_model->RegisterUser($this->data['mobile'], $this->data['name'], $profile_pic, $gender, $token, $this->input->post('password'), $setting->bonus_amount);
                $this->Users_model->UpdateReferralCode($user_id, $setting->referral_id);
                if (!empty($referral_user)) {
                    $this->Users_model->UpdateWallet($referral_user[0]->id, $setting->referral_amount, $user_id);
                }
                $data['message'] = 'Success';
                $data['user_id'] = $user_id;
                $data['token'] = $token;
                $data['code'] = HTTP_OK;
                $this->response($data, HTTP_OK);
                exit();
            }
        } else {
            $data['message'] = 'OTP Not Matched';
            $data['code'] = HTTP_NOT_FOUND;
            $this->response($data, HTTP_OK);
            exit();
        }
    }

    public function email_login_post()
    {
        // if($this->Users_model->OTPConfirm($this->data['otp_id'], $this->data['otp'], $this->data['mobile']) || $this->data['otp']==$this->Setting_model->Setting()->default_otp)
        // {
        $token = md5(uniqid(rand(), true));
        $user = $this->Users_model->UserProfileByEmail($this->data['email']);
        if ($user) {
            if ($user[0]->status==1) {
                $data['message'] = 'You are blocked, Please contact to admin';
                $data['code'] = HTTP_NOT_FOUND;
                $this->response($data, HTTP_OK);
                exit();
            }

            $this->Users_model->UpdateToken($user[0]->id, $token);
            $data['message'] = 'Email Already Exist';
            $data['user'] = $user;
            $data['token'] = $token;
            $data['code'] = 201;
            $this->response($data, HTTP_OK);
            exit();
        } else {
            $setting = $this->Users_model->Setting();
            $referral_user = array();
            if (!empty($this->data['referral_code'])) {
                $referral_user = $this->Users_model->IsValidReferral($this->data['referral_code']);
                if (empty($referral_user)) {
                    $data['message'] = 'Referral Code is Not Valid';
                    $data['code'] = HTTP_NOT_FOUND;
                    $this->response($data, HTTP_OK);
                    exit();
                }
            }

            $profile_pic = '';

            if (!empty($this->data['profile_pic'])) {
                $img = $this->data['profile_pic'];
                $img = str_replace(' ', '+', $img);
                $img_data = base64_decode($img);
                $profile_pic = uniqid().'.jpg';
                $file = './data/post/'.$profile_pic;
                file_put_contents($file, $img_data);
            }

            $gender = (strtolower(trim($this->input->post('gender')))=='female') ? 'f' : 'm';
            $user_id = $this->Users_model->RegisterUserEmail($this->data['email'], $this->data['name'], $this->data['source'], $profile_pic, $gender, $token);
            $this->Users_model->UpdateReferralCode($user_id, $setting->referral_id);
            if (!empty($referral_user)) {
                $this->Users_model->UpdateWallet($referral_user[0]->id, $setting->referral_amount, $user_id);
            }
            $data['message'] = 'Success';
            $data['user_id'] = $user_id;
            $data['token'] = $token;
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        }
        // }
        // else
        // {
        //     $data['message'] = 'OTP Not Matched';
        //     $data['code'] = HTTP_NOT_FOUND;
        //     $this->response($data, HTTP_OK);
        //     exit();
        // }
    }

    public function login_post()
    {
        $user = $this->Users_model->LoginUser($this->data['mobile'], $this->data['password']);
        if ($user) {
            if ($user[0]->status==1) {
                $data['message'] = 'You are blocked, Please contact to admin';
                $data['code'] = HTTP_NOT_FOUND;
                $this->response($data, HTTP_OK);
                exit();
            }

            $token = md5(uniqid(rand(), true));
            $this->Users_model->UpdateToken($user[0]->id, $token);
            $user = $this->Users_model->LoginUser($this->data['mobile'], $this->data['password']);
            $data['message'] = 'Success';
            $data['user_data'] = $user;
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        } else {
            if ($this->Users_model->UserProfileByMobile($this->data['mobile'])) {
                $data['message'] = 'Incorrect Password';
                $data['code'] = 408;
                $this->response($data, HTTP_OK);
                exit();
            } else {
                $data['message'] = 'User Not Found With This Mobile Number';
                $data['code'] = HTTP_NOT_FOUND;
                $this->response($data, HTTP_OK);
                exit();
            }
        }
    }

    public function forgot_password_post()
    {
        $user_data = $this->Users_model->UserProfileByMobile($this->data['mobile']);
        if ($user_data) {
            // $msg = "Your Password is ".$user_data[0]->password.", Keep Playing Teen Patti.";
            // Send_SMS($this->data['mobile'], $msg);
            Send_OTP($this->data['mobile'], $user_data[0]->password);
            $data['message'] = 'Password Sent.';
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        } else {
            $data['message'] = 'User Not Found With This Mobile Number';
            $data['code'] = HTTP_NOT_FOUND;
            $this->response($data, HTTP_OK);
            exit();
        }
    }

    public function profile_post()
    {
        if (!$this->Users_model->TokenConfirm($this->data['id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $fcm = $this->input->post('fcm');

        if (!empty($fcm)) {
            $this->Users_model->UpdateUser($this->data['id'], $fcm);
        }

        $app_version = $this->input->post('app_version');
        if (!empty($app_version)) {
            $this->Users_model->UpdateAppVersion($this->data['id'], $app_version);
        }
        $UserData = $this->Users_model->UserProfile($this->data['id']);
        $UserKyc = $this->Users_model->UserKyc($this->data['id']);
        $UserBankDetails = $this->Users_model->UserBankDetails($this->data['id']);
        $setting = $this->Setting_model->Setting('`min_redeem`, `referral_amount`, `contact_us`, `terms`, `privacy_policy`, `help_support`, `game_for_private`, `app_version`, `joining_amount`, `whats_no`, `bonus`, `payment_gateway`, `symbol`, `razor_api_key`, `cashfree_client_id`,`cashfree_stage`, `paytm_mercent_id`, `payumoney_key`, `share_text`, `bank_detail_field`, `adhar_card_field`, `upi_field`, `referral_link`, `referral_id`,`app_message`,`upi_merchant_id`,`upi_secret_key`,`admin_commission`,`upi_id`,`extra_spinner`');

        $avatar[] = 'f_1.png';
        $avatar[] = 'f_2.png';
        $avatar[] = 'm_1.png';
        $avatar[] = 'm_2.png';
        $avatar[] = 'm_3.png';
        $avatar[] = 'm_4.png';
        $avatar[] = 'm_5.png';
        $avatar[] = 'm_6.png';
        $avatar[] = 'm_7.png';
        $avatar[] = 'm_8.png';
        $avatar[] = 'm_9.png';
        $avatar[] = 'm_10.png';

        $data['message'] = 'Success';
        $data['user_data'] = $UserData;
        $data['user_kyc'] = $UserKyc;
        $data['user_bank_details'] = $UserBankDetails;
        $data['avatar'] = $avatar;
        $data['setting'] = $setting;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function withdrawal_log_post()
    {
        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $this->load->model('WithdrawalLog_model');
        $UserData = $this->WithdrawalLog_model->WithDrawal_log($this->data['user_id']);
        $data['message'] = 'Success';
        $data['data'] = $UserData;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function game_on_off_post()
    {
        $setting = $this->Setting_model->GetPermission('*');

        $data['message'] = 'Success';
        $data['game_setting'] = $setting;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function leaderboard_post()
    {
        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $leaderboard = $this->Game_model->Leaderboard();

        $data['message'] = 'Success';
        $data['leaderboard'] = $leaderboard;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function setting_post()
    {
        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $setting = $this->Setting_model->Setting();

        $data['message'] = 'Success';
        $data['setting'] = $setting;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function list_post()
    {
        $Users = $this->Users_model->AllUserList();
        if ($Users) {
            $data = [
                'List' => $Users,
                'message' => 'Success',
                'code' => HTTP_OK,
            ];
            $this->response($data, HTTP_OK);
        } else {
            $data = [
                'message' => 'Please try after sometime',
                'code' => HTTP_NOT_FOUND,
            ];
            $this->response($data, HTTP_OK);
        }
    }

    public function bot_post()
    {
        $Users = $this->Users_model->GetFreeBot();
        if ($Users) {
            $data = [
                'List' => $Users,
                'message' => 'Success',
                'code' => HTTP_OK,
            ];
            $this->response($data, HTTP_OK);
        } else {
            $data = [
                'message' => 'Please try after sometime',
                'code' => HTTP_NOT_FOUND,
            ];
            $this->response($data, HTTP_OK);
        }
    }

    public function winning_history_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            // 'GameWins' => $this->Users_model->View_Wins($user_id),
            // 'TeenPattiGameLog' => $this->Users_model->TeenPattiLog($user_id),
            // 'RummyGameLog' => $this->Users_model->RummyLog($user_id),
            'AllPurchase' => $this->Users_model->View_AllPurchase($user_id),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function winning_game_history_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameWins' => $this->Users_model->View_Wins($user_id),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function teenpatti_gamelog_history_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'TeenPattiGameLog' => $this->Users_model->TeenPattiLog($user_id),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function rummy_gamelog_history_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'RummyGameLog' => $this->Users_model->RummyLog($user_id),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->WalletAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_gamelog_history_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->WalletAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_dragon_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->DragonWalletAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_rummy_deal_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->RummyDealLog($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_rummy_pool_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->RummyPoolLog($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_seven_up_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->SevenUpAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_color_prediction_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->ColorPredictionAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_car_roulette_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->CarRouletteAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_animal_roulette_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->AnimalRouletteAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_jackpot_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->JackpotAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_head_tail_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->HeadTailAmount($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_red_black_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'GameLog' => $this->Users_model->RedBlack($user_id),
            'MinRedeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function wallet_history_all_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $GameLog = $this->Users_model->GetAllLogs($user_id);
        $total = !empty($GameLog[0]->user_wallet) ? $GameLog[0]->user_wallet : 0;
        foreach ($GameLog as $key => $value) {
            $amount = $value->user_amount-$value->amount;
            $GameLog[$key]->bracket_amount = $amount;
            $GameLog[$key]->total = $total;
            $total=$total-$amount;
        }

        $data = [
            'GameLog' => $GameLog,
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function min_amount_post()
    {
        $user_id = $this->input->post('user_id');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data = [
            'Wallet' => $user[0]->wallet,
            'Min_Redeem' => min_redeem(),
            'message' => 'Success',
            'code' => HTTP_OK,
        ];
        $this->response($data, HTTP_OK);
    }

    public function check_adhar_post()
    {
        $user_id = $this->input->post('user_id');


        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }
        $adhar = $this->Users_model->getAdhar($user_id);
        if ($adhar=='') {
            $data['message'] = '0';
            $data['code'] = 200;
            $this->response($data, 200);
            exit();
        } else {
            $data['message'] = '1';
            $data['code'] = 200;
            $this->response($data, 200);
            exit();
        }
    }

    public function update_profile_post()
    {
        $user_id = $this->input->post('user_id');
        $name = $this->input->post('name');
        $bank_detail = $this->input->post('bank_detail');
        $adhar_card = $this->input->post('adhar_card');
        $upi = $this->input->post('upi');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        // $img = $profile_pic;
        // $img = str_replace(' ', '+', $img);
        // $img_data = base64_decode($img);
        // $profile_pic_name = uniqid().'.jpg';
        // $file = './data/post/'.$profile_pic_name;
        // file_put_contents($file, $img_data);

        $profile_pic = '';
        if (!empty($this->data['profile_pic'])) {
            $img = $this->data['profile_pic'];
            $img = str_replace(' ', '+', $img);
            $img_data = base64_decode($img);
            $profile_pic = uniqid().'.jpg';
            $file = './data/post/'.$profile_pic;
            file_put_contents($file, $img_data);
        }

        if (!empty($this->input->post('avatar'))) {
            $profile_pic = $this->data['avatar'];
        }

        $this->Users_model->UpdateUserPic($user_id, $name, $profile_pic, $bank_detail, $adhar_card, $upi);
        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function change_password_post()
    {
        $user_id = $this->input->post('user_id');
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if ($user[0]->password!=$old_password) {
            $data['message'] = 'Invalid Old Password';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $user_data['password'] = $new_password;

        $this->Users_model->Update($user_id, $user_data);
        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function update_bank_details_post()
    {
        $user_id = $this->input->post('user_id');
        $bank_name = $this->input->post('bank_name');
        $ifsc_code = $this->input->post('ifsc_code');
        $acc_holder_name = $this->input->post('acc_holder_name');
        $acc_no = $this->input->post('acc_no');
        $passbook_img = $this->input->post('passbook_img');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!empty($passbook_img)) {
            $img = $passbook_img;
            $img = str_replace(' ', '+', $img);
            $img_data = base64_decode($img);
            $passbook = uniqid().'.jpg';
            $file = './data/post/'.$passbook;
            file_put_contents($file, $img_data);
            $update_data['passbook_img'] = $passbook;
        }

        $update_data['bank_name'] = $bank_name;
        $update_data['ifsc_code'] = $ifsc_code;
        $update_data['acc_holder_name'] = $acc_holder_name;
        $update_data['acc_no'] = $acc_no;

        $user_bank_details = $this->Users_model->UserBankDetails($user_id);
        if ($user_bank_details) {
            $this->Users_model->UpdateUserBankDetails($user_id, $update_data);
        } else {
            $update_data['user_id'] = $user_id;
            $this->Users_model->InsertUserBankDetails($update_data);
        }

        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function update_kyc_post()
    {
        $user_id = $this->input->post('user_id');
        $pan_no = $this->input->post('pan_no');
        $pan_img = $this->input->post('pan_img');
        $aadhar_no = $this->input->post('aadhar_no');
        $aadhar_img = $this->input->post('aadhar_img');

        if (empty($user_id)) {
            $data['message'] = 'Invalid Params';
            $data['code'] = HTTP_BLANK;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($user_id);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!empty($pan_img)) {
            $img = $pan_img;
            $img = str_replace(' ', '+', $img);
            $img_data = base64_decode($img);
            $pan = uniqid().'pan.jpg';
            $file = './data/post/'.$pan;
            file_put_contents($file, $img_data);
            $update_data['pan_img'] = $pan;
        }

        if (!empty($aadhar_img)) {
            $img = $aadhar_img;
            $img = str_replace(' ', '+', $img);
            $img_data = base64_decode($img);
            $aadhar = uniqid().'.jpg';
            $file = './data/post/'.$aadhar;
            file_put_contents($file, $img_data);
            $update_data['aadhar_img'] = $aadhar;
        }

        $update_data['pan_no'] = $pan_no;
        $update_data['aadhar_no'] = $aadhar_no;

        $user_kyc = $this->Users_model->UserKyc($user_id);
        if ($user_kyc) {
            $update_data['status'] = 0;
            $this->Users_model->UpdateUserKyc($user_id, $update_data);
        } else {
            $update_data['user_id'] = $user_id;
            $this->Users_model->InsertUserKyc($update_data);
        }

        $data['message'] = 'Success';
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }

    public function welcome_bonus_post()
    {
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $WelcomeBonus = $this->Users_model->WelcomeBonus();
        if ($WelcomeBonus) {
            $bonus_log = $this->Users_model->WelcomeBonusLog($this->data['user_id']);

            $data['message'] = 'Success';
            $data['collected_days'] = count($bonus_log);
            $data['welcome_bonus'] = $WelcomeBonus;
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
            exit();
        }

        $data['message'] = 'Invalid Bonus';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function collect_welcome_bonus_post()
    {
        if (empty($this->data['user_id'])) {
            $data['message'] = 'Invalid Parameter';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        if (!$this->Users_model->TokenConfirm($this->data['user_id'], $this->data['token'])) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_INVALID;
            $this->response($data, HTTP_OK);
            exit();
        }

        $user = $this->Users_model->UserProfile($this->data['user_id']);
        if (empty($user)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $WelcomeBonus = $this->Users_model->WelcomeBonus();

        $bonus_log = $this->Users_model->WelcomeBonusLog($this->data['user_id']);
        if (empty($bonus_log)) {
            if ($WelcomeBonus[0]->game_played<=$user[0]->game_played) {
                $this->Users_model->AddWelcomeBonus($WelcomeBonus[0]->coin, $this->data['user_id']);
                // $setting = $this->Setting_model->Setting();
                // for ($i=1; $i <= 3; $i++) {
                //     if ($user[0]->referred_by!=0) {
                //         $level = 'level_'.$i;
                //         $coins = (($WelcomeBonus[0]->coin*$setting->$level)/100);
                //         $this->Users_model->UpdateWalletOrder($coins, $user[0]->referred_by);

                //         $log_data = [
                //             'user_id' => $user[0]->referred_by,
                //             'day' => $WelcomeBonus[0]->id,
                //             'bonus_user_id' => $this->data['user_id'],
                //             'coin' => $coins,
                //             'added_date' => date('Y-m-d H:i:s'),
                //             'level' => $i,
                //         ];

                //         $this->Users_model->AddWelcomeReferLog($log_data);
                //         $user = $this->Users_model->UserProfile($user[0]->referred_by);
                //     } else {
                //         break;
                //     }
                // }
                $data['message'] = 'Success';
                $data['coin'] = $WelcomeBonus[0]->coin;
                $data['code'] = HTTP_OK;
                $this->response($data, HTTP_OK);
                exit();
            }

            $data['message'] = 'You Have To Play '.($WelcomeBonus[0]->game_played-$user[0]->game_played).' More Games to Collect Bonus';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        } else {
            $last_date = $bonus_log[0]->date;

            if (strtotime($last_date)<strtotime(date('Y-m-d'))) {
                $collected_days = count($bonus_log);
                if ($WelcomeBonus[$collected_days]->game_played<=$user[0]->game_played) {
                    $this->Users_model->AddWelcomeBonus($WelcomeBonus[$collected_days]->coin, $this->data['user_id']);

                    $setting = $this->Setting_model->Setting();
                    for ($i=1; $i <= 3; $i++) {
                        if ($user[0]->referred_by!=0) {
                            $level = 'level_'.$i;
                            $coins = (($WelcomeBonus[$collected_days]->coin*$setting->$level)/100);
                            $this->Users_model->UpdateWalletOrder($coins, $user[0]->referred_by);

                            $log_data = [
                                'user_id' => $user[0]->referred_by,
                                'day' => $WelcomeBonus[$collected_days]->id,
                                'bonus_user_id' => $this->data['user_id'],
                                'coin' => $coins,
                                'added_date' => date('Y-m-d H:i:s'),
                                'level' => $i,
                            ];

                            $this->Users_model->AddWelcomeReferLog($log_data);
                            $user = $this->Users_model->UserProfile($user[0]->referred_by);
                        } else {
                            break;
                        }
                    }

                    $data['message'] = 'Success';
                    $data['coin'] = $WelcomeBonus[$collected_days]->coin;
                    $data['code'] = HTTP_OK;
                    $this->response($data, HTTP_OK);
                    exit();
                }

                $data['message'] = 'You Have To Play '.($WelcomeBonus[$collected_days]->game_played-$user[0]->game_played).' More Games to Collect Bonus';
                $data['code'] = HTTP_NOT_ACCEPTABLE;
                $this->response($data, 200);
                exit();
            }

            $data['message'] = "Today's Bonus Already Collected";
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, 200);
            exit();
        }

        $data['message'] = 'Invalid Bonus';
        $data['code'] = HTTP_NOT_ACCEPTABLE;
        $this->response($data, 200);
        exit();
    }

    public function user_category_post()
    {
        $this->load->model('UserCategory_model');

        $user_category = $this->UserCategory_model->AllTableMasterList();

        $data['message'] = 'Success';
        $data['user_category'] = $user_category;
        $data['code'] = HTTP_OK;
        $this->response($data, HTTP_OK);
        exit();
    }
}