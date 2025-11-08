<div class="row">

    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">

                <table id="datatable" class="table table-bordered nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>Updated Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $i=0;
                            foreach ($Tickets as $key => $Ticket) {
                                $i++;
                                ?>
                            <tr>
                            <td><?= $i?></td>
                            <td><?= $Ticket->name ?></td>
                            <td><?= $Ticket->mobile ?></td>
                            <td><?= date("d-m-Y H:i",strtotime($Ticket->updated_date)) ?></td>
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