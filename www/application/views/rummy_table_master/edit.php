<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
            echo form_open_multipart('backend/rummyTableMaster/update', ['autocomplete' => false, 'id' => 'edit_rummy_table_master'
                ,'method'=>'post'], ['type' => $this->url_encrypt->encode('tbl_rummy_table_master'),
                'id'=> $RummyTableMaster->id])
                ?>
                <div class="form-group row"><label for="point_value" class="col-sm-2 col-form-label">Boot Value
                        *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" min="0" step="0.01" name="point_value" required
                            id="point_value" value="<?= $RummyTableMaster->point_value?>"
                            onkeyup="updateValue(this.value)">
                    </div>
                </div>

                <div class="form-group row"><label for="boot_value" class="col-sm-2 col-form-label">Boot Value *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" min="0" step="0.01" name="boot_value" id="boot_value"
                            value="<?= $RummyTableMaster->boot_value?>" readonly>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <div>
                        <?php
                        echo form_submit('submit', 'Update', ['class' => 'btn btn-primary waves-effect waves-light mr-1']);
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
        $('#point_value').val(x * 80);
    }
    </script>