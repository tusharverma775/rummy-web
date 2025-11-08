
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                echo form_open_multipart('backend/welcomebonus/update', [
                    'autocomplete' => false, 'id' => 'edit_bonus', 'method' => 'post'
                ])
                ?>

                <div class="form-group row"><label for="id" class="col-sm-2 col-form-label">Day *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" name="id" value="<?= $bonus[0]->id ?>" readonly>
                    </div>
                </div>

                <div class="form-group row"><label for="game_played" class="col-sm-2 col-form-label">Game Play Needed *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" name="game_played" value="<?= $bonus[0]->game_played ?>" required>
                    </div>
                </div>

                <div class="form-group row"><label for="coin" class="col-sm-2 col-form-label">Coin *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="number" name="coin" value="<?= $bonus[0]->coin ?>" required>
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
        
        // user for ckeditor.
        CKEDITOR.replace('contact_us');
        CKEDITOR.replace('terms');
    </script>