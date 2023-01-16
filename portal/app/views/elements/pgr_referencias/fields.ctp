<div class="well">
	<div class='row-fluid inline'>
		<?php if($readonly): ?>
			<?php echo $this->BForm->hidden('codigo_cliente',Array('value'=>$cliente['Cliente']['codigo'])) ?>
			<?php echo $this->BForm->input('codigo_cliente_visual',array('readonly'=>true,'label'=>'Cliente','value'=>$cliente['Cliente']['razao_social'])) ?>
		<?php else: ?>
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'TPrefPgrReferencia',(isset($cliente['Cliente']['codigo']) ? $cliente['Cliente']['codigo'] : '') ) ?>
		<?php endif; ?>
		<?php echo $this->BForm->input('pref_pgpg_codigo', array('class' => 'input-small', 'label' => 'PGR', 'empty' => 'Todos', 'options' => $pgrs)) ?>
	</div>
	<div class='row-fluid inline'>
		<?php if($readonly): ?>
			<?php echo $this->BForm->hidden('pref_refe_codigo') ?>
			<?php echo $this->BForm->input('bvre_refe_codigo_visual',array('readonly'=>true,'label'=>'Alvo','value'=>$this->data['TRefeReferencia']['refe_descricao'])) ?>
		<?php else: ?>
			<?php echo $this->Buonny->input_referencia($this, '#TPrefPgrReferenciaCodigoCliente', 'TPrefPgrReferencia', 'pref_refe_codigo', false, 'Alvo', 'Alvo'); ?>
		<?php endif; ?>
	</div>
</div>
