<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Game Id</th>
                            <th>Time</th>
                            <th>Winner</th>
                            <th>Winning Amount</th>
                            <th>User Amount</th>
                            <th>Admin Comission</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($AllGames as $key => $Games) { ?>
                        <tr>
                            <td><?= $Games->id ?></td>
                            <td><?= date("d-m-Y h:i A", strtotime($Games->added_date)) ?></td>
                            <td><?= $Games->name ?></td>
                            <td><?= $Games->amount ?></td>
                            <td><?= $Games->user_winning_amt ?></td>
                            <td><?= $Games->admin_winning_amt ?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>