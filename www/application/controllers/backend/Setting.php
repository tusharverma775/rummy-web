<?php

class Setting extends MY_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model( 'Setting_model' );
    }

    public function index() {
        $data = [
            'title' => 'Setting',
            'Setting' => $this->Setting_model->Setting(),
            'Permission' => $this->Setting_model->GetPermission()
        ];

        template( 'setting/index', $data );
    }

    public function edit() {
        $data = [
            'title' => 'Edit Setting',
            'Setting' => $this->Setting_model->Setting(),
        ];

        template( 'setting/edit', $data );
    }

    public function update() {
        $referral_amount = $this->input->post( 'referral_amount' );
        $mobile = $this->input->post( 'mobile' );
        $level_1 = $this->input->post( 'level_1' );
        $level_2 = $this->input->post( 'level_2' );
        $level_3 = $this->input->post( 'level_3' );
        $referral_id = $this->input->post( 'referral_id' );
        $referral_link = $this->input->post( 'referral_link' );
        $contact_us = $this->input->post( 'contact_us' );
        $about_us = $this->input->post( 'about_us' );
        $refund_policy = $this->input->post( 'refund_policy' );
        $terms = $this->input->post( 'terms' );
        $privacy_policy = $this->input->post( 'privacy_policy' );
        $help_support = $this->input->post( 'help_support' );
        $default_otp = $this->input->post( 'default_otp' );
        $game_for_private = $this->input->post( 'game_for_private' );
        $app_version = $this->input->post( 'app_version' );
        $joining_amount = $this->input->post( 'joining_amount' );
        $admin_commission = $this->input->post( 'admin_commission' );
        $whats_no = $this->input->post( 'whats_no' );
        $upi_merchant_id = $this->input->post( 'upi_merchant_id' );
        $upi_secret_key = $this->input->post( 'upi_secret_key' );
        $bonus = $this->input->post( 'bonus' );
        $bonus_amount = $this->input->post( 'bonus_amount' );
        $payment_gateway = $this->input->post( 'payment_gateway' );
        $symbol = $this->input->post( 'symbol' );
        $razor_api_key = $this->input->post( 'razor_api_key' );
        $payumoney_key = $this->input->post( 'payumoney_key' );
        $payumoney_salt = $this->input->post( 'payumoney_salt' );
        $razor_secret_key = $this->input->post( 'razor_secret_key' );
        $cashfree_client_id = $this->input->post( 'cashfree_client_id' );
        $cashfree_client_secret = $this->input->post( 'cashfree_client_secret' );
        $cashfree_stage = $this->input->post( 'cashfree_stage' );
        $paytm_mercent_id = $this->input->post( 'paytm_mercent_id' );
        $paytm_mercent_key = $this->input->post( 'paytm_mercent_key' );
        $share_text = $this->input->post( 'share_text' );
        $bank_detail_field = $this->input->post( 'bank_detail_field' );
        $adhar_card_field = $this->input->post( 'adhar_card_field' );
        $upi_field = $this->input->post( 'upi_field' );
        $app_message = $this->input->post( 'app_message' );
        $upi_id = $this->input->post( 'upi_id' );
        if ( !empty( $_FILES[ 'app_url' ][ 'name' ] ) ) {
            $app_url = upload_apk( $_FILES[ 'app_url' ], APP_URL );
        } else {
            $app_url = '';
        }
        if ( !empty( $_FILES[ 'logo' ][ 'name' ] ) ) {
            $logo = upload_image( $_FILES[ 'logo' ], LOGO );
        } else {
            $logo = '';
        }
        $UpdateProduct = $this->Setting_model->update( $mobile, $referral_amount, $level_1, $level_2, $level_3, $referral_id, $referral_link, $contact_us, $terms, $privacy_policy, $help_support, $default_otp, $game_for_private, $app_version, $joining_amount, $admin_commission, $whats_no, $bonus, $bonus_amount, $payment_gateway, $symbol, $razor_api_key, $razor_secret_key, $cashfree_client_id, $cashfree_client_secret, $cashfree_stage, $paytm_mercent_id, $paytm_mercent_key, $share_text, $bank_detail_field, $adhar_card_field, $upi_field, $about_us, $refund_policy, $app_message, $app_url, $logo, $payumoney_key, $payumoney_salt, $upi_merchant_id, $upi_secret_key, $upi_id );
        if ( $UpdateProduct ) {
            $this->session->set_flashdata( 'msg', array( 'message' => 'Setting Updated Successfully', 'class' => 'success', 'position' => 'top-right' ) );
        } else {
            $this->session->set_flashdata( 'msg', array( 'message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right' ) );
        }
        redirect( 'backend/setting' );
    }

    public function AdminCoin_log() {
        $data = [
            'title' => 'Admin Coin Log',
            'AllTipLog' => $this->Setting_model->AllTipLog(),
            'AllCommissionLog' => $this->Setting_model->AllCommissionLog(),
        ];
        template( 'setting/AdminCoin_log', $data );
    }

    public function ChangeJackpotStatus() {
        $status = $this->input->post( 'status' );

        $Change = $this->Setting_model->update_jackpot_status( $status );
        if ( $Change ) {
            $this->session->set_flashdata( 'message', array( 'message' => 'Status Change Successfully', 'class' => 'success' ) );
        } else {
            $this->session->set_flashdata( 'message', array( 'message' => 'Something went to wrong', 'class' => 'success' ) );
        }
        echo 'true';
    }

    public function ChangeRummyBotStatus() {
        $status = $this->input->post( 'status' );

        $Change = $this->Setting_model->update_rummy_bot_status( $status );
        if ( $Change ) {
            $this->session->set_flashdata( 'message', array( 'message' => 'Status Change Successfully', 'class' => 'success' ) );
        } else {
            $this->session->set_flashdata( 'message', array( 'message' => 'Something went to wrong', 'class' => 'success' ) );
        }
        echo 'true';
    }

    public function ChangeTeenpattiBotStatus() {
        $status = $this->input->post( 'status' );

        $Change = $this->Setting_model->update_teenpatti_bot_status( $status );
        if ( $Change ) {
            $this->session->set_flashdata( 'message', array( 'message' => 'Status Change Successfully', 'class' => 'success' ) );
        } else {
            $this->session->set_flashdata( 'message', array( 'message' => 'Something went to wrong', 'class' => 'success' ) );
        }
        echo 'true';
    }

    public function ChangeGameStatus() {

        $type = $this->input->post( 'type' );
		$Change=false;
		$column=$this->input->post( 'name' );
        switch ( $column ) {
            case 'teen_patti':
            if ( TEENPATTI == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'dragon_tiger':
            if ( DRAGON_TIGER == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'andar_bahar':
            if ( ANDER_BAHAR == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'point_rummy':
            if ( POINT_RUMMY == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'private_rummy':
            if ( POINT_RUMMY == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'pool_rummy':
            if ( RUMMY_POOL == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'deal_rummy':
            if ( RUMMY_DEAL == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'private_table':
            if ( TEENPATTI == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'custom_boot':
            if ( TEENPATTI == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'seven_up_down':
            if ( SEVEN_UP_DOWN == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'car_roulette':
            if ( CAR_ROULETTE == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'jackpot_teen_patti':
            if ( TEENPATTI == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'animal_roulette':
            if ( ANIMAL_ROULETTE == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'color_prediction':
            if ( COLOR_PREDICTION == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'poker':
            if ( POKER == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'head_tails':
            if ( HEAD_TAILS == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'red_vs_black':
            if ( RED_VS_BLACK == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'ludo_online':
            if ( LUDO == true ) {
                $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
            }
            break;
            case 'ludo_local':
                if ( LUDO_LOCAL == true ) {
                    $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
                }
                break;

                case 'ludo_computer':
                    if ( LUDO_COMPUTER == true ) {
                        $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
                    }
                    break;
            case 'bacarate':
                if ( BACCARAT == true ) {
                    $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
                }
                break;
                case 'jhandi_munda':
                    if ( JHANDI_MUNDA == true ) {
                        $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
                    }
                    break;
                    case 'roulette':
                        if ( ROULETTE == true ) {
                            $Change = $this->Setting_model->UpdateGamesStatus( $column, $type );
                        }
                        break;
            default:
            $Change = false;
            break;
        }
        if ( $Change ) {
            echo 'true';
        } else {
            echo 'false';
        }

    }

}