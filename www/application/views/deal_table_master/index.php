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
                            <th>Maximum Blind</th>
                            <th>Chaal Value</th>
                            <th>Pot Limit</th>
                            <th>Added Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($AllDealTableMaster as $key => $DealTableMaster) {
                            $i++;
                        ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $DealTableMaster->boot_value ?></td>
                            <td><?= $DealTableMaster->maximum_blind ?></td>
                            <td><?= $DealTableMaster->chaal_limit ?></td>
                            <td><?= $DealTableMaster->pot_limit ?></td>
                            <td><?= date("d-m-Y", strtotime($DealTableMaster->added_date)) ?></td>
                            <td>
                                <a href="<?= base_url('backend/DealTableMaster/edit/' . $DealTableMaster->id) ?>"
                                    class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Edit"><span
                                        class="fa fa-edit"></span></a>
                                | <a href="<?= base_url('backend/DealTableMaster/delete/' . $DealTableMaster->id) ?>"
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