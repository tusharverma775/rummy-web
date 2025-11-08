<?php
class Chips_model extends MY_Model
{

    public function AllChipsList()
    {
        $this->db->select('tbl_coin_plan.*');
        $this->db->from('tbl_coin_plan');
        $this->db->where('tbl_coin_plan.isDeleted', false);
        $this->db->order_by('tbl_coin_plan.id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function ViewChips($id)
    {
        $Query = $this->db->where('isDeleted', False)
            ->where('id', $id)
            ->get('tbl_coin_plan');
        return $Query->row();
    }
    
    public function AddChips($data)
    {
        $this->db->insert('tbl_coin_plan', $data);
        return $this->db->insert_id();
    }

    public function Delete($id)
    {
        $data = [
            'isDeleted' => TRUE,
            'updated_date' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id', $id);
        $this->db->update('tbl_coin_plan', $data);
        return $this->db->last_query();
    }

    public function UpdateChips($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_coin_plan', $data);
        return $this->db->last_query();
    }
}
