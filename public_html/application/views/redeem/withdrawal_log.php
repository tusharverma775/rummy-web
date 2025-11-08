<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
                <ul class="nav nav-tabs">
                    <!-- <li class="active"><a data-toggle="tab" href="#pending">Pending</a></li>
                    <li><a data-toggle="tab" href="#approved">Approved</a></li>
                    <li><a data-toggle="tab" href="#rejected">Rejected</a></li> -->
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#pending" role="tab" aria-selected="true">
                            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                            <span class="d-none d-sm-block">Pending</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#approved" role="tab" aria-selected="false">
                            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                            <span class="d-none d-sm-block">Approved</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#rejected" role="tab" aria-selected="false">
                            <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                            <span class="d-none d-sm-block">Rejected</span>
                        </a>
                    </li>
                </ul>



                <div class="tab-content">
                    <br>
                    <div class="tab-pane p-3 active" id="pending" role="tabpanel">
                        <!-- <div id="pending" class="tab-pane fade in active"> -->
                        <table class="table table-bordered"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>User Name</th>
                                    <th>Bank Details</th>
                                    <th>Aadhar</th>
                                    <th>UPI</th>
                                    <th>Mobile</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($Pending as $key => $Data) {
                                    $i++;
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $Data->user_name ?></td>
                                    <td><?= $Data->bank_detail ?></td>
                                    <td><?= $Data->adhar_card ?></td>
                                    <td><?= $Data->upi ?></td>
                                    <td><?= $Data->mobile ?></td>
                                    <td><?= $Data->coin ?></td>
                                    <td>
                                        <select class="form-control"
                                            onchange="ChangeWithDrawalStatus(<?= $Data->id ?>,this.value)">
                                            <option value="0" <?= (($Data->status == 0) ? 'selected' : '') ?>>Pending
                                            </option>
                                            <option value="1" <?= (($Data->status == 1) ? 'selected' : '') ?>>Approve
                                            </option>
                                            <option value="2" <?= (($Data->status == 2) ? 'selected' : '') ?>>Reject
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div id="approved" class="tab-pane fade">
                        <br>
                        <table class="table table-bordered"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>User Name</th>
                                    <th>Mobile</th>
                                    <th>Coin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($Approved as $key => $Data) {
                                    $i++;
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $Data->user_name ?></td>
                                    <td><?= $Data->mobile ?></td>
                                    <td><?= $Data->coin ?></td>
                                    <td>
                                        <select class="form-control"
                                            onchange="ChangeWithDrawalStatus(<?= $Data->id ?>,this.value)">
                                            <!--   <option value="0" <?= (($Data->status == 0) ? 'selected' : '') ?>>Pending</option> -->
                                            <option value="1" <?= (($Data->status == 1) ? 'selected' : '') ?>>Approved
                                            </option>
                                            <!--  <option value="2" <?= (($Data->status == 2) ? 'selected' : '') ?>>Reject</option> -->
                                        </select>
                                    </td>
                                </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="rejected" class="tab-pane fade">
                        <br>
                        <table class="table table-bordered"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sr. No.</th>
                                    <th>User Name</th>
                                    <th>Mobile</th>
                                    <th>Coin</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                foreach ($Rejected as $key => $Data) {
                                    $i++;
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= $Data->user_name ?></td>
                                    <td><?= $Data->mobile ?></td>
                                    <td><?= $Data->coin ?></td>
                                    <td>
                                        <select class="form-control"
                                            onchange="ChangeWithDrawalStatus(<?= $Data->id ?>,this.value)">
                                            <!--                                         <option value="0" <?= (($Data->status == 0) ? 'selected' : '') ?>>Pending</option>
                                        <option value="1" <?= (($Data->status == 1) ? 'selected' : '') ?>>Approve</option>
 -->
                                            <option value="2" <?= (($Data->status == 2) ? 'selected' : '') ?>>Rejected
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                                <?php }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>

<script>
$(document).ready(function() {
    $('.table').dataTable();
})

function ChangeWithDrawalStatus(id, status) {
    jQuery.ajax({
        url: "<?= base_url('backend/WithdrawalLog/ChangeStatus') ?>",
        type: "POST",
        data: {
            'id': id,
            'status': status
        },
        success: function(data) {
            var response = JSON.parse(data)
            if (response.class == "success") {
                toastr.success(response.msg);
            } else {
                toastr.error(response.msg);
            }

            setTimeout(function() {
                location.reload()
            }, 1000);
        }
    });
}
</script>