<?php
class Ticket_model extends MY_Model
{
    private $generate_count = 0;

    function randomGen($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }

    public function GetTicket($user_id, $game_id)
    {
        $this->generate_count++;
        $random = $this->randomGen(1,90,20);

        $data = [
            'game_id' => $game_id,
            'user_id' => $user_id,
            'r1_1' => $random[1],
            'r1_2' => $random[2],
            'r1_3' => $random[3],
            'r1_4' => $random[4],
            'r1_5' => $random[5],
            'r2_1' => $random[6],
            'r2_2' => $random[7],
            'r2_3' => $random[8],
            'r2_4' => $random[9],
            'r2_5' => $random[10],
            'r3_1' => $random[11],
            'r3_2' => $random[12],
            'r3_3' => $random[13],
            'r3_4' => $random[14],
            'r3_5' => $random[0],
            'isDeleted' => 1,
            'added_date' => date('Y-m-d H:i:s')
        ];

        //check if record already exists
        $this->db->where('game_id', $game_id);
        $this->db->where('r1_1', $data['r1_1']);
        $this->db->where('r1_2', $data['r1_2']);
        $this->db->where('r1_3', $data['r1_3']);
        $this->db->where('r1_4', $data['r1_4']);
        $this->db->where('r1_5', $data['r1_5']);
        $this->db->where('r2_1', $data['r2_1']);
        $this->db->where('r2_2', $data['r2_2']);
        $this->db->where('r2_3', $data['r2_3']);
        $this->db->where('r2_4', $data['r2_4']);
        $this->db->where('r2_5', $data['r2_5']);
        $this->db->where('r3_1', $data['r3_1']);
        $this->db->where('r3_2', $data['r3_2']);
        $this->db->where('r3_3', $data['r3_3']);
        $this->db->where('r3_4', $data['r3_4']);
        $this->db->where('r3_5', $data['r3_5']);
        $Query = $this->db->get('tbl_tickets');
        $TicketRecord = $Query->row();

        if ($TicketRecord) {
            if($this->generate_count>5)
            {
                return 'Please Try Again.';
            }
            $this->GetTicket($game_id, $user_id);
        }
        else
        {
            if($this->db->insert('tbl_tickets', $data))
            {
                $data['ticket_id'] = $this->db->insert_id();
                return $data;
            }
        }
    }

    public function GetTicketWallet($user_id, $game_id, $amount)
    {
        $this->generate_count++;
        $random = $this->randomGen(1,90,20);

        $data = [
            'game_id' => $game_id,
            'user_id' => $user_id,
            'r1_1' => $random[1],
            'r1_2' => $random[2],
            'r1_3' => $random[3],
            'r1_4' => $random[4],
            'r1_5' => $random[5],
            'r2_1' => $random[6],
            'r2_2' => $random[7],
            'r2_3' => $random[8],
            'r2_4' => $random[9],
            'r2_5' => $random[10],
            'r3_1' => $random[11],
            'r3_2' => $random[12],
            'r3_3' => $random[13],
            'r3_4' => $random[14],
            'r3_5' => $random[0],
            'razorpay_order_id' => 'WALLET',
            'added_date' => date('Y-m-d H:i:s')
        ];

        //check if record already exists
        $this->db->where('game_id', $game_id);
        $this->db->where('r1_1', $data['r1_1']);
        $this->db->where('r1_2', $data['r1_2']);
        $this->db->where('r1_3', $data['r1_3']);
        $this->db->where('r1_4', $data['r1_4']);
        $this->db->where('r1_5', $data['r1_5']);
        $this->db->where('r2_1', $data['r2_1']);
        $this->db->where('r2_2', $data['r2_2']);
        $this->db->where('r2_3', $data['r2_3']);
        $this->db->where('r2_4', $data['r2_4']);
        $this->db->where('r2_5', $data['r2_5']);
        $this->db->where('r3_1', $data['r3_1']);
        $this->db->where('r3_2', $data['r3_2']);
        $this->db->where('r3_3', $data['r3_3']);
        $this->db->where('r3_4', $data['r3_4']);
        $this->db->where('r3_5', $data['r3_5']);
        $Query = $this->db->get('tbl_tickets');
        $TicketRecord = $Query->row();

        if ($TicketRecord) {
            if($this->generate_count>5)
            {
                return 'Please Try Again.';
            }
            $this->GetTicket($game_id, $user_id);
        }
        else
        {
            if($this->db->insert('tbl_tickets', $data))
            {
                $data['ticket_id'] = $this->db->insert_id();

                $this->db->set('wallet', 'wallet-'.$amount,FALSE);
                $this->db->set('updated_date', date('Y-m-d H:i:s'));
                $this->db->where('id', $user_id);
                $this->db->update('tbl_users');
                
                return $data;
            }
        }
    }

