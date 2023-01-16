
<?php	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();window.location = window.location;");
        exit;
    }
?>

<?php echo $this->Bajax->form('TRefeReferencia',array('url' => array('controller' => 'Referencias','action' => 'inativar',$codigo_referencia))) ?>
	<?php echo $this->BForm->hidden('refe_codigo') ?>
	<div class="form-actions">
		<div class="form-actions alert-error veiculo-error" style="display: block;">
			<?php if(count($origem_destino) > 0){ ?>
				<div>
					<p>Existem os seguintes relacionamentos para esse alvo:</p>
					<table width="100%">
					<tr>
						<td><b>Origem</b></td>
						<td><b>Destino</b></td>
					</tr>
					<?php foreach($origem_destino as $item) { ?>
					<tr>
						<td> <?= $item['Origem']['refe_codigo'].' - '.$item['Origem']['refe_descricao'] ?> </td>
						<td> <?= $item['Destino']['refe_codigo'].' - '.$item['Destino']['refe_descricao'] ?></td>
					</tr>
					<?php } ?>
					</table>
				</div>
				<br>
			<?php } ?>
			<?php echo 'Deseja desativar mesmo assim a Referencia ID '.$codigo_referencia ?>
		</div>

		<?php echo $this->BForm->submit('Remover', array('div' => false, 'class' => 'btn btn-danger')); ?>
		<?php echo $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>