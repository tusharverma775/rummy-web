<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <?php
            echo form_open_multipart('backend/game/insert', ['autocomplete' => false, 'id' => 'add_post'
                ,'method'=>'post'], ['type' => $this->url_encrypt->encode('tbl_post')])
            ?>
                
                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Name *</label>
                    <div class="col-sm-10"><input class="form-control" type="text" value="" name="name" required id="name"></div>
                </div>
                <div class="form-group row"><label for="ticket_price" class="col-sm-2 col-form-label" style="color:green">Ticket Price *</label>
                    <div class="col-sm-10"><input type="number" step="1" name="ticket_price" id="ticket_price" class="form-control"required></input></div>
                </div>
                <div class="form-group row"><label for="first_five" class="col-sm-2 col-form-label">Fisrt Five Price *</label>
                    <div class="col-sm-10"><input type="number" step="1" name="first_five" id="first_five" class="form-control"required></input></div>
                </div>
                <div class="form-group row"><label for="first_row" class="col-sm-2 col-form-label">Fisrt Row Price *</label>
                    <div class="col-sm-10"><input type="number" step="1" name="first_row" id="first_row" class="form-control"required></input></div>
                </div>
                <div class="form-group row"><label for="second_row" class="col-sm-2 col-form-label">Second Row Price *</label>
                    <div class="col-sm-10"><input type="number" step="1" name="second_row" id="second_row" class="form-control"required></input></div>
                </div>
                <div class="form-group row"><label for="third_row" class="col-sm-2 col-form-label">Third Row Price *</label>
                    <div class="col-sm-10"><input type="number" step="1" name="third_row" id="third_row" class="form-control"required></input></div>
                </div>
                <div class="form-group row"><label for="whole" class="col-sm-2 col-form-label">Whole Price *</label>
                    <div class="col-sm-10"><input type="number" step="1" name="whole" id="whole" class="form-control"required></input></div>
                </div>
                <div class="form-group row"><label for="start_time" class="col-sm-2 col-form-label">Start Time *</label>
                    <div class="col-sm-10"><input type="datetime-local" name="start_time" id="start_time" class="form-control"required></input></div>
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