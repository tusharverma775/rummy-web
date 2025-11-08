<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-body">
                <h4 class="page-title">Tip LOG</h4>
                <table name="Tip_Log" class="table table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Table ID</th>
                            <th>Tip</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($AllTipLog as $key => $tip) {
                            $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $tip->user_id ?></td>
                            <td><?= $tip->name ?></td>
                            <td><?= $tip->table_id ?></td>
                            <td><?= $tip->coin ?></td>
                            <!-- <td>
                                    <a href="<?= base_url('backend/setting/edit') ?>" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Edit"><span class="fa fa-edit"></span></a>
                                </td> -->
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-body table-responsive">
                <h4 class="page-title">Commission LOG</h4>

                <table name="Commission_Log" class="table table-bordered"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Game ID</th>
                            <th>Winner Name</th>
                            <th>Game Coin</th>
                            <th>Commission Coin</th>
                            <!-- <th>Action</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($AllCommissionLog as $key => $Commission) {
                            $i++; ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $Commission->id ?></td>
                            <td><?= $Commission->name ?></td>
                            <td><?= $Commission->amount ?></td>
                            <td><?= $Commission->admin_winning_amt ?></td>
                            <!-- <td>
                                    <a href="<?= base_url('backend/setting/edit') ?>" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Edit"><span class="fa fa-edit"></span></a>
                                </td> -->
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>
<script>
$(document).ready(function() {
    $('.table').dataTable();
})
</script>