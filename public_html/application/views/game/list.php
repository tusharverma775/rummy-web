<div class="row">

    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">

                <table id="datatable" class="table table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Name</th>
                            <th>Ticket Price</th>
                            <th>First Five Price</th>
                            <th>First Row Price</th>
                            <th>Second Row Price</th>
                            <th>Third Row Price</th>
                            <th>Whole Price</th>
                            <th>Start Time</th>
                            <th>Status</th>
                            <th>Updated Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=0;
                            foreach ($AllGame as $key => $Game) {
                                $i++;
                                ?>
                            <tr>
                            <td><?= $i?></td>
                            <td><?= $Game->name ?></td>
                            <td><?= $Game->ticket_price ?></td>
                            <td><?= $Game->first_five ?></td>
                            <td><?= $Game->first_row ?></td>
                            <td><?= $Game->second_row ?></td>
                            <td><?= $Game->third_row ?></td>
                            <td><?= $Game->whole ?></td>
                            <td><?= date("d-m-Y H:i",strtotime($Game->start_time)) ?></td>
                            <td><?= ($Game->status==0)?"Created":(($Game->status==1)?"Started":"Ended") ?></td>
                            <td><?= date("d-m-Y",strtotime($Game->updated_date)) ?></td>
                            <td>
                                <a href="<?= base_url('backend/game/ticket/'.$Game->id) ?>" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="View Tickets"><span class="fa fa-eye"></span></a> |
                                <?php if($Game->status<1){ ?>
                                <a href="<?= base_url('backend/game/edit/'.$Game->id) ?>" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Edit"><span class="fa fa-edit"></span></a> |
                                <?php } ?>
                                <a href="<?= base_url('backend/game/delete/'.$Game->id) ?>" onclick="return confirm('Are You Sure Want To Remove This Game?')" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Delete"><span class="fa fa-trash"></span></a>
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
