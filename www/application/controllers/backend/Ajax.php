<?php
class Ajax extends MY_Controller
{
    public function __construct() {
        parent::__construct();
        $this->load->model([
            'Ajax_model',
            'Post_model'
        ]);
    }

    public function GetCity()
    {
        $id = $this->input->post('state_id');
        $data = $this->Post_model->AllCity($id);
        
        echo json_encode($data);
    }

    public function DeleteRecord()
    {
        $RecordId = $this->url_encrypt->decode($this->input->post('id'));
        $Table = $this->url_encrypt->decode($this->input->post('type'));
        $DeleteRecord = $this->Ajax_model->DeleteRecord($RecordId,$Table);
        if($DeleteRecord)
        {
            echo 'Record Deleted Successfully';
        }else{
            echo 'Something Went Wrong';
        }
    }

    public function CheckExisting()
    {
        $Data = $_POST;
        $id = $this->url_encrypt->decode($this->input->post('id'));
        $type = $this->url_encrypt->decode($this->input->post('type'));
        $Columename = key($Data);
        $ColumeValue = reset($Data);
        $Check = $this->Ajax_model->Check_exsiting($id,$type,$Columename,$ColumeValue);
        if($Check){
            echo 'false';
        }else{
            echo 'true';
        }
    }
}
