<?php
if (null !== $this->session->flashdata('msg')) {
    $message = $this->session->flashdata('msg');
    echo '<script>';
    echo '$( document ).ready(function() {';
    ?>
    let type = '<?= $message['class']?>';
    let message = '<?= $message['message']?>';
        toastr.options = {
            "closeButton": true,
        }
        if(type==='success'){
            toastr.success(message)
        }else if(type==='error'){
            toastr.error(message)
        }else if(type==='info'){
            toastr.info(message)
        }else {
            toastr.warning(message)
        }
    <?php
    echo '});';
    echo '</script>';
}
?>