<?php
class AnderBaherTableMaster_model extends MY_Model
{

    public function AllTableMasterList()
    {
        $this->db->from('tbl_ander_baher_room');
        $this->db->where('isDeleted', false);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function ViewTableMaster($id)
    {
        $Query = $this->db->where('isDeleted', False)
            ->where('id', $id)
            ->get('tbl_ander_baher_room');
        return $Query->row();
    }
    
    public function AddTableMaster($data)
    {
        $this->db->insert('tbl_ander_baher_room', $data);
        return $this->db->insert_id();
    }

    public function Delete($id)
    {
        $data = [
            'isDeleted' => TRUE,
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id', $id);
        $this->db->update('tbl_ander_baher_room', $data);
        return $this->db->last_query();
    }

    public function UpdateTableMaster($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_ander_baher_room', $data);
        return $this->db->last_query();
    }
}