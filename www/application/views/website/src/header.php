<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    <title><?= PROJECT_NAME ?> | <?= $title ?></title>
    <meta name="description"
        content="Download and Play <?= PROJECT_NAME ?>, classic Indian Cards Games online anytime, anywhere. Free daily bonus chips, win real money cash in rupees. Winners Take All!">
    <meta property="og:title" content="Play <?= PROJECT_NAME ?>, Win Unlimited Rupees" />
    <meta property="og:description"
        content="Download and Play <?= PROJECT_NAME ?>, classic Indian Cards Games online anytime, anywhere. Free daily bonus chips, win real money cash in rupees. Winners Take All!" />
    <link href="<?= base_url(LOGO.$Setting->logo) ?>" rel="shortcut icon">
    <link href="https://fonts.googleapis.com/css?family=Anton|Titan+One" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/website/css/style.css') ?>">
    <script src="<?= base_url('assets/website/js/jquery-3.4.1.min.js') ?>" type="text/javascript">
    </script>
    <style>
    .banner-bg{
        background-image:url(<?= BANNER_URL.$banner->banner ?>);
        background-repeat:no-repeat;
        background-size:cover;
    }
    </style>
</head>

<body>
    <header class="header">
        <a class="logo" style="background: url(<?= LOGO.$Setting->logo ?>) left center no-repeat; background-size: 120px;" href="<?= base_url() ?>"></a>
        <div class="navigation">
            <div class="menu_icon"></div>
            <ul>
                <li>
                    <a href="<?= base_url() ?>">Home</a>
                </li>
                <li>
                    <a href="<?= base_url('download') ?>">Download</a>
                </li>
                <li>
                    <a href="<?= base_url('faq') ?>">FAQ</a>
                </li>
                <li>
                    <a href="<?= base_url('about-us') ?>">About us</a>
                </li>
                <li>
                    <a href="<?= base_url('privacy-policy') ?>">Privacy Policy</a>
                </li>
                <li>
                    <a href="<?= base_url('terms-conditions') ?>">Terms & Conditions</a>
                </li>
                <li>
                    <a href="<?= base_url('refund-policy') ?>">Refund Policy</a>
                </li>
                <li>
                    <a href="<?= base_url('security') ?>">Security</a>
                </li>
                <li>
                    <a href="<?= base_url('contact-us') ?>">Contact us</a>
                </li>
            </ul>
        </div>
    </header>