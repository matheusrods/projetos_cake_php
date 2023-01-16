<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo']; ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	</div>
</div>

<?php echo $this->BForm->create('TVeicVeiculo', array('url' => array('controller' => 'Veiculos','action' => 'localizar_veiculo',$cliente['Cliente']['codigo'])));?>
<div id="form-pai" class='row-fluid inline'>
	<?php echo $this->BForm->input('veic_placa', array('label' => 'Placa','type' => 'text','class' => 'input-small placa-veiculo')) ?>
</div>

<div class="form-actions">
	<?php echo $this->BForm->submit('Localizar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar', 'adicionar_veiculo', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(function(){
		setup_mascaras();
	});', false);
?>