<?php

class Game extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Game_model', 'Ticket_model', 'Users_model']);
    }

    // public function index()
    // {
    //     $data = [
    //         'title' => 'Game',
    //         'AllGame' => $this->Game_model->List()
    //     ];
    //     $data['SideBarbutton'] = ['backend/game/add', 'Add Game'];
    //     template('game/list', $data);
    // }

    public function index()
    {
        $AllGames = $this->Game_model->AllGames();
        // foreach ($AllGames as $key => $value) {
        //     $AllGames[$key]->details=$this->Game_model->ViewBet('', $value->id);
        // }
        // echo '<pre>';print_r($AllGames);die;
        $data = [
            'title' => 'Game History',
            'AllGames' => $AllGames
        ];
        template('game/index', $data);
    }

    public function view($id)
    {
        $data = [
            'title' => 'View Game',
            'Post' => $this->Game_model->View($id),
        ];
        // print_r($data);
        template('game/view', $data);
    }

    public function add()
    {
        $data = [
            'title' => 'Add Game'
        ];
        template('game/add', $data);
    }

    public function ticket($game_id)
    {
        $data = [
            'title' => 'View Ticket',
            'Tickets' => $this->Ticket_model->GetTicketByGameId($game_id),
        ];
        // print_r($data);
        template('game/ticket', $data);
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Game',
            'Game' => $this->Game_model->View($id)
        ];
        // print_r($data);
        template('game/edit', $data);
    }

    public function delete($id)
    {
        if ($this->Game_model->Delete($id)) {
            $this->session->set_flashdata('msg', array('message' => 'Game Removed Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/game');
    }

    public function insert()
    {
        $data = [
            'name' => $this->input->post('name'),
            'ticket_price' => $this->input->post('ticket_price'),
            'first_five' => $this->input->post('first_five'),
            'first_row' => $this->input->post('first_row'),
            'second_row' => $this->input->post('second_row'),
            'third_row' => $this->input->post('third_row'),
            'whole' => $this->input->post('whole'),
            'start_time' => $this->input->post('start_time'),
            'added_date' => date('Y-m-d H:i:s')
        ];
        $game_id = $this->Game_model->Create($data);
        if ($game_id) {
            $userdata = $this->Users_model->AllUserList();

            foreach ($userdata as $key => $value) {
                if (!empty($value->fcm)) {
                    $data['msg'] = "you can buy ticket ";
                    $data['title'] = "New Game added Game start on " . date('h:i A', strtotime($this->input->post('start_time')));
                    push_notification_android($value->fcm, $data);
                }
            }
            $this->session->set_flashdata('msg', array('message' => 'Game Added Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/game');
    }

    public function update()
    {
        $data = [
            'name' => $this->input->post('name'),
            'ticket_price' => $this->input->post('ticket_price'),
            'first_five' => $this->input->post('first_five'),
            'first_row' => $this->input->post('first_row'),
            'second_row' => $this->input->post('second_row'),
            'third_row' => $this->input->post('third_row'),
            'whole' => $this->input->post('whole'),
            'start_time' => $this->input->post('start_time'),
            'added_date' => date('Y-m-d H:i:s')
        ];
        $game_id = $this->Game_model->Update($data, $this->input->post('game_id'));
        if ($game_id) {
            $this->session->set_flashdata('msg', array('message' => 'Game Updated Successfully', 'class' => 'success', 'position' => 'top-right'));
        } else {
            $this->session->set_flashdata('msg', array('message' => 'Somthing Went Wrong', 'class' => 'error', 'position' => 'top-right'));
        }
        redirect('backend/game');
    }

    public function Leaderboard()
    {
        $data = [
            'title' => 'LeaderBoard',
            'LeaderBoard' => $this->Game_model->Leaderboard()
        ];
        template('game/LeaderBoard', $data);
    }
}