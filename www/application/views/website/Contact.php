 <div class="container-main">
     <div class="banner banner_small">
         <div class="text">
             <h1>Contact us</h1>
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
         <!-- <div class="right_box">
             <div class="box">
                 <h3>Contact Us</h3><br>
                 <div class="col-md-12">

                     <div class="left text-center">
                         <div class=""><b>Email</b> : care@54decks.com</div>

                     </div>
                     <div class="right text-center">
                         <b>Address </b>: No.87, First Floor, 4th Cross Street
                         Thirumala Nagar 1st Main Road,
                         Perungudi, Chennai – 600096
                     </div>
                     <div class=" text-center">
                         <div class="">
                             <b>Contact Number </b> : +91 8925290666
                         </div>
                     </div>

                 </div>


             </div>
         </div> -->
         <div class="right_box">
             <div class="box">
                 <h3>Contact Us</h3>
                 <?= $Setting->contact_us ?>
             </div>
         </div>
         <div class="clear"></div>
     </div>
 </div>