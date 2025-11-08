<?php
$actual_link = (($this->input->server('HTTPS') === 'on') ? "https" : "http") . "://" . $this->input->server('HTTP_HOST') . $this->input->server('REQUEST_URI');
$final_url = str_replace(strtolower(base_url()), '', strtolower($actual_link));
?>
<!-- Top Bar End -->
<!-- ========== Left Sidebar Start ========== -->
<div class="left side-menu" style="background-image: url('<?= base_url('assets/images/sp_bg.png') ?>');">
    <div class="slimscroll-menu" id="remove-scroll">
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu" id="side-menu">

                <li><a href="<?= base_url('backend/dashboard/admin') ?>" class="waves-effect"><i class="ti-home"></i>
                        <span>Dashboard</span></a></li>
                <li class="menu-title">Content</li>
                <?php if (USER_MANAGEMENT==true) { ?>
                <li
                    class="<?= (array_filter([strpos($final_url, "backend/user"),strpos($final_url, "backend/usercategory"),strpos($final_url, "backend/table"),strpos($final_url, "tablemaster/add"),strpos($final_url, "tablemaster/edit"),strpos($final_url, "backend/robotcards"),strpos($final_url, "backend/table")], 'is_numeric')) ? 'mm-active' : '' ?>">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ion ion-md-contact"></i>
                        <span>Users Management</span>
                    </a>
                    <ul class="sub-menu mm-collapse">
                        <li
                            class="<?= (array_filter([strpos($final_url, "backend/user")], 'is_numeric')) ? 'mm-active' : '' ?>">
                            <a href="<?= base_url('backend/user') ?>" class="waves-effect">
                                <span>Users</span></a>
                        </li>
                        <li
                            class="<?= (array_filter([strpos($final_url, "backend/usercategory")], 'is_numeric')) ? 'mm-active' : '' ?>">
                            <a data-toggle="modal" data-target="#exampleModal" class="waves-effect">
                                <span>User Category</span></a>
                        </li>


                        <li
                            class="<?= (array_filter([strpos($final_url, "backend/kyc")], 'is_numeric')) ? 'mm-active' : '' ?>">
                            <a data-toggle="modal" data-target="#exampleModal" class="waves-effect"></i>
                                <span>Kyc</span></a>
                        </li>
                        <li
                            class="<?= (array_filter([strpos($final_url, "backend/BankDetails")], 'is_numeric')) ? 'mm-active' : '' ?>">
                            <a data-toggle="modal" data-target="#exampleModal" class="waves-effect"> <span>Bank
                                    Details</span></a>
                        </li>
                    </ul>
                </li>
                <?php } ?>

                <?php if (WITHDRAWL_DASHBOARD==true) { ?>
                <li
                    class="<?= (array_filter([strpos($final_url, "withdrawldashboard")], 'is_numeric')) ? 'mm-active' : '' ?>">
                    <a data-toggle="modal" data-target="#exampleModal" class="waves-effect"><i class="ti-home"></i>
                        <span>Withdrawl Dashboard</span></a>
                </li>
                <?php } ?>

                <?php if (BANNER==true) { ?>
                <li><a href="<?= base_url('backend/banner') ?>" class="waves-effect"><i class="ion ion-md-contact"></i>
                        <span>Banner</span></a></li>
                <?php } ?>

                <?php if (POINT_RUMMY==true) { ?>
                <li
                    class="<?= (array_filter([strpos($final_url, "backend/rummy")], 'is_numeric')) ? 'mm-active' : '' ?>">
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ti-layout-grid2-alt"></i>
                        <span>Rummy Management</span>
                    </a>
                    <ul class="sub-menu mm-collapse">
                    <li
                            class="<?= (array_filter([strpos($final_url, "backend/rtummyablemaster")], 'is_numeric')) ? 'mm-active' : '' ?>">
                            <a href="<?= base_url('backend/RummyTableMaster') ?>" class="waves-effect">
                                <span>Point Table Master</span></a>
                        </li>
                        <li
                            class="<?= (array_filter([strpos($final_url, "rummy"),], 'is_numeric')) ? 'mm-active' : '' ?>">
                            <a href="<?= base_url('backend/Rummy') ?>" class="waves-effect">
                                <span>Rummy Point History</span></a>
                        </li>
                    </ul>
                </li>
                <?php } ?> 
               

                <?php if (PURCHASE_HISTORY==true) { ?>
                <li><a data-toggle="modal" data-target="#exampleModal" class="waves-effect"><i
                            class="ion ion-md-contact"></i> <span>Purchase History</span></a></li>
                <?php } ?>

                <?php if (LEAD_BOARD==true) { ?>
                <li><a href="<?= base_url('backend/Game/Leaderboard') ?>" class="waves-effect"><i
                            class="ion ion-md-contact"></i> <span>Leadboard</span></a></li>
                <?php } ?>

                <?php if (NOTIFICATION==true) { ?>
                <li><a data-toggle="modal" data-target="#exampleModal" class="waves-effect"><i
                            class="ion ion-md-list-box"></i> <span>Notification</span></a></li>
                <?php } ?>

                <?php if (WELCOME_BONUS==true) { ?>
                <li><a href="<?= base_url('backend/welcomebonus') ?>" class="waves-effect"><i
                            class="ion ion-md-list-box"></i> <span>Welcome Bonus</span></a></li>
                <?php } ?>



                <?php if (REEDEM_MANAGEMENT==true) { ?>
                <li><a data-toggle="modal" data-target="#exampleModal" class="waves-effect"><i
                            class="ion ion-md-list-box"></i> <span>Reedem Management</span></a></li>
                <?php } ?>

                <?php if (WITHDRAWAL_LOG==true) { ?>
                <li><a data-toggle="modal" data-target="#exampleModal" class="waves-effect"><i
                            class="ion ion-md-list-box"></i> <span>Withdrawal Log</span></a></li>
                <?php } ?>

                <!-- <?php if (COMISSION==true) { ?>
                <li><a href="<?= base_url('backend/Comission') ?>" class="waves-effect"><i
                            class="ion ion-md-list-box"></i> <span>Comission</span></a></li>
                <?php } ?> -->

                <?php if (SETTING==true) { ?>
                <li><a href="<?= base_url('backend/setting') ?>" class="waves-effect"><i
                            class="ion ion-md-list-box"></i> <span>Setting</span></a></li>
                <?php } ?>
            </ul>
        </div>
        <!-- Sidebar -->
        <div class="clearfix"></div>
    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <h4 class="page-title"><?= $title ?></h4>

                    </div>
                    <div class="col-sm-6">
                        <div class="float-right d-md-block">
                            <?php

                     if (isset($SideBarbutton) && isset($SideBarbutton[1])) {
                         ?>

                            <a href="<?= base_url($SideBarbutton[0]) ?>"
                                class="btn btn-primary btn-lg btn-dashboard custom-btn">
                                <?= $SideBarbutton[1] ?></a>

                            <?php
                     } ?>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->