<?php

class Rummy extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Rummy_model','Users_model']);
    }

    public function index()
    {
        $AllGames = $this->Rummy_model->AllGames();

        $data = [
            'title' => 'Point Rummy History',
            'AllGames' => $AllGames
        ];
        template('rummy/index', $data);
    }
}