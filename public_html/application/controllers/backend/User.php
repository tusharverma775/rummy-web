<?php
class User extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Users_model', 'Setting_model']);
    }

    public function index()
    {
        $data = [
            'title' => 'User Management',
            // 'AllUser' => $this->Users_model->AllUserList()
        ];
        $data['SideBarbutton'] = ['backend/user/add', 'Add Boat'];
        template('user/list', $data);
    }


    public function add()
    {
        $data = [
            'title' => 'Add Bot'
        ];

        template('user/add', $data);
    }

    public function sendNotification()
    {
        $userdata = $this->Users_model->AllUserList();

        foreach ($userdata as $key => $value) {
            if (!empty($value->fcm)) {
                $data['msg'] = "you can buy ticket ";
                $data['title'] = "new game";
                push_notification_android($value->fcm, $data);
            }
        }
    }

    public function GetUsers()
    {
        // error_reporting(-1);
        // ini_set('display_errors', 1);
        // POST data
        $postData = $this->input->post();

        // Get data
        $data = $this->Users_model->GetUsers($postData);

        echo json_encode($data);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Add Wallet Amount',
            'User' => $this->Users_model->UserProfile($id)
        ];

        template('user/edit', $data);
    }

    public function GetLadgerReports($id)
    {
        $postData = $this->input->post();
        // Get data
        $data = $this->Users_model->GetLadgerReports($id,$postData);

        echo json_encode($data);
    }

    public function delete($id)
    {
        if ($this->Users_model->Delete($id)) {
            $this->session->set_flashdata('msg', array('message' => 'User Removed Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/user');
    }

    public function insert()
    {
        $data = [
            'name' => $this->input->post('name'),
            'profile_pic' => 'f_' . rand(1, 3) . '.png',
            'wallet' => $this->input->post('wallet'),
            'user_type' => 1,
            'added_date' => date('Y-m-d H:i:s')
        ];
        $user = $this->Users_model->AddBot($data);
        if ($user) {
            $this->session->set_flashdata('msg', array('message' => 'Bot Added Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/user');
    }

    public function update()
    {
        $user = $this->Users_model->UpdateWalletOrder($this->input->post('amount'), $this->input->post('user_id'));
        if ($user) {
            $user = $this->Users_model->WalletLog($this->input->post('amount'), $this->input->post('bonus'), $this->input->post('user_id'));
            $this->session->set_flashdata('msg', array('message' => 'User Wallet Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/user');
    }

    public function update_user()
    {
        $data = [
            'name' => $this->input->post('name'),
            'bank_detail' => $this->input->post('bank_detail'),
            'adhar_card' => $this->input->post('adhar_card'),
            'upi' => $this->input->post('upi'),
            'password' => $this->input->post('password'),
            // 'mobile' => $this->input->post('mobile'),
            'gender' => $this->input->post('gender'),
            'email' => $this->input->post('email'),
        ];
        $profile_pic = '';
            if (!empty($_FILES["profile_pic"]['name'])) {
                
                $ext = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);
                $profile_pic = date("Ymd_Hi")."_".uniqid() . "." . $ext;
                
                $config['upload_path'] = 'data/profile_pic';
                $config['allowed_types'] = 'gif|jpg|png|jpeg|JPEG';
                $config['file_name'] = $profile_pic;
                //$config['max_size'] = '10000';
                //$config['max_width'] = '2000';
                //$config['max_height'] = '2000';
                $this->load->library('upload', $config);
                
                if (!$this->upload->do_upload('profile_pic')) {

                    $error = array('error' => $this->upload->display_errors());
                    // var_dump($error);die; 
                    redirect('backend/user');
                } else {
                    $file = $this->upload->data();
                }
            }
        if($profile_pic != ''){
            $data['profile_pic'] = $profile_pic;
        }
        // print_r($data);die;
        $user = $this->Users_model->Update($this->input->post('user_id'),$data);
        if ($user) {
            $this->session->set_flashdata('msg', array('message' => 'User Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/user');
    }

    public function ChangeStatus()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $Change = $this->Users_model->ChangeStatus($id, $status);
        if ($Change) {
            $this->session->set_flashdata('message', array('message' => 'Status Change Successfully', 'class' => 'success'));
        } else {
            $this->session->set_flashdata('message', array('message' => 'Something went to wrong', 'class' => 'success'));
        }
    }
}