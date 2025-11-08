<div class="row">

    <div class="col-12">
        <div class="card">
            <div class="card-body table-responsive">
                <table id="datatable" class="table table-bordered"
                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Referral Coins</th>
                            <th>Referral Level 1</th>
                            <th>Referral Level 2</th>
                            <th>Referral Level 3</th>
                            <!-- <th>Contact Us</th>
                            <th>Privacy Policy</th>
                            <th>Terms & Conditions</th>
                            <th>Help & Support</th> -->
                            <!-- <th>Default OTP</th> -->
                            <th>App Version</th>
                            <th>Game For Private</th>
                            <th>Joining Amount</th>
                            <th>Admin Commission</th>
                            <th>Whatsapp No.</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="<?= base_url('backend/setting/edit') ?>" class="btn btn-info"
                                    data-toggle="tooltip" data-placement="top" title="Edit"><span
                                        class="fa fa-edit"></span></a>
                            </td>
                            <td><?= $Setting->referral_amount ?></td>
                            <td><?= $Setting->level_1 ?>%</td>
                            <td><?= $Setting->level_2 ?>%</td>
                            <td><?= $Setting->level_3 ?>%</td>
                            <!-- <td><?= $Setting->contact_us ?></td>
                            <td><?= $Setting->privacy_policy ?></td>
                            <td><?= $Setting->terms ?></td>
                            <td><?= $Setting->help_support ?></td> -->
                            <td><?= $Setting->app_version ?></td>
                            <td><?= $Setting->game_for_private ?></td>
                            <td><?= $Setting->joining_amount ?></td>
                            <td><?= $Setting->admin_commission ?></td>
                            <td><?= $Setting->whats_no ?></td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- end col -->
</div>

<h4>Games Permission</h4>

<div class="row">
    
    <?php if (POINT_RUMMY==true) { ?>
    <div class="col-xl-2 col-md-1">
        <div class="card ">
            <div class="card-body">
                <div class="mb-">
                    <h5 class="font-14 text-uppercase mt-0">Point Rummy</h5>
                    <input class="form-check form-switch" type="checkbox" id="point_rummy" name="point_rummy"
                        <?= $Permission->point_rummy ? 'checked' : ''?> value="<?= $Permission->point_rummy ? 0 : 1 ?>"
                        switch="none">
                    <label class="form-label" for="point_rummy" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>


<script>
$(document).on('change', '.form-switch', function(e) {
    e.preventDefault();
    var game = $(this).attr("name")
    var type = $(this).val()
    if (type == 1) {
        $(this).val(0)
    } else {
        $(this).val(1)
    }
    console.log(type)
    jQuery.ajax({
        type: 'POST',
        url: '<?= base_url('backend/Setting/ChangeGameStatus') ?>',
        data: {
            name: game,
            type: type
        },
        beforeSend: function() {},
        success: function(response) {},
        error: function(e) {}
    })
});
</script>