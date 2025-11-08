<?php
class Notification_model extends MY_Model
{

    public function List()
    {
        $this->db->from('tbl_notification');
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function Add($data)
    {
        $this->db->insert('tbl_notification', $data);
        return $this->db->insert_id();
    }
}
