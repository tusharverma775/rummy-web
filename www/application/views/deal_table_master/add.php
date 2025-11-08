<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
            echo form_open_multipart('backend/DealTableMaster/insert', ['autocomplete' => false, 'id' => 'add_deal_table_master'
                ,'method'=>'post'], ['type' => $this->url_encrypt->encode('tbl_rummy_deal_table_master')])
            ?>
                <div class="form-group row"><label for="boot_value" class="col-sm-2 col-form-label">Boot Value *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" min="0" step="0.01" name="boot_value" required
                            id="boot_value" onkeyup="updateValue(this.value)">
                    </div>
                </div>

                <div class="form-group row"><label for="chaal_limit" class="col-sm-2 col-form-label">Chaal Limit
                        *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" min="0" step="0.01" name="chaal_limit" readonly
                            id="chaal_limit">
                    </div>
                </div>

                <div class="form-group row"><label for="pot_limit" class="col-sm-2 col-form-label">Pot Limit *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" min="0" step="0.01" name="pot_limit" readonly
                            id="pot_limit">
                    </div>
                </div>

                <div class="form-group mb-0">
                    <div>
                        <?php
                        echo form_submit('submit', 'Submit', ['class' => 'btn btn-primary waves-effect waves-light mr-1']);
                        echo form_reset(['class' => 'btn btn-secondary waves-effect', 'value' => 'Cancel']);
                        ?>
                    </div>
                </div>
                <?php
            echo form_close();
            ?>
            </div>
        </div><!-- end col -->
    </div>
    <script>
    function updateValue(x) {
        $('#chaal_limit').val(x * 128);
        $('#pot_limit').val(x * 1024);
    }
    </script>