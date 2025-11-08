<?php
class Gift_model extends MY_Model
{

    public function List()
    {
        $this->db->from('tbl_gift');
        $this->db->where('isDeleted', false);
        $this->db->order_by('id', 'desc');
        $Query = $this->db->get();
        return $Query->result();
    }

    public function View_Gift($id)
    {
        $this->db->from('tbl_gift');
        $this->db->where('isDeleted', false);
        $this->db->where('id', $id);

        $Query = $this->db->get();
        return $Query->row();
    }

    public function View($id)
    {
        $this->db->from('tbl_coin_plan');
        $this->db->where('isDeleted', false);
        $this->db->where('id', $id);

        $Query = $this->db->get();
        return $Query->row();
    }

    public function GetCoin($user_id, $plan_id, $coin, $price)
    {
        $data = [
            'user_id' => $user_id,
            'plan_id' => $plan_id,
            'coin' => $coin,
            'price' => $price
        ];

        if ($this->db->insert('tbl_purchase', $data)) {
            return $this->db->insert_id();
        }
    }

    public function UpdateOrder($user_id, $order_id, $razor_payment_id)
    {
        $this->db->set('razor_payment_id', $razor_payment_id);
        $this->db->set('updated_date', date('Y-m-d H:i:s'));
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $order_id);
        $this->db->update('tbl_purchase');

        return $this->db->affected_rows();
    }

    public function GetUserByOrderId($order_id)
    {
        $this->db->select('tbl_users.name,tbl_users.mobile,tbl_users.profile_pic,tbl_purchase.*');
        $this->db->from('tbl_purchase');
        $this->db->join('tbl_users', 'tbl_purchase.user_id=tbl_users.id');
        $this->db->where('tbl_purchase.id', $order_id);
        $Query = $this->db->get();

        return $Query->result();
    }

    public function UpdateOrderPayment($razor_payment_id)
    {
        $this->db->set('isDeleted', 0);
        $this->db->set('payment', 1);
        $this->db->where('razor_payment_id', $razor_payment_id);
        $this->db->update('tbl_purchase');

        return $this->db->affected_rows();
    }

    public function AddGift($image)
    {
        $data = [
            'name' => $this->input->post('name'),
            'image' => $image,
            'coin' => $this->input->post('coin'),
            'added_date' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('tbl_gift', $data);
        return $this->db->insert_id();
    }

    public function UpdateGift($data, $id)
    {
        $result = FALSE;
        $this->db->where('id', $id);
        $Updated = $this->db->update('tbl_gift', $data);
        if ($Updated) {
            $result = TRUE;
        }
        return $result;
    }
}