    public function GenerateNumber($game_id)
    {
        $number = 0;

        $this->db->where('id', $game_id);
        $this->db->where('isDeleted', FALSE);
        $GameQuery = $this->db->get('tbl_game');

        if($GameQuery->num_rows())
        {
            $Game = $GameQuery->row();

            if($Game->status==1)
            {

                $check_first_five = ($Game->first_five_winner>0)?FALSE:TRUE;
                $check_first_row = ($Game->first_row_winner>0)?FALSE:TRUE;
                $check_second_row = ($Game->second_row_winner>0)?FALSE:TRUE;
                $check_third_row = ($Game->third_row_winner>0)?FALSE:TRUE;
                $check_whole = ($Game->whole_winner>0)?FALSE:TRUE;

                //SELECT id FROM `numbers` WHERE id NOT IN (SELECT number FROM `tbl_game_draw` WHERE game_id=1) ORDER BY RAND() LIMIT 1
                $this->db->where('id NOT IN (SELECT number FROM `tbl_game_draw` WHERE game_id='.$game_id.')', NULL, FALSE);
                $Query = $this->db->order_by('RAND()');
                $Query = $this->db->limit(1);
                $Query = $this->db->get('numbers');
                
                // print_r($Query->row()->Id);
                if($Query->num_rows())
                {
                    $number = $Query->row()->Id;

                    // SELECT * FROM `tbl_tickets` WHERE 1 IN (`r1_1`, `r1_2`, `r1_3`, `r1_4`, `r1_5`, `r2_1`, `r2_2`, `r2_3`, `r2_4`, `r2_5`, `r3_1`, `r3_2`, `r3_3`, `r3_4`, `r3_5`)
                    $this->db->where($number.' IN (`r1_1`, `r1_2`, `r1_3`, `r1_4`, `r1_5`, `r2_1`, `r2_2`, `r2_3`, `r2_4`, `r2_5`, `r3_1`, `r3_2`, `r3_3`, `r3_4`, `r3_5`)', NULL, FALSE);
                    $this->db->where('game_id', $game_id);
                    $this->db->where('isDeleted', false);
                    $TicketQuery = $this->db->get('tbl_tickets');
                    // print_r($this->db->last_query());

                    if($TicketQuery->num_rows())
                    {
                        $Tickets = $TicketQuery->result();
                        foreach ($Tickets as $key => $value) {
                            $r1 = 0;
                            $r2 = 0;
                            $r3 = 0;

                            // First Five
                            if($check_first_five)
                            {
                                if(($value->total_count+1)==5)
                                {
                                    $this->db->set('first_five_winner', $value->id);
                                    $this->db->where('id', $game_id);
                                    $this->db->update('tbl_game');
                                    // print_r($this->db->last_query());

                                    $this->db->set('wallet', 'wallet+'.$Game->first_five, FALSE);
                                    $this->db->where('id', $value->user_id);
                                    $this->db->update('tbl_users');

                                    $data = [
                                        'user_id' => $value->user_id,
                                        'game_id' => $game_id,
                                        'ticket_id' => $value->id,
                                        'winning_type' => 'first_five',
                                        'amount' => $Game->first_five,
                                        'added_date' => date('Y-m-d H:i:s')
                                    ];
                                    $this->db->insert('tbl_game_rewards', $data);

                                    $check_first_five = FALSE;
                                }
                            }

                            switch ($number) {
                                case $value->r1_1:
                                case $value->r1_2:
                                case $value->r1_3:
                                case $value->r1_4:
                                case $value->r1_5:
                                    $r1 = 1;
                                    // First Row
                                    if(!$check_first_five && $check_first_row)
                                    {
                                        if(($value->r1_count+$r1)==5)
                                        {
                                            $this->db->set('first_row_winner', $value->id);
                                            $this->db->where('id', $game_id);
                                            $this->db->update('tbl_game');
                                            // print_r($this->db->last_query());

                                            $this->db->set('wallet', 'wallet+'.$Game->first_row, FALSE);
                                            $this->db->where('id', $value->user_id);
                                            $this->db->update('tbl_users');

                                            $data = [
                                                'user_id' => $value->user_id,
                                                'game_id' => $game_id,
                                                'ticket_id' => $value->id,
                                                'winning_type' => 'first_row',
                                                'amount' => $Game->first_row,
                                                'added_date' => date('Y-m-d H:i:s')
                                            ];
                                            $this->db->insert('tbl_game_rewards', $data);

                                            $check_first_row = FALSE;
                                        }
                                    }
                                    break;

                                case $value->r2_1:
                                case $value->r2_2:
                                case $value->r2_3:
                                case $value->r2_4:
                                case $value->r2_5:
                                    $r2 = 1;
                                    // Second Row
                                    if(!$check_first_five && $check_second_row)
                                    {
                                        if(($value->r2_count+$r2)==5)
                                        {
                                            $this->db->set('second_row_winner', $value->id);
                                            $this->db->where('id', $game_id);
                                            $this->db->update('tbl_game');
                                            // print_r($this->db->last_query());

                                            $this->db->set('wallet', 'wallet+'.$Game->second_row, FALSE);
                                            $this->db->where('id', $value->user_id);
                                            $this->db->update('tbl_users');

                                            $data = [
                                                'user_id' => $value->user_id,
                                                'game_id' => $game_id,
                                                'ticket_id' => $value->id,
                                                'winning_type' => 'second_row',
                                                'amount' => $Game->second_row,
                                                'added_date' => date('Y-m-d H:i:s')
                                            ];
                                            $this->db->insert('tbl_game_rewards', $data);

                                            $check_second_row = FALSE;
                                        }
                                    }
                                    break;

                                case $value->r3_1:
                                case $value->r3_2:
                                case $value->r3_3:
                                case $value->r3_4:
                                case $value->r3_5:
                                    $r3 = 1;
                                    // Third Row
                                    if(!$check_first_five && $check_third_row)
                                    {
                                        if(($value->r3_count+$r3)==5)
                                        {
                                            $this->db->set('third_row_winner', $value->id);
                                            $this->db->where('id', $game_id);
                                            $this->db->update('tbl_game');
                                            // print_r($this->db->last_query());

                                            $this->db->set('wallet', 'wallet+'.$Game->third_row, FALSE);
                                            $this->db->where('id', $value->user_id);
                                            $this->db->update('tbl_users');

                                            $data = [
                                                'user_id' => $value->user_id,
                                                'game_id' => $game_id,
                                                'ticket_id' => $value->id,
                                                'winning_type' => 'third_row',
                                                'amount' => $Game->third_row,
                                                'added_date' => date('Y-m-d H:i:s')
                                            ];
                                            $this->db->insert('tbl_game_rewards', $data);

                                            $check_third_row = FALSE;
                                        }
                                    }
                                    break;
                                
                                default:
                                    break;
                            }

                            // Whole
                            if(!$check_first_five && $check_whole)
                            {
                                if(($value->total_count+1)==15)
                                {
                                    $this->db->set('whole_winner', $value->id);
                                    $this->db->set('status', 2);
                                    $this->db->where('id', $game_id);
                                    $this->db->update('tbl_game');
                                    // print_r($this->db->last_query());

                                    $this->db->set('wallet', 'wallet+'.$Game->whole, FALSE);
                                    $this->db->where('id', $value->user_id);
                                    $this->db->update('tbl_users');

                                    $data = [
                                        'user_id' => $value->user_id,
                                        'game_id' => $game_id,
                                        'ticket_id' => $value->id,
                                        'winning_type' => 'whole',
                                        'amount' => $Game->whole,
                                        'added_date' => date('Y-m-d H:i:s')
                                    ];
                                    $this->db->insert('tbl_game_rewards', $data);

                                    $check_whole = FALSE;
                                }
                            }

                            $selected_log = [
                                'ticket_id' => $value->id,
                                'number' => $number,
                                'added_date' => date('Y-m-d H:i:s')
                            ];
        
                            $this->db->insert('tbl_ticket_selected',$selected_log);

                            $this->db->set('r1_count', 'r1_count+'.$r1, FALSE);
                            $this->db->set('r2_count', 'r2_count+'.$r2, FALSE);
                            $this->db->set('r3_count', 'r3_count+'.$r3, FALSE);
                            $this->db->set('total_count', 'total_count+1', FALSE);
                            $this->db->where('id', $value->id);
                            $this->db->update('tbl_tickets');
                        }
                    }

                    $data = [
                        'game_id' => $game_id,
                        'number' => $number,
                        'added_date' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('tbl_game_draw',$data);
                }
                else
                {
                    $this->db->set('status', 2);
                    $this->db->where('id', $game_id);
                    $this->db->update('tbl_game');
                }
            }
            else
            {
                if($Game->status==0)
                {
                    $number = 102; // Game Not Started
                }
                else if($Game->status==2)
                {
                    $number = 103; // Game Ended
                }
            }
        }
        else
        {
            $number = 101; // Game Not Found
        }
        return $number;
    }

    public function Update_Ticket($user_id, $ticket_id, $amount, $razorpay_order_id)
    {
        $this->db->set('amount', $amount);
        $this->db->set('razorpay_order_id', $razorpay_order_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('id', $ticket_id);
        $this->db->update('tbl_tickets');

        return $this->db->affected_rows();
    }

    public function No_Of_Tickets($razorpay_order_id)
    {
        $this->db->where('razorpay_order_id', $razorpay_order_id);
        $this->db->from('tbl_tickets');
        $Query = $this->db->get();

        return $Query->num_rows();
    }

    public function Update_Ticket_Payment($razorpay_order_id)
    {
        $this->db->set('isDeleted', 0);
        $this->db->where('razorpay_order_id', $razorpay_order_id);
        $this->db->update('tbl_tickets');

        return $this->db->affected_rows();
    }

    public function GetTicketByGameId($game_id)
    {
        $this->db->select('tbl_tickets.*,tbl_users.name,tbl_users.mobile');
        $this->db->from('tbl_tickets');
        $this->db->join('tbl_users','tbl_tickets.user_id=tbl_users.id');
        $this->db->where('tbl_tickets.game_id', $game_id);
        $this->db->where('tbl_tickets.isDeleted', 0);
        $Query = $this->db->get();

        return $Query->result();
    }

    public function GetUserByTicketId($ticket_id)
    {
        $this->db->select('tbl_users.name,tbl_users.mobile,tbl_users.profile_pic,tbl_tickets.*,tbl_tickets.id as ticket_id');
        $this->db->from('tbl_tickets');
        $this->db->join('tbl_users','tbl_tickets.user_id=tbl_users.id');
        $this->db->where('tbl_tickets.id', $ticket_id);
        $Query = $this->db->get();

        return $Query->result();
    }

    public function GetUserTicketByGameId($user_id,$game_id)
    {
        $this->db->from('tbl_tickets');
        $this->db->where('game_id', $game_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('isDeleted', 0);
        $Query = $this->db->get();

        return $Query->result();
    }

    public function GetSelectedTicketNumber($ticket_id)
    {
        $this->db->select('number,added_date');
        $this->db->from('tbl_ticket_selected');
        $this->db->where('ticket_id', $ticket_id);
        $Query = $this->db->get();

        return $Query->result();
    }

    public function GetSelectedGameNumber($game_id)
    {
        $this->db->select('number,DATE_FORMAT(added_date, "%e %M, %h:%i:%s %p") as added_date');
        $this->db->from('tbl_game_draw');
        $this->db->where('game_id', $game_id);
        $Query = $this->db->get();

        return $Query->result();
    }

}
