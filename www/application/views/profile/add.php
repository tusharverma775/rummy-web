<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                echo form_open_multipart('backend/profile/update', [
                    'autocomplete' => false, 'id' => 'add_post', 'method' => 'post'
                ], ['type' => $this->url_encrypt->encode('tbl_category')])
                ?>

                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label"> Company Name *</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" value="<?= $admin->first_name ?>" name="name" required
                            id="Name">
                    </div>
                </div>
                <input type="hidden" value="<?= $admin->id ?>" name="id">
                <div class="form-group row"><label for="name" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-10">
                        <input class="form-control" type="password" name="password" value=""
                            placeholder="Enter New Password If You Want To Update Old Password" id="password">
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
    // function get_child(cat_id, cat) {

    //     if (cat_id == 0 && cat == 'main') {
    //         $("#sub_cat").html('');
    //         return;
    //     }

    //     if (cat_id == 0 && cat == 'sub') {
    //         return;
    //     }

    //     $.ajax({
    //         type: 'POST',
    //         url: BASE_URL + "backend/category/get_category",
    //         data: {
    //             cat_id: cat_id
    //         },
    //         success: function(data) {
    //             if (cat == 'main') {
    //                 $("#sub_cat").html(data);
    //             } else {
    //                 $("#sub_cat").append(data);
    //             }
    //         }
    //     });
    // }

    // this code for ckeditor.
    // CKEDITOR.replace('detail');

    // function trim(el) {
    //     el.value = el.value.
    //     replace(/(^\s*)|(\s*$)/gi, ""). // removes leading and trailing spaces
    //     replace(/[ ]{2,}/gi, " "). // replaces multiple spaces with one space 
    //     replace(/\n +/, "\n"); // Removes spaces after newlines
    //     return;
    // }
    </script>