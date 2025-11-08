<?php
class Comission extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Game_model', 'AnderBahar_model', 'Jackpot_model','Rummy_model','DragonTiger_model','RummyDeal_model','RummyPool_model','SevenUp_model','ColorPrediction_model','CarRoulette_model']);
    }

    public function index()
    {
        $data['title'] = 'Comission Management' ;
        
        if (isset($_GET['tab'])) {
            if ($_GET['tab'] == 1) {
                $data['Point_Comission'] = $this->Rummy_model->Comission();
            }elseif ($_GET['tab'] == 2) {
                $data['AnderBahar_Comission'] = $this->AnderBahar_model->Comission();
            }elseif ($_GET['tab'] == 3) {
                $data['DragonTiger_Comission'] = $this->DragonTiger_model->Comission();
            }elseif ($_GET['tab'] == 4) {
                $data['Jackpot_Comission'] = $this->Jackpot_model->Comission();
            }elseif ($_GET['tab'] == 5) {
                $data['Pool_Comission'] = $this->RummyPool_model->Comission();
            }elseif ($_GET['tab'] == 6) {
                $data['Deal_Comission'] = $this->RummyDeal_model->Comission();
            }elseif ($_GET['tab'] == 7) {
                $data['Seven_Comission'] = $this->SevenUp_model->Comission();
            }elseif ($_GET['tab'] == 8) {
                $data['Car_Comission'] = $this->CarRoulette_model->Comission();
            }elseif ($_GET['tab'] == 9) {
                $data['Color_Comission'] = $this->ColorPrediction_model->Comission();
            }else{
                $data['Game_Comission'] = $this->Game_model->Comission();
            }
        }else{
            $data['Game_Comission'] = $this->Game_model->Comission();
        }
        // echo '<pre>';
        // print_r($data);die;
        template('comission/list', $data);
    }

}