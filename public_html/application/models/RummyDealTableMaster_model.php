<?php
class RummyDealTableMaster_model extends MY_Model
{
    public function AllTableMasterList()
    {
        $this->db->from('tbl_rummy_deal_table_master');
        $this->db->where('isDeleted', false);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function ViewTableMaster($id)
    {
        $Query = $this->db->where('isDeleted', false)
            ->where('id', $id)
            ->get('tbl_rummy_deal_table_master');
        return $Query->row();
    }
    
    public function AddTableMaster($data)
    {
        $this->db->insert('tbl_rummy_deal_table_master', $data);
        return $this->db->insert_id();
    }

    public function Delete($id)
    {
        $data = [
            'isDeleted' => true,
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id', $id);
        $this->db->update('tbl_rummy_deal_table_master', $data);
        return $this->db->last_query();
    }

    public function UpdateTableMaster($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_rummy_deal_table_master', $data);
        return $this->db->last_query();
    }
}