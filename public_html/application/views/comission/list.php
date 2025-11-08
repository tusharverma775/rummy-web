<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
                <ul class="nav nav-tabs">
                    <?php 
                        $patti='';
                        $point='';
                        $andar='';
                        $dragon='';
                        $jackpot='';
                        $pool='';
                        $deal='';
                        $seven='';
                        $car='';
                        $color='';
                        if (isset($_GET['tab'])) {
                            if ($_GET['tab'] == 1) {
                                $point='active';
                            }elseif ($_GET['tab'] == 2) {
                                $andar='active';
                            }elseif ($_GET['tab'] == 3) {
                                $dragon='active';
                            }elseif ($_GET['tab'] == 4) {
                                $jackpot='active';
                            }elseif ($_GET['tab'] == 5) {
                                $pool='';
                            }elseif ($_GET['tab'] == 6) {
                                $deal='';
                            }elseif ($_GET['tab'] == 7) {
                                $seven='';
                            }elseif ($_GET['tab'] == 8) {
                                $car='';
                            }elseif ($_GET['tab'] == 9) {
                                $color='';
                            }else{
                                $patti='active';
                            }
                        }else{
                            $patti='active';
                        }
                    ?>
                    <li class="<?= $patti ?>"><a href="<?= base_url('backend/Comission') ?>">3 Patti</a></li>
                    <li class="<?= $point ?>"><a href="<?= base_url('backend/Comission?tab=1') ?>">Point</a></li>
                    <li class="<?= $andar ?>"><a href="<?= base_url('backend/Comission?tab=2') ?>">Andar Bahar</a></li>
                    <li class="<?= $dragon ?>"><a href="<?= base_url('backend/Comission?tab=3') ?>">DragonTiger</a></li>
                    <li class="<?= $jackpot ?>"><a href="<?= base_url('backend/Comission?tab=4') ?>">Jackpot</a></li>
                    <!-- <li class="<?= $pool ?>"><a href="<?= base_url('backend/Comission?tab=5') ?>">Pool</a></li>
                    <li class="<?= $deal ?>"><a href="<?= base_url('backend/Comission?tab=6') ?>">Deal</a></li>
                    <li class="<?= $seven ?>"><a href="<?= base_url('backend/Comission?tab=7') ?>">Seven</a></li>
                    <li class="<?= $car ?>"><a href="<?= base_url('backend/Comission?tab=8') ?>">Car</a></li>
                    <li class="<?= $color ?>"><a href="<?= base_url('backend/Comission?tab=9') ?>">Color</a></li> -->
                </ul>
                <div class="tab-content">
                    <br>
                    <?php 
                        if (isset($_GET['tab'])) {
                            if ($_GET['tab'] == 1) { ?>
                                <div id="point" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Point_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?= $Game->amount ?></td>
                                                <td><?= $Game->user_winning_amt ?></td>
                                                <td><?= $Game->admin_winning_amt ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 2) { ?>
                                <div id="andar_bahar" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($AnderBahar_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?= ($Game->winning=='0')?'Andar':'Bahar' ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 3) { ?>
                                <div id="dragon_tiger" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($DragonTiger_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?= ($Game->winning=='0')?'Dragon':'Tiger' ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 4) { ?>
                                <div id="jackpot" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Jackpot_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?php if($Game->winning=='1'){
                                                    echo 'High Card';
                                                }elseif ($Game->winning=='2') {
                                                    echo 'Pair';
                                                }elseif ($Game->winning=='3') {
                                                    echo 'Color';
                                                }elseif ($Game->winning=='4') {
                                                    echo 'Sequence';
                                                }elseif ($Game->winning=='5') {
                                                    echo 'Pure Sequence';
                                                }elseif ($Game->winning=='6') {
                                                    echo 'Set';
                                                }
                                                ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 5) { ?>
                                <div id="jackpot" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Pool_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?php if($Game->winning=='1'){
                                                    echo 'High Card';
                                                }elseif ($Game->winning=='2') {
                                                    echo 'Pair';
                                                }elseif ($Game->winning=='3') {
                                                    echo 'Color';
                                                }elseif ($Game->winning=='4') {
                                                    echo 'Sequence';
                                                }elseif ($Game->winning=='5') {
                                                    echo 'Pure Sequence';
                                                }elseif ($Game->winning=='6') {
                                                    echo 'Set';
                                                }
                                                ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 6) { ?>
                                <div id="jackpot" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Jackpot_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?php if($Game->winning=='1'){
                                                    echo 'High Card';
                                                }elseif ($Game->winning=='2') {
                                                    echo 'Pair';
                                                }elseif ($Game->winning=='3') {
                                                    echo 'Color';
                                                }elseif ($Game->winning=='4') {
                                                    echo 'Sequence';
                                                }elseif ($Game->winning=='5') {
                                                    echo 'Pure Sequence';
                                                }elseif ($Game->winning=='6') {
                                                    echo 'Set';
                                                }
                                                ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 7) { ?>
                                <div id="jackpot" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Jackpot_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?php if($Game->winning=='1'){
                                                    echo 'High Card';
                                                }elseif ($Game->winning=='2') {
                                                    echo 'Pair';
                                                }elseif ($Game->winning=='3') {
                                                    echo 'Color';
                                                }elseif ($Game->winning=='4') {
                                                    echo 'Sequence';
                                                }elseif ($Game->winning=='5') {
                                                    echo 'Pure Sequence';
                                                }elseif ($Game->winning=='6') {
                                                    echo 'Set';
                                                }
                                                ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 8) { ?>
                                <div id="jackpot" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Jackpot_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?php if($Game->winning=='1'){
                                                    echo 'High Card';
                                                }elseif ($Game->winning=='2') {
                                                    echo 'Pair';
                                                }elseif ($Game->winning=='3') {
                                                    echo 'Color';
                                                }elseif ($Game->winning=='4') {
                                                    echo 'Sequence';
                                                }elseif ($Game->winning=='5') {
                                                    echo 'Pure Sequence';
                                                }elseif ($Game->winning=='6') {
                                                    echo 'Set';
                                                }
                                                ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }elseif ($_GET['tab'] == 9) { ?>
                                <div id="jackpot" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Winning</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Jackpot_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?php if($Game->winning=='1'){
                                                    echo 'High Card';
                                                }elseif ($Game->winning=='2') {
                                                    echo 'Pair';
                                                }elseif ($Game->winning=='3') {
                                                    echo 'Color';
                                                }elseif ($Game->winning=='4') {
                                                    echo 'Sequence';
                                                }elseif ($Game->winning=='5') {
                                                    echo 'Pure Sequence';
                                                }elseif ($Game->winning=='6') {
                                                    echo 'Set';
                                                }
                                                ?></td>
                                                <td><?= $Game->winning_amount ?></td>
                                                <td><?= $Game->user_amount ?></td>
                                                <td><?= $Game->comission_amount ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php }else{ ?>
                                <div id="patti" class="tab-pane fade in active">
                                    <table class="table table-bordered dt-responsive nowrap"
                                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Sr. No.</th>
                                                <th>Game ID</th>
                                                <th>Amount</th>
                                                <th>User Amount</th>
                                                <th>Comission Amount</th>
                                                <th>Added Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            foreach ($Game_Comission as $key => $Game) {
                                                $i++;
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td><?= $Game->id ?></td>
                                                <td><?= $Game->amount ?></td>
                                                <td><?= $Game->user_winning_amt ?></td>
                                                <td><?= $Game->admin_winning_amt ?></td>
                                                <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                            </tr>
                                            <?php }
                                            ?>


                                        </tbody>
                                    </table>
                                </div>
                            <?php } 
                        }else{ ?>
                            <div id="patti" class="tab-pane fade in active">
                                <table class="table table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Sr. No.</th>
                                            <th>Game ID</th>
                                            <th>Amount</th>
                                            <th>User Amount</th>
                                            <th>Comission Amount</th>
                                            <th>Added Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        foreach ($Game_Comission as $key => $Game) {
                                            $i++;
                                        ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= $Game->id ?></td>
                                            <td><?= $Game->amount ?></td>
                                            <td><?= $Game->user_winning_amt ?></td>
                                            <td><?= $Game->admin_winning_amt ?></td>
                                            <td><?= date("d-m-Y", strtotime($Game->added_date)) ?></td>
                                        </tr>
                                        <?php }
                                        ?>


                                    </tbody>
                                </table>
                            </div>
                        <?php }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>

<script>
$(document).ready(function() {
    $('.table').dataTable({
        dom: 'Bfrtip',
        "buttons": [
            'excel'
        ]
    });
})
</script>