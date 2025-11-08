<?php
class Redeem extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
    }

    public function index()
    {
        $data = [
            'title' => 'Redeem',
            'AllRedeem' => $this->Users_model->AllRedeemList()
        ];

        template('redeem/list', $data);
    }

    public function ChangeOrderStatus()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        $Change = $this->Users_model->ChangeRedeemStatus($id, $status);
        if ($Change) {
            $this->session->set_flashdata('message', array('message' => 'Status Change Successfully', 'class' => 'success'));
        } else {
            $this->session->set_flashdata('message', array('message' => 'Something went to wrong', 'class' => 'success'));
        }
    }
}