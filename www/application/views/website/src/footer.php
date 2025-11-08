<footer class="footer">
    <div class="footer_main">
        <div class="top" style="background: url(<?= LOGO.$Setting->logo ?>) no-repeat 16px; background-size: 48px;">
            <?= PROJECT_NAME ?> - The Most Popular Indian Cards Games, play <?= PROJECT_NAME ?> online win real rupees
            with Millions of Players
            around the world in real-time!
        </div>
        <div class="footer_item">
            <a href="<?= base_url() ?>">Home</a>
            <a href="<?= base_url('download') ?>">Download</a>
            <a href="<?= base_url('faq') ?>">FAQ</a>
            <a href="<?= base_url('about-us') ?>">About Us</a>
            <a href="<?= base_url('privacy-policy') ?>">Privacy Policy</a>
            <a href="<?= base_url('terms-conditions') ?>">Terms & Conditions</a>
            <a href="<?= base_url('refund-policy') ?>">Refund Policy</a>
            <a href="<?= base_url('security') ?>">Security</a>
            <a href="<?= base_url('contact-us') ?>">Contact us</a>
        </div>
        <div class="copy"> © <?= date('Y') ?> <?= PROJECT_NAME ?> All rights reserved</div>
    </div>

    <div class="share-right">
        <ul>
            <li>
                <a class="social_share" data-type="fb">
                    <i class="fa share-fb"></i>
                </a>
            </li>
            <li>
                <a class="social_share" data-type="twitter">
                    <i class="share-twitter"></i>
                </a>
            </li>
            <li>
                <a class="social_share" data-type="vk">
                    <i class="share-vk"></i>
                </a>
            </li>
            <li>
                <a class="social_share" data-type="reddit">
                    <i class="share-reddit"></i>
                </a>
            </li>
            <li>
                <a class="share-more">
                    <i class="share_more_btn"></i>
                </a>
            </li>
        </ul>
    </div>
    <button class="share-button">
        <i class="fa-share"></i>
    </button>
    <div class="small-share">
        <a class="social_share" data-type="fb">
            <i class="share-fb"></i>
            <span class="text">Facebook</span>
        </a>
        <a class="social_share" data-type="twitter">
            <i class="share-twitter"></i>
            <span class="text">Twitter</span>
        </a>
        <a class="social_share" data-type="vk">
            <i class="share-vk"></i>
            <span class="text">Vkontakte</span>
        </a>
        <a class="social_share" data-type="reddit">
            <i class="share-reddit"></i>
            <span class="text">Reddit</span>
        </a>
        <a class="share-more">
            <i class="share_more_btn"></i>
            <span class="text">More</span>
        </a>
    </div>
    <div class="other-share">
        <div class="other-share-main">
            <div class="title"><?= PROJECT_NAME ?></div>
            <div class="subtitle">Download and play <?= PROJECT_NAME ?> to win real money rupees in cash. Classic Indian
                Cards Games
                for all player.</div>
            <div class="list">
                <a class="social_share" data-type="fb">
                    <i class="share-fb"></i>
                    <span class="text">Facebook</span>
                </a>
                <a class="social_share" data-type="twitter">
                    <i class="share-twitter"></i>
                    <span class="text">Twitter</span>
                </a>
                <a class="social_share" data-type="vk">
                    <i class="share-vk"></i>
                    <span class="text">Vkontakte</span>
                </a>
                <a class="social_share" data-type="reddit">
                    <i class="share-reddit"></i>
                    <span class="text">Reddit</span>
                </a>
                <a class="social_share" data-type="googleplus">
                    <i class="share-g"></i>
                    <span class="text">Google</span>
                </a>
                <a class="social_share" data-type="pinterest">
                    <i class="share-pinterest"></i>
                    <span class="text">Pinterest</span>
                </a>
                <a class="social_share" data-type="whatsapp">
                    <i class="share-whatsapp"></i>
                    <span class="text">WhatsApp</span>
                </a>
                <a class="social_share" data-type="email">
                    <i class="share-e"></i>
                    <span class="text">Email</span>
                </a>
            </div>
        </div>
    </div>
</footer>
<script type="text/javascript" src="<?= base_url('assets/website/js/lazyload.min.js') ?>">
</script>
<script src="<?= base_url('assets/website/js/jsshare.js') ?>" type="text/javascript"></script>
<script type="text/javascript">
// lazyload img
(function() {
    var lazy = new LazyLoad({
        threshold: 200,
        elements_selector: ".lazy",
    });
})();

//show Menu
$('.menu_icon').click(function() {
    $(".header .navigation ul").animate({
        left: '0px'
    }, 200);
    $(".header").append("<div class='menu_bg close_menu'></div>");
    $("body").css("overflow", "hidden");
});

//close Menu
$(document).on("click", ".close_menu", function() {
    $(".header .navigation ul").animate({
        left: '-300px'
    }, 200);
    $("body").css("overflow", "auto");
    $(".menu_bg").remove();
});

$(window).resize(function() {
    window.location.reload();
});

$(".share-button").click(function() {
    $(".small-share").show();
    $(this).hide();
});

$(".share-more").click(function() {
    $(".other-share").show();
    $(".share-button").hide();
});

$(".small-share").click(function() {
    $(this).hide();
    if ($(".other-share").is(":hidden")) {
        $(".share-button").show();
    }
});

$(".other-share").click(function() {
    $(this).hide();
    if ($(".share-right").is(":hidden")) {
        $(".share-button").show();
    }
});
var shareItems = document.querySelectorAll('.social_share');
for (var i = 0; i < shareItems.length; i += 1) {
    shareItems[i].addEventListener('click', function share(e) {
        return JSShare.go(this);
    });
}
</script>
</body>

</html>