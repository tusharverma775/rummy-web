<?php //print_r($post_para); ?>
<!-- page-title -->
<section class="section bg-secondary">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <h4><?= $post->heading ?></h4>
      </div>
    </div>
  </div>
</section>
<!-- /page-title -->

<!-- blog single -->
<section>
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <ul class="list-inline d-flex justify-content-between py-3">
          <li class="list-inline-item"><i class="ti-direction-alt mr-2"></i><?= $city->city_name_a.", ".$state->state_name ?></li>
          <li class="list-inline-item"><i class="ti-calendar mr-2"></i><?= date('M d, Y', strtotime($post->added_date)) ?></li>
        </ul>
        <img src="<?= base_url('/data/post/'.$post->image); ?>" alt="post-thumb" class="w-100 img-fluid mb-4">
        <div class="content">
          <p><?= $post->details ?></p>
          <?php foreach ($post_para as $key => $value) { ?>
              <h5><?= $value->heading ?></h5>
              <?php if(!empty($value->image)){ ?>
              <img src="<?= base_url('/data/post/'.$value->image); ?>" alt="post-thumb" class="w-100 img-fluid">
              <?php } ?>
              <p><?= $value->detail ?></p>
          <?php } ?>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="widget search-box">
          <i class="ti-search"></i>
          <input type="search" id="search-post" class="form-control border-0 pl-5" name="search-post"
            placeholder="Search">
        </div>
        <div class="widget">
          <h6 class="mb-4">LATEST POST</h6>
          <?php
          foreach ($all_post as $val) { ?>
          <div class="media mb-4">
            <div class="post-thumb-sm mr-3">
              <img class="img-fluid" src="<?= base_url('data/post/'.$val->image); ?>" alt="post-thumb">
            </div>
            <div class="media-body">
              <ul class="list-inline d-flex justify-content-between mb-2">
                <li class="list-inline-item">Post By Travel</li>
                <li class="list-inline-item"><?= date('M d, Y', strtotime($val->added_date)) ?></li>
              </ul>
              <h6><a class="text-dark" href="<?= base_url('home/post/'.$val->id) ?>"><?= $val->heading ?></a></h6>
            </div>
          </div>
          <?php } ?>
        <div class="widget">
          <h6 class="mb-4">TAG</h6>
          <ul class="list-inline tag-list">
            <li class="list-inline-item m-1"><a href="blog-single.html">ui ux</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">developmetns</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">travel</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">article</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">travel</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">ui ux</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">article</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">developmetns</a></li>
          </ul>
        </div>
        <div class="widget">
          <h6 class="mb-4">CATEGORIES</h6>
          <ul class="list-inline tag-list">
            <li class="list-inline-item m-1"><a href="blog-single.html">ui ux</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">developmetns</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">travel</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">article</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">travel</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">ui ux</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">article</a></li>
            <li class="list-inline-item m-1"><a href="blog-single.html">developmetns</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /blog single -->