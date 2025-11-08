<div class="row">

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Title</th>
                            <th>Coin</th>
                            <th>Amount</th>
                            <th>Image</th>
                            <th>Added Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($AllRedeem as $key => $Redeem) {
                            $i++;
                        ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><?= $Redeem->title ?></td>
                                <td><?= $Redeem->coin ?></td>
                                <td><?= $Redeem->amount ?></td>
                                <?php if($Redeem->img){ ?>
                                    <td><img src="<?= base_url() . 'data/Redeem/' . $Redeem->img ?>" width="100"></td>
                                 <?php  }else{ ?>
                                    <td>-</td>
                                 <?php } ?>
                                
                                <td><?= date("d-m-Y", strtotime($Redeem->created_date)) ?></td>
                                <td>
                                    <a href="<?= base_url('backend/WithdrawalLog/edit/' . $Redeem->id) ?>" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Edit"><span class="fa fa-edit"></span></a>
                                    |
                                    <a href="<?= base_url('backend/WithdrawalLog/delete/' . $Redeem->id) ?>" onclick="return confirm('Are You Sure Want To Remove This Image?')" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fa fa-trash"></span></a>
                                </td>
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