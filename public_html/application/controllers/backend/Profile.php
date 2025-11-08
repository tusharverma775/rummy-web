<?php
class Profile extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('Users_model'));
    }


    public function add()
    {
		$id = $this->session->userdata()['admin_id'];
        $data = [
            'title' => 'Update Profile',
            'admin'=> $this->Users_model->Setting()
        ];
        template('profile/add', $data);
    }

    public function update()
    {
        $id=$this->input->post('id');
        if(!empty($this->input->post('password')))
        {
            $data = [
                'sw_password' => md5($this->input->post('password')),
                'password' => $this->input->post('password'),
            ];
        }

        $data['first_name'] = $this->input->post('name');
        $Update = $this->Users_model->UpdateSetting($id,$data);
        
        if ($Update) {
            $this->session->set_flashdata('msg', array('message' => 'Password Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/Profile/add');
    }

}