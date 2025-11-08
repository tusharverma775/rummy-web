<?php

class Dashboard extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Setting_model', 'Users_model', 'Coin_plan_model']);
    }

    public function index()
    {
        redirect('backend/dashboard/admin');
    }

    public function admin()
    {
        $data = [
            'title' => 'Dashboard',
            'AdminCoins' => $this->Setting_model->Setting()->admin_coin,
            'JackpotCoins' => $this->Setting_model->Setting()->jackpot_coin,
            'RummyBotStatus' => $this->Setting_model->Setting()->robot_rummy,
            'ActiveUser' => $this->Users_model->ActiveUser(),
            'AllUserList' => $this->Users_model->AllUserList(),
            'TotalCoins' => $this->Coin_plan_model->GetTotalPurchase(),
        ];
        // $data['ActiveUser'];
        // exit;
        template('dashboard/manufacturer', $data);
    }
}
