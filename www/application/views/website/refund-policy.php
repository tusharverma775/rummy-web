    <div class="container-main">
        <div class="banner banner_small">
            <div class="text">
                <h1>Refund Policy</h1>
                <p>Contact <?= PROJECT_NAME ?> Team for more help</p>
            </div>
        </div>
        <div class="container" style="width: auto">
            <div class="lift_box">
                <div class="lift_down">
                    <div class="icon">
                        <img src="<?= base_url('assets/website/images/icon.png') ?>" alt="">
                    </div>
                    <div class="text">
                        <p><?= PROJECT_NAME ?></p>
                        <p>Install and Play <?= PROJECT_NAME ?> to win real rupees in cash!</p>
                    </div>
                    <div class="down">
                        <a href="<?= base_url('game.apk') ?>" download>Download APK</a>
                    </div>
                </div>
            </div>
            <div class="right_box">
                <div class="box">
                    <h3>Refund Policy</h3>
                    <?= $Setting->refund_policy ?>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>