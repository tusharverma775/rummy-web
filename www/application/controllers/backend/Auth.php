<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model(array('Auth_model','Setting_model'));
    }

    public function login()
    {
        $data['title'] = 'Sign In';
        $data['Setting'] = $this->Setting_model->Setting();
        $this->load->view('login', $data);
    }

    public function index()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('backend/dashboard');
        }
        // <editor-fold defaultstate="collapsed" desc="login ">
        $data['title'] = 'Sign In';
        $data['Setting'] = $this->Setting_model->Setting();
        if ($this->form_validation->run('login') === false) {
            $this->load->view('login', $data);
        } else {
            // Get email
            $username = $this->input->post('email');
            // Get and encrypt the password
            $password = md5($this->input->post('password'));
            // Login user

            $data = $this->Auth_model->login($username, $password);

            if ($data) {
                // Create session
                $user_data = array(
                    'admin_id' => $data->id,
                    'email' => $data->email_id,
                    'name' => $data->first_name,
                    'logged_in' => true,
                    'role' => $data->role
                );
                $this->session->set_userdata($user_data);
                $this->session->set_flashdata('msg', array('message' => 'You are now logged in', 'class' => 'success', 'position' => 'top-right'));
                if (empty($this->input->post('redirect'))) {
                    redirect('backend/dashboard');
                } else {
                    redirect($this->input->post('redirect'));
                }
            } else {
                $this->session->set_flashdata('msg', array('message' => 'Invalid credentials', 'class' => 'error', 'position' => 'top-right'));
                redirect('backend/auth/login');
            }
        }
        // </editor-fold>
    }

    // Log user out
    public function logout()
    {
        // <editor-fold defaultstate="collapsed" desc="Logout">

        $user_data = array(
            'admin_id' => '',
            'email' => '',
            'name' => '',
            'image' => '',
            'logged_in' => '',
            'role' => '',
        );
        $this->session->unset_userdata($user_data);
        $this->session->sess_destroy();
        redirect('backend/auth');
    }
}
