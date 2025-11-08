<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                echo form_open_multipart('backend/WithdrawalLog/insert', [
                    'autocomplete' => false, 'id' => 'add_redeem', 'method' => 'post'
                ], ['type' => $this->url_encrypt->encode('tbl_redeem')])
                ?>
                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Title *</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title" required>
                    </div>
                </div>
                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Coin *</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" step="any" name="coin" required>
                    </div>
                </div>
                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Amount *</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" step="any" name="amount" required>
                    </div>
                </div>

                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Image</label>
                    <div class="col-sm-10">
                        <input type="file" class="form-control" name="img">
                    </div>
                </div>

                <div class="form-group mb-0">
                    <div>
                        <?php
                        echo form_submit('submit', 'Submit', ['class' => 'btn btn-primary waves-effect waves-light mr-1']);
                        ?>
                  <a href="<?= base_url('backend/WithdrawalLog/ReedemNow') ?>" class="btn btn-secondary cancle_btn waves-effect">Cancel</a>
                    </div>
                </div>
                <?php
                echo form_close();
                ?>
            </div>
        </div><!-- end col -->
    </div>

