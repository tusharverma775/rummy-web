<?php

use Restserver\Libraries\REST_Controller;

include(APPPATH . '/libraries/REST_Controller.php');
include(APPPATH . '/libraries/Format.php');
class Otp extends REST_Controller
{
    private $data;
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
        if (!isset($header['Content-Type']) && $header['Content-Type']!= 'application/json') {
            $data['message'] = 'Invalid Data';
            $data['code'] = HTTP_UNAUTHORIZED;
            $this->response($data, HTTP_OK);
            exit();
        }
        $this->data = json_decode(file_get_contents('php://input'));

        $this->load->model([
            'Users_model'
        ]);
    }
    public function index_post()
    {
        $MobileNo = $this->data->MobileNo;

        if (empty($MobileNo) || strlen($MobileNo) !== 10) {
            $data['message'] = 'Invalid Mobile Number';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, HTTP_OK);
            exit();
        }
        //$OTP = rand(100000,999999);
        $OTP = 9988;
        $GenerateOTP = $this->Users_model->InsertOTP($MobileNo, $OTP);

        Send_SMS($MobileNo, 'The OTP is '.$OTP);
        // $url = "http://androapps.msg4all.com/GatewayAPI/rest?method=SendMessage&send_to=$MobileNo&msg=Your+Verification+Code+is+%3A-+$OTP&msg_type=TEXT&loginid=Rupee1&auth_scheme=plain&password=nV12IxSoD&v=1.1&format=text";

        // $curl = curl_init();
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_HEADER, false);
        // $strc = curl_exec($curl);
        $strc = true;
        if ($strc) {
            $data['message'] = 'OTP Sent Successfully';
            $data['id'] = $this->url_encrypt->encode($GenerateOTP);
            $data['code'] = HTTP_OK;
            $this->response($data, HTTP_OK);
        } else {
            $data['code'] = HTTP_FORBIDDEN;
            $data['message'] = 'Something Went Wrong';
            $this->response(null, 200);
        }
        // curl_close($curl);
    }
    public function Confirm_post()
    {
        $OTP = $this->data->OTP;
        $id = $this->data->Id;
        $MobileNo = $this->data->MobileNo;
        $FCM = $this->data->fcm;
        if (empty($OTP)) {
            $data['message'] = 'Invalid OTP';
            $data['code'] = HTTP_BLANK;
            $this->response($data, HTTP_OK);
            exit();
        }
        if (empty($MobileNo)) {
            $data['message'] = 'Invalid Mobile Number';
            $data['code'] = HTTP_BLANK;
            $this->response($data, HTTP_OK);
            exit();
        }
        if (empty($FCM)) {
            $data['message'] = 'Invalid Param';
            $data['code'] = HTTP_BLANK;
            $this->response($data, HTTP_OK);
            exit();
        }
        if (empty($id)) {
            $data['message'] = 'Invalid User';
            $data['code'] = HTTP_BLANK;
            $this->response($data, HTTP_OK);
            exit();
        }
        $OTPConfirm = $this->Users_model->OTPConfirm($this->url_encrypt->decode($id), $OTP, $MobileNo);
        if ($OTPConfirm) {
            //check user already registed
            $UserDetails = $this->Users_model->UserByMobile($MobileNo);
            if ($UserDetails) {
                //update token
                UpdateToken($UserDetails->id, $FCM);
                $UserDetails = $this->Users_model->UserByMobile($MobileNo);
                $data=[
                    'UserId'=> $UserDetails->token,
                    'token'=>$this->url_encrypt->encode($UserDetails->id),
                    'message'=>'Success',
                    'AlreadyRegistered'=>true,
                    'code'=>HTTP_OK
                ];
                $this->response($data, HTTP_OK);
            } else {
                // Register User
                $RegisterUser = $this->Users_model->RegisterUser($MobileNo);
                if ($RegisterUser) {
                    UpdateToken($RegisterUser, $FCM);
                    $UserDetails = $this->Users_model->UserByMobile($MobileNo);
                    $data=[
                        'UserId'=> $UserDetails->token,
                        'token'=>$this->url_encrypt->encode($UserDetails->id),
                        'message'=>'Success',
                        'AlreadyRegistered'=>false,
                        'code'=>HTTP_OK
                    ];
                    $this->response($data, HTTP_OK);
                } else {
                    $data=[

                        'message'=>'Technical Error',
                        'code'=>HTTP_NOT_ACCEPTABLE
                    ];
                    $this->response($data, HTTP_OK);
                }
            }
        } else {
            $data['message'] = 'OTP Not Matched1';
            $data['code'] = HTTP_NOT_ACCEPTABLE;
            $this->response($data, HTTP_OK);
        }
    }
}