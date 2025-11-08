<?php
class DealTableMaster extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['RummyDealTableMaster_model']);
    }

    public function index()
    {
        $data = [
            'title' => 'Deal Table Master Management',
            'AllDealTableMaster' => $this->RummyDealTableMaster_model->AllTableMasterList()
        ];
        $data['SideBarbutton'] = ['backend/DealTableMaster/add', 'Add Deal Table Master'];
        template('deal_table_master/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Deal Table Master'
        ];

        template('deal_table_master/add', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Deal Table Master',
            'DealTableMaster' => $this->RummyDealTableMaster_model->ViewTableMaster($id)
        ];

        template('deal_table_master/edit', $data);
    }

    public function delete($id)
    {
        if ($this->RummyDealTableMaster_model->Delete($id)) {
            $this->session->set_flashdata('msg', array('message' => 'Deal Table Master Removed Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/DealTableMaster');
    }

    public function insert()
    {
        $data = [
            'boot_value' => $this->input->post('boot_value'),
            'maximum_blind' => 4,
            'chaal_limit' => $this->input->post('chaal_limit'),
            'pot_limit' => $this->input->post('pot_limit'),
            'added_date' => date('Y-m-d H:i:s')
        ];
        $DealTableMaster = $this->RummyDealTableMaster_model->AddTableMaster($data);
        if ($DealTableMaster) {
            $this->session->set_flashdata('msg', array('message' => 'Deal Table Master Added Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/DealTableMaster');
    }

    public function update()
    {
        $data = [
            'boot_value' => $this->input->post('boot_value'),
            'maximum_blind' => 4,
            'chaal_limit' => $this->input->post('chaal_limit'),
            'pot_limit' => $this->input->post('pot_limit'),
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $DealTableMaster = $this->RummyDealTableMaster_model->UpdateTableMaster($data, $this->input->post('id'));
        if ($DealTableMaster) {
            $this->session->set_flashdata('msg', array('message' => 'Deal Table Master Wallet Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/DealTableMaster');
    }

}