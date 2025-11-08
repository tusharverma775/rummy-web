<?php
class Banner_model extends MY_Model
{

    public function view()
    {
        $Query = $this->db->get('tbl_banner');
        return $Query->row();
    }

    public function update($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tbl_banner', $data);
        return $this->db->last_query();
    }
}
