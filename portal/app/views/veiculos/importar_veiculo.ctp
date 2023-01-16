<div class='well'>
	<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
	<strong style="margin:0 0 0 20px">Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    <span class="pull-right">
		<?php echo $this->Html->Link(
		'<i class="cus-page-white-excel"></i>&nbsp;Arquivo Exemplo', 
		'/files/Modelo_de_Importacao_veiculo.xls', 
		array(
			'class'  => 'button', 
			'target' => '_blank',
			'escape' => false,
			)
		);?>
    </span>
</div>
<?php echo $this->BForm->create('Veiculo', array('type'=>'file')); ?>	
<div class='row-fluid inline'>	
	<?php echo $this->BForm->input('arquivo_veiculo', array('type'=>'file', 'label'=>'Selecione o arquivo CSV', 'class' => 'input-xlarge' )); ?>
	<?php echo $this->BForm->hidden('Cliente.codigo', array('value'=>$cliente['Cliente']['codigo'])); ?>
	<?php echo $this->BForm->hidden('Cliente.codigo_documento', array('value'=>$cliente['Cliente']['codigo_documento'])); ?>
</div>
<div class="form-actions">
	<?php echo $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('action' => 'adicionar_veiculo'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>