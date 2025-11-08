<div class="row">

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Coins</th>
                            <th>Game Play Needed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bonus as $key => $value) { ?>
                        <tr>
                            <td><?= $value->id ?></td>
                            <td><?= $value->coin ?></td>
                            <td><?= $value->game_played ?></td>
                            <td>
                                <a href="<?= base_url('backend/welcomebonus/edit/'.$value->id) ?>" class="btn btn-info" data-toggle="tooltip" data-placement="top" title="Edit"><span class="fa fa-edit"></span></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>