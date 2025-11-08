<?php
class LudoTableMaster_model extends MY_Model
{

    public function AllTableMasterList()
    {
        $this->db->from('tbl_ludo_table_master');
        $this->db->where('isDeleted', false);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }


    public function getHistory()
    {
        $this->db->select('tbl_ludo.*,tbl_users.name');
        $this->db->from('tbl_ludo');
        $this->db->join('tbl_users', 'tbl_users.id=tbl_ludo.winner_id');
        $this->db->order_by('tbl_ludo.id', 'DESC');
        $this->db->limit(10);
        $Query = $this->db->get();
        return $Query->result();
    }


    public function ViewTableMaster($id)
    {
        $Query = $this->db->where('isDeleted', False)
            ->where('id', $id)
            ->get('tbl_ludo_table_master');
        return $Query->row();
    }
    
    public function AddTableMaster($data)
    {
        $this->db->insert('tbl_ludo_table_master', $data);
        return $this->db->insert_id();
    }

    public function Delete($id)
    {
        $data = [
            'isDeleted' => TRUE,
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id', $id);
        $this->db->update('tbl_ludo_table_master', $data);
        return $this->db->last_query();
    }

    public function UpdateTableMaster($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_ludo_table_master', $data);
        return $this->db->last_query();
    }
}