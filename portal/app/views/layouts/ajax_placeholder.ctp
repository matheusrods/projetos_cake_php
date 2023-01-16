<?php echo $content_for_layout; ?>
<?php echo $this->Buonny->link_js('jquery.html5-placeholder-shim') ?>
<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){
	setTimeout(function () {
        $.placeholder.shim();  
    }, 500);
	$.placeholder.shim();});'); ?>