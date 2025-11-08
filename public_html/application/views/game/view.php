<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group row"><label for="" class="col-sm-2 col-form-label">State *</label>
                    <div class="col-sm-10">
                        <?= $State->state_name ?>
                    </div>
                </div>
                <div class="form-group row"><label for="" class="col-sm-2 col-form-label">City *</label>
                    <div class="col-sm-10">
                        <?= $City->city_name_a ?>
                    </div>
                </div>

                <div class="form-group row"><label for="heading" class="col-sm-2 col-form-label">Heading *</label>
                    <div class="col-sm-10"><?= $Post->heading ?></div>
                </div>
                <div class="form-group row"><label for="post_image" class="col-sm-2 col-form-label">Image *</label>
                    <div class="col-sm-10"><img height="200" src="<?= base_url('data/post/'.$Post->image) ?>"></div>
                </div>
                <div class="form-group row"><label for="details" class="col-sm-2 col-form-label">Details *</label>
                    <div class="col-sm-10"><?= $Post->details ?></textarea></div>
                </div>

                <?php foreach ($Post_para as $key => $value) { ?>
                    <div class="form-group row"><label for="sub_heading" class="col-sm-2 col-form-label">Sub Heading <?= $key+1 ?></label>
                        <div class="col-sm-10"><?= $value->heading; ?></div>
                    </div>

                    <?php if(!empty($value->image)){ ?>
                    <div class="form-group row"><label for="sub_post_image" class="col-sm-2 col-form-label">Sub Image <?= $key+1 ?></label>
                        <div class="col-sm-10"><img height="200" src="<?= base_url('data/post/'.$value->image); ?>"></div>
                    </div>
                    <?php } ?>

                    <div class="form-group row"><label for="sub_details" class="col-sm-2 col-form-label">Sub Details <?= $key+1 ?></label>
                        <div class="col-sm-10"><?= $value->detail; ?></div>
                    </div>
                <?php } ?>

                <div class="form-group row"><label for="keyword" class="col-sm-2 col-form-label">Keyword *</label>
                    <div class="col-sm-10"><?= $Post->keyword ?></div>
                </div>
                <div class="form-group row"><label for="meta" class="col-sm-2 col-form-label">Meta *</label>
                    <div class="col-sm-10"><?= $Post->meta ?></div>
                </div>
            </div>
        </div><!-- end col -->
    </div>
</div>