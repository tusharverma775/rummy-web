<?php
class Ajax_model extends MY_Model
{
    public function DeleteRecord($RecordId,$Table)
    {
        $data =[
            'isDeleted'=>TRUE,
            'updated_date'=>date("Y-m-d H:i:s")
        ];
        $this->db->where('id',$RecordId);
        $this->db->update($Table,$data);
        return $this->db->last_query();
    }

    public function Check_exsiting($id,$type,$Columename,$ColumeValue)
    {
        $this->db->where('isDeleted',FALSE);
        if($id){
            $this->db->where('id!=',$id);
        }
        $this->db->where($Columename,$ColumeValue);
        $Query  = $this->db->get($type);
        return $Query->num_rows();
    }
}
