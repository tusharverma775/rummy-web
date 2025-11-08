<?php
class RummyTableMaster extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['RummyTableMaster_model']);
    }

    public function index()
    {
        $data = [
            'title' => 'Point Table Master Management',
            'AllRummyTableMaster' => $this->RummyTableMaster_model->AllTableMasterList()
        ];
        $data['SideBarbutton'] = ['backend/RummyTableMaster/add', 'Add Point Table Master'];
        template('rummy_table_master/index', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Point Table Master'
        ];

        template('rummy_table_master/add', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Point Table Master',
            'RummyTableMaster' => $this->RummyTableMaster_model->ViewTableMaster($id)
        ];

        template('rummy_table_master/edit', $data);
    }

    public function delete($id)
    {
        if ($this->RummyTableMaster_model->Delete($id)) {
            $this->session->set_flashdata('msg', array('message' => 'Point Table Master Removed Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/RummyTableMaster');
    }

    public function insert()
    {
        $data = [
            'boot_value' => $this->input->post('boot_value'),
            'point_value' => $this->input->post('point_value'),
            'added_date' => date('Y-m-d H:i:s')
        ];
        $RummyTableMaster = $this->RummyTableMaster_model->AddTableMaster($data);
        if ($RummyTableMaster) {
            $this->session->set_flashdata('msg', array('message' => 'Point Table Master Added Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/RummyTableMaster');
    }

    public function update()
    {
        $data = [
            'boot_value' => $this->input->post('boot_value'),
            'point_value' => $this->input->post('point_value'),
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $RummyTableMaster = $this->RummyTableMaster_model->UpdateTableMaster($data, $this->input->post('id'));
        if ($RummyTableMaster) {
            $this->session->set_flashdata('msg', array('message' => 'Point Table Master Wallet Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/RummyTableMaster');
    }

}