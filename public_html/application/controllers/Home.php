<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Setting_model');
        $this->load->model('Banner_model');
    }

    public function index()
    {
        $data = [
            'title' => 'Home',
            'banner' => $this->Banner_model->view(),
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/index', $data);
    }

    public function download()
    {
        $data = [
            'title' => 'Downlaod',
            'banner' => $this->Banner_model->view(),
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/download', $data);
    }

    public function faq()
    {
        $data = [
            'title' => 'FAQ',
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/faq', $data);
    }

    public function about_us()
    {
        $data = [
            'title' => 'About Us',
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/about-us', $data);
    }

    public function refund_policy()
    {
        $data = [
            'title' => 'Refund Policy',
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/refund-policy', $data);
    }

    public function privacy_policy()
    {
        $data = [
            'title' => 'Privacy Policy',
            'Setting' => $this->Setting_model->Setting(),
        ];

        website('website/privacy', $data);
    }

    public function terms_conditions()
    {
        $data = [
            'title' => 'Terms & Conditions',
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/t-and-c', $data);
    }

    public function security()
    {
        $data = [
            'title' => 'Security',
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/security', $data);
    }

    public function contact_us()
    {
        $data = [
            'title' => 'Contact us',
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/Contact', $data);
    }

    public function download2()
    {
        $data = [
            'title' => 'Download',
            'banner' => $this->Banner_model->view(),
            'Setting' => $this->Setting_model->Setting(),
        ];
        website('website/download-2', $data);
    }
}
