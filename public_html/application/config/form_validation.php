<?php

defined('BASEPATH') or exit('No direct script access allowed');
$config = array(
    'login' => array(
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required',
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required',
        ),
    ),
     

);
