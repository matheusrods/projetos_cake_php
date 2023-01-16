<?php $cidades_origem = array(); ?>
<?php $cidades_destinos = array(); ?>
<?php $ativo = array('A' => 'Ativa', 'D' => 'Inativa'); ?>
<div class='row-fluid inline'>
	<div style=<?php echo (!isset($this->passedArgs['searcher']) ? "''": "'display:none'") ?> >
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'TRotaRota') ?>
	</div>
	<?php echo $this->BForm->input('rota_codigo', array('label' => false, 'placeholder' => 'Codigo','type' => 'text', 'class' => 'input-small just-number')) ?>
	<?php echo $this->BForm->input('rpon_descricao_origem', array('label' => false, 'placeholder' => 'Alvo Origem','type' => 'text', 'class' => 'input-large')) ?>
	<?php echo $this->BForm->input('rpon_descricao_destino', array('label' => false, 'placeholder' => 'Alvo Destino','type' => 'text', 'class' => 'input-large')) ?>
	<?php echo $this->BForm->input('rota_ativo', array('label' => false, 'empty' => 'Status','options' => $ativo, 'class' => 'input-small')) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('rota_descricao', array('label' => false, 'placeholder' => 'Descricao','type' => 'text', 'class' => 'input-large')) ?>
	<?php echo $this->BForm->input('rota_observacao', array('label' => false, 'placeholder' => 'Observacao','type' => 'text', 'class' => 'input-large')) ?>
</div>
<div class='row-fluid inline'>
  <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
  <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>