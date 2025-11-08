<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                echo form_open_multipart('backend/WithdrawalLog/update', [
                    'autocomplete' => false, 'id' => 'edit_Redeem', 'method' => 'post'
                ], ['type' => $this->url_encrypt->encode('tbl_Redeem')])
                ?>


                <input type="hidden" value="<?= $Redeem->id ?>" name="Redeem_id">

                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Title *</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="title" value="<?= $Redeem->title ?>" required>
                    </div>
                </div>
                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Coin *</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" step="any" value="<?= $Redeem->coin ?>" name="coin" required>
                    </div>
                </div>
                 <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Amount *</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" step="any" value="<?= $Redeem->amount ?>" name="amount" required>
                    </div>
                </div>
                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Image *</label>
                    <div class="col-sm-5">
                        <input class="form-control" type="file" name="img" id="img">
                    </div><br>
                    <div class="col-sm-5">
                        <p>Preview</p>
                        <a href="<?= base_url('./data/Redeem/' . $Redeem->img) ?>" target="blank">
                            <img src="<?= base_url('./data/Redeem/' . $Redeem->img) ?>" width="300px">

                        </a>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <div>
                        <?php
                        echo form_submit('submit', 'Update', ['class' => 'btn btn-primary waves-effect waves-light mr-1']);
                        ?>
                        <a href="<?= base_url('backend/WithdrawalLog/RedeemNow') ?>" class="btn btn-secondary cancle_btn waves-effect">Cancel</a>
                    </div>
                </div>
                <?php
                echo form_close();
                ?>
            </div>
        </div><!-- end col -->
    </div>