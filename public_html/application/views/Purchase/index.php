<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>User Name</th>
                            <th>Plan ID</th>
                            <th>Coins</th>
                            <th>Price</th>
                            <!-- <th>Payment Status</th> -->
                            <th>Added Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($AllPurchase as $key => $Purchase) {
                            $i++;
                            ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $Purchase->name ?></td>
                            <td><?= $Purchase->plan_id ?></td>
                            <td><?= $Purchase->coin ?></td>
                            <td><?= $Purchase->price ?></td>
                            <!-- <td><?= ($Purchase->payment == 0) ? 'Pending' : 'Done' ?></td> -->
                            <td><?= date("d-m-Y", strtotime($Purchase->added_date)) ?></td>
                        </tr>
                        <?php }
                        ?>
                    </tbody>
                </table>
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