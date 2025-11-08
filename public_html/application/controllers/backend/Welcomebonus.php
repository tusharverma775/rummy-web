<?php
class Welcomebonus extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Users_model');
    }

    public function index()
    {
        $data = [
            'title' => 'Welcome Bonus',
            'bonus' => $this->Users_model->WelcomeBonus()
        ];
        
        template('welcomebonus/index', $data);
    }

    public function edit($id)
    {
        $data=[
            'title'=>'Edit Welcome Bonus',
            'bonus'=>$this->Users_model->WelcomeBonus($id)
        ];

        template('welcomebonus/edit', $data);
    }

    public function update()
    {
        $coin=$this->input->post('coin');
        $game_played=$this->input->post('game_played');
        $id=$this->input->post('id');

        $data = [
            'coin' => $coin,
            'game_played' => $game_played,
            'updated_date' => date('Y-m-d H:i:s')
        ];

        $UpdateProduct = $this->Users_model->UpdateWelcomeBonus($id, $data);
        if ($UpdateProduct) {
            $this->session->set_flashdata('msg', array('message' => 'Welcome Bonus Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/welcomebonus');
    }
}
