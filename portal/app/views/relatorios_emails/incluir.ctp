<div>
	<h5><i>Prezado Cliente,</i></h5>
<?php if(empty($erros)) :?>
	<p>
		em instantes o relatório será enviado para <strong><?php echo $this->data['RelatorioEmail']['email']?></strong>.
	</p>		
<?php else :?>
	não é possivel enviar o relatório por email pois <br />
	<p>
	<?php foreach ($erros as $erro ) : ?>
		<span style="color:red;"><?php echo $erro;?></span><br />
	<?php endforeach;?>
	</p>
<?php endif; ?>
</div>
<div class='form-actions'>
	<?php echo $html->link('Ok', 'javascript:void(0)', array( 'onclick' => "close_dialog();",'class' => 'btn' ));?>
</div>