<div class="container-main">
    <div class="banner banner_small" style="background: url(<?= BANNER_URL.$banner->banner ?>) no-repeat center;">
        <div class="text">
            <h1>
                <?= PROJECT_NAME ?>
                APK is Downloading<span class="dotting"></span>
            </h1>
            <p>If the download doesn't start automatically, click the download button bellow.</p>
            <a href="<?= base_url('game.apk') ?>" download class="download_btn">Click to Restart</a>
        </div>
    </div>
    <div class="container">
        <div class="how-to">
            <div class="title">How to Install <?= PROJECT_NAME ?></div>
            <div class="item">
                <div>1</div>
                <p>Download <?= PROJECT_NAME ?> on your Android</p>
            </div>
            <div class="item">
                <div>2</div>
                <p>Open the file from the notification area or from your download folder, select Install.</p>
            </div>
            <div class="item">
                <div>3</div>
                <p>You may have to allow Unknown Sources at Settings > Security Screen</p>
            </div>
        </div>
        <div class="section_down">
            <div class="big-title">How to install unknown sources APK</div>
            <div class="left">
                <img src="<?= base_url('assets/website/images/install-unknown-sources.jpg') ?>"
                    alt="Install unknown sources apk">
            </div>
            <div class="right">
                <div class="text">
                    <div class="title">Install APK outside Play Store on Android 8 or higher</div>
                    <p>Enabling Sideloading on Android 8 & Higher, unlike enable allowing unknown sources APK
                        installtion. When you try download an APK with your browser on Android 8 or higher, you'll need
                        to give your browser permission to install apps.</p>
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="section_down">
            <div class="right">
                <img src="<?= base_url('assets/website/images/google-play-protect.png') ?>" alt="google play protect <?= PROJECT_NAME ?>
">
            </div>
            <div class="left">
                <div class="text">
                    <div class="title">You're still protected</div>
                    <p>Even when installing Apps from Unknown sources, Google Play Protect will continue scanning Apps
                        on your Mobile, looking for viruses, malware and blocking forbidden Apps. <?= PROJECT_NAME ?>
                        is safe to play.</p>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>