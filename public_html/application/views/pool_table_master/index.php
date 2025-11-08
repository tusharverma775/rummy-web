<div class="row">

    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <table id="datatable" class="table table-bordered dt-responsive nowrap"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Boot Value</th>
                            <th>Pool Point</th>
                            <th>Added Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($AllPoolTableMaster as $key => $PoolTableMaster) {
                            $i++;
                            ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $PoolTableMaster->boot_value ?></td>
                            <td><?= $PoolTableMaster->pool_point ?></td>
                            <td><?= date("d-m-Y", strtotime($PoolTableMaster->added_date)) ?></td>
                            <td>
                                <a href="<?= base_url('backend/PoolTableMaster/edit/' . $PoolTableMaster->id) ?>"
                                    class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Edit"><span
                                        class="fa fa-edit"></span></a>
                                | <a href="<?= base_url('backend/PoolTableMaster/delete/' . $PoolTableMaster->id) ?>"
                                    class="btn btn-danger" data-toggle="tooltip" data-placement="top"
                                    title="Delete"><span class="fa fa-times"></span></a>
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