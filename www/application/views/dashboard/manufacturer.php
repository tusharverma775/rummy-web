<div class="row">
    <div class="col-xl-3 col-md-6">
        <a href="<?= base_url("backend/Setting/AdminCoin_log") ?>">
            <div class="card bg_dasbord_box mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4"><img src="<?= base_url("assets/images/coin.png") ?>"
                                alt=""></div>
                        <h5 class="font-14 text-uppercase mt-0 text-white-50">Admin Coin</h5>
                        <h4 class="font-500"><?= number_format($AdminCoins) ?></h4>
                        <!-- <div class="mini-stat-label bg-success">
                                    <p class="mb-0">+ 12%</p>
                                 </div>  -->
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card bg_dasbord_box mini-stat bg-primary text-white">
            <div class="card-body">
                <div class="mb-4">
                    <div class="float-left mini-stat-img mr-4"><img src="<?= base_url("assets/images/artisan.png") ?>"
                            alt=""></div>
                    <h5 class="font-14 text-uppercase mt-0 text-white-50">Active User</h5>
                    <h4 class="font-500"><?= number_format(count($ActiveUser)) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="<?= base_url("backend/user") ?>">
            <div class="card bg_dasbord_box mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4"><img
                                src="<?= base_url("assets/images/customer.png") ?>" alt=""></div>
                        <h5 class="font-14 text-uppercase mt-0 text-white-50">Total User</h5>
                        <h4 class="font-500"><?= number_format(count($AllUserList)) ?></h4>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <div class="col-xl-3 col-md-6">
        <a href="<?= base_url("backend/Purchase") ?>">
            <div class="card bg_dasbord_box mini-stat bg-primary text-white">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4"><img
                                src="<?= base_url("assets/images/money-bag.png") ?>" alt=""></div>
                        <h5 class="font-14 text-uppercase mt-0 text-white-50">Total Purchase</h5>
                        <h4 class="font-500"><?= number_format((float) $TotalCoins) ?></h4>
                    </div>
                </div>
            </div>
        </a>
    </div>

    <?php if (POINT_RUMMY==true) { ?>
    <div class="col-xl-3 col-md-6">
        <label>Rummy Bot</label>
        <select class="form-control" onchange="ChangeRummyBotStatus(this.value)">
            <option value="0" <?= (($RummyBotStatus == 0) ? 'selected' : '') ?>>ON</option>
            <option value="1" <?= (($RummyBotStatus == 1) ? 'selected' : '') ?>>OFF</option>
        </select><br>
    </div>
    <?php } ?>

    <!-- end row -->
</div>
<!-- container-fluid -->
</div>
<script>
function ChangeStatus(status) {
    jQuery.ajax({
        url: "<?= base_url('backend/setting/ChangeJackpotStatus') ?>",
        type: "POST",
        data: {
            'status': status
        },
        success: function(data) {
            if (data) {
                alert('Successfully Change status');
            }
            location.reload();
        }
    });
}

function ChangeRummyBotStatus(status) {
    jQuery.ajax({
        url: "<?= base_url('backend/setting/ChangeRummyBotStatus') ?>",
        type: "POST",
        data: {
            'status': status
        },
        success: function(data) {
            if (data) {
                alert('Successfully Change status');
            }
            location.reload();
        }
    });
}

function ChangeTeenpattiBotStatus(status) {
    jQuery.ajax({
        url: "<?= base_url('backend/setting/ChangeTeenpattiBotStatus') ?>",
        type: "POST",
        data: {
            'status': status
        },
        success: function(data) {
            if (data) {
                alert('Successfully Change status');
            }
            location.reload();
        }
    });
}
</script>