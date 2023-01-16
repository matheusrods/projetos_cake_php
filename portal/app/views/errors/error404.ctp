<h3><?php echo $name; ?></h3>
<p class="error">
	<strong><?php __('Error'); ?>: </strong>
	<p><i>Página não encontrada</i></p>
	<?php echo sprintf(__("The requested address %s was not found on this server.", true), "<strong>'{$message}'</strong>")?>
</p>