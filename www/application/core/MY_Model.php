<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    protected $TBL_ADMIN = 'tbl_admin';
    protected $TBL_STATE = 'tbl_state';
    protected $TBL_CITY = 'tbl_city';
    protected $TBL_KEYWORD = 'tbl_keyword';
    protected $TBL_POST = 'tbl_post';
    protected $TBL_POST_CITY = 'tbl_post_city';
    protected $TBL_POST_PARA = 'tbl_post_para';
    protected $TBL_POST_KEYWORD = 'tbl_post_keyword';
}
