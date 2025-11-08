 <!-- Begin page -->
 <div id="wrapper">
     <!-- Top Bar Start -->
     <div class="topbar">
         <!-- LOGO -->
         <div class="topbar-left"><a href="#" class="logo"><span style="color: white"><?= PROJECT_NAME ?></span><i><img
                         src="<?= base_url('assets/images/bu.png') ?>" alt="" height="50"></i></a></div>
         <nav class="navbar-custom">
             <ul class="navbar-right list-inline float-right mb-0">

                 <!-- full screen -->
                 <li class="dropdown notification-list list-inline-item d-none d-md-inline-block"><a
                         class="nav-link waves-effect" href="#" id="btn-fullscreen"><i
                             class="mdi mdi-fullscreen noti-icon"></i></a></li>
                 <li class="dropdown notification-list list-inline-item">
                     <div class="dropdown notification-list nav-pro-img">
                         <a class="dropdown-toggle nav-link arrow-none waves-effect nav-user" data-toggle="dropdown"
                             href="#" role="button" aria-haspopup="false" aria-expanded="false"><img
                                 src="<?= base_url(LOGO.$logo->logo) ?>" alt="user" class="rounded-circle"></a>
                         <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                             <a class="dropdown-item" href="<?= base_url('backend/Profile/add'); ?>"><i
                                     class="mdi mdi-account-circle m-r-5"></i> Profile</a>
                             <div class="dropdown-divider"></div>
                             <a class="dropdown-item text-danger" href="<?= base_url('backend/auth/logout')?>"><i
                                     class="mdi mdi-power text-danger"></i> Logout</a>
                         </div>
                     </div>
                 </li>
             </ul>
             <ul class="list-inline menu-left mb-0">
                 <li class="float-left"><button class="button-menu-mobile open-left waves-effect"><i
                             class="mdi mdi-menu"></i></button></li>

             </ul>
         </nav>
     </div>