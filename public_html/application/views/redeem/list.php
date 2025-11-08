<div class="row">

    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
            <ul>
                <li class="btn"><a href="#" class="btn btn-primary">Pending</a></li>
                <li class="btn"><a href="#" class="btn btn-success">Approve</a></li>
                <li class="btn"><a href="#" class="btn btn-danger">Reject</a></li>
            </ul>
                <table id="datatable1" class="table table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th style="display: none">status</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Redeem Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=0;
                            foreach ($AllRedeem as $key => $Game) {
                                $i++;
                                ?>
                            <tr>
                            <td><?= $i ?></td>
                            <th style="display: none"><?= ($Game->status==0)?'Pending':(($Game->status==1)?'Approve':'Reject') ?></th>
                            <td><?= $Game->name ?></td>
                            <td><?= $Game->redeem_mobile ?></td>
                            <td><?= $Game->amount ?></td>
                            <td><?= $Game->payment_method ?></td>
                            <td>
                                <select class="form-control" onchange="ChangeOrderStatus(<?= $Game->id ?>,this.value)" <?= (($Game->status == 0) ? '' : 'disabled') ?>>
                                    <option value="0" <?= (($Game->status == 0) ? 'selected' : '') ?>>Pending</option>
                                    <option value="1" <?= (($Game->status == 1) ? 'selected' : '') ?>>Approve</option>
                                    <option value="2" <?= (($Game->status == 2) ? 'selected' : '') ?>>Reject</option>
                                </select>
                            </td>
                            <td><?= date("d-m-Y",strtotime($Game->added_date)) ?></td>
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
    function ChangeOrderStatus(id,status)
    {
        jQuery.ajax(
        {
            url:"<?= base_url('backend/redeem/ChangeOrderStatus') ?>",
            type:"POST",
            data:{'id':id,'status':status},
            success:function(data)
            {
                if(data)
                {
                    alert('Successfully Change status'); 
                }
                location.reload();
            }
        });
    }

    $(document).ready(function(){
        var table = $('#datatable1').DataTable();

        $('ul').on( 'click', 'a', function () {

        table
            .columns( 1 )
            .search(  $(this).text() )
            .draw();
        });
    })
    
</script>