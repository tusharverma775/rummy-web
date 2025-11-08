<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
            echo form_open_multipart('backend/UserCategory/update', ['autocomplete' => false, 'id' => 'edit_user_category'
                ,'method'=>'post'], ['type' => $this->url_encrypt->encode('tbl_user_category'),
                'id'=> $UserCategory->id])
                ?>
             
             <div class="form-group row">
                    <div class="col-md-3">
                    <label for="name">Name *</label>
                        <input class="form-control" type="text"  name="name" value="<?= $UserCategory->name ?>" required
                            id="name">
                    </div>
                    <div class="col-md-2">
                    <label for="amount">Amount *</label>
                        <input class="form-control" type="number" min="0" name="amount" value="<?= $UserCategory->amount ?>" required
                            id="amount">
                    </div>
                    <div class="col-md-2">
                    <label for="percentage">Percentage(%) *</label>
                        <input class="form-control" type="number" min="0"  name="percentage" value="<?= $UserCategory->percentage ?>" required
                            id="percentage">
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