<?php echo $this->BForm->create('TEaatEstacaoAreaAtuacao', array('autocomplete' => 'off', 'url' => array('controller' => 'estacoes_areas_atuacoes', 'action' => 'adicionar_estacao_area_atuacao'))) ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('eaat_eras_codigo', array('value' => $eras_codigo)) ?>
    <?php echo $this->BForm->input('eaat_aatu_codigo', array('label' => 'Área de Atuação', 'options' => $areas_atuacoes, 'empty' => 'Selecione uma área de atuação')); ?>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('action' => 'gerenciar', $eras_codigo), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
		
	