<?php echo $this->BForm->create('InformacaoCliente', array('action' => 'editar', $this->passedArgs[0] )); ?>
<div id="cliente" class='well'>
	<strong>Código: </strong><?= $this->data['InformacaoCliente']['codigo'] ?>
	<strong>Razão Social: </strong><?= $this->data['InformacaoCliente']['razao_social'] ?>
	<strong>Nome Fantasia: </strong><?= $this->data['InformacaoCliente']['nome_fantasia'] ?>
	<strong>CNPJ: </strong><?= $buonny->documento($this->data['InformacaoCliente']['codigo_documento']) ?>
</div>

<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_area_atuacao', array('options' => $areasAtuacao, 'empty' => '--- Selecione ---', 'class' => 'input-medium', 'label' => 'Área Buonnysat')); ?>
	<?php echo $this->BForm->input('codigo_sistema_monitoramento', array('options' => $sistemasMonitoramento, 'empty' => '--- Selecione ---', 'class' => 'input-medium', 'label' => 'Sistema de Monitoramento')); ?>
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>