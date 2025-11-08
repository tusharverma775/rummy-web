<?php

class PoolTableMaster extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['RummyPoolTableMaster_model']);
    }

    public function index()
    {
        $data = [
            'title' => 'Pool Table Master Management',
            'AllPoolTableMaster' => $this->RummyPoolTableMaster_model->AllTableMasterList()
        ];
        $data['SideBarbutton'] = ['backend/PoolTableMaster/add', 'Add Pool Table Master'];
        template('pool_table_master/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Pool Table Master'
        ];

        template('pool_table_master/add', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Pool Table Master',
            'PoolTableMaster' => $this->RummyPoolTableMaster_model->ViewTableMaster($id)
        ];

        template('pool_table_master/edit', $data);
    }

    public function delete($id)
    {
        if ($this->RummyPoolTableMaster_model->Delete($id)) {
            $this->session->set_flashdata('msg', array('message' => 'Pool Table Master Removed Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/PoolTableMaster');
    }

    public function insert()
    {
        $data = [
            'boot_value' => $this->input->post('boot_value'),
            'pool_point' => $this->input->post('pool_point'),
            'added_date' => date('Y-m-d H:i:s')
        ];
        $PoolTableMaster = $this->RummyPoolTableMaster_model->AddTableMaster($data);
        if ($PoolTableMaster) {
            $this->session->set_flashdata('msg', array('message' => 'Pool Table Master Added Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/PoolTableMaster');
    }

    public function update()
    {
        $data = [
            'boot_value' => $this->input->post('boot_value'),
            'pool_point' => $this->input->post('pool_point'),
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $PoolTableMaster = $this->RummyPoolTableMaster_model->UpdateTableMaster($data, $this->input->post('id'));
        if ($PoolTableMaster) {
            $this->session->set_flashdata('msg', array('message' => 'Pool Table Master Wallet Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/PoolTableMaster');
    }
}
