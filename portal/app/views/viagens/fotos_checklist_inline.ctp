<?
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        $classe = 'alert-success';
    } else if($session->read('Message.flash.params.type') == MSGT_ERROR){
        $session->delete('Message.flash');
        $classe = 'alert-error';
    } else {
		$classe = 'alert-info';
    }
?>
<?php if ($message = $session->flash()): ?>
<div id="flash_data" class="message" >
    <div class="alert <?php echo $classe?>"><?php echo $message; ?></div>
</div>
<?php echo $this->Javascript->codeBlock("
	$(document).ready(function() {
	  // fade out flash 'success' messages
	  $('#flash_data').width($('.container').width());
	  $('#flash_data').delay(3000).hide('highlight', {}, 3000);
	});
");?>
<?php endif; ?>
<?php echo $this->element('viagens/fields_fotos_checklist') ?>
