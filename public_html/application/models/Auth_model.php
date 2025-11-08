<?php
class Auth_model extends MY_Model
{
    public function login($username,$password)
    {
        $Query = $this->db->select('id,role,email_id,first_name,last_name')
                 ->where('isDeleted',false)
                 ->where('email_id',$username)
                 ->where('sw_password',$password)
                 ->get($this->TBL_ADMIN);      
                 
        return $Query->row();
    }
}
