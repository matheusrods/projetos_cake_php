<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $eras_codigo ?>
		<strong>Operador: </strong><?= $descricao['TErasEstacaoRastreamento']['eras_descricao'] ?>
	</div>
</div>

<?php echo $this->BForm->create('TEaatEstacaoAreaAtuacao', array('autocomplete' => 'off', 'url' => array('controller' => 'estacoes_areas_atuacoes', 'action' => 'gerenciar'))) ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('eaat_eras_codigo', array('value' => $eras_codigo)) ?>
    <?php echo $this->BForm->input('eaat_aatu_codigo', array('label' => false, 'options' => $areas_atuacoes, 'empty' => 'Selecione uma área de atuação')); ?>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'estacoes_rastreamento', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
		
<?php if (!empty($dados)): ?>
	<table class='table'>
		<thead>
			<th class='input-small numeric'>Código</th>
			<th>Descrição</th>
			<th></th>
		</thead>
		<tbody>			
			<?php foreach($dados as $valor): ?>
				<tr>
					<td class='numeric'><?php echo $valor['TAatuAreaAtuacao']['aatu_codigo']; ?></td>
					<td><?php echo $valor['TAatuAreaAtuacao']['aatu_descricao']; ?></td>
					<td class='action-icon'><?= $html->link('', array('controller' => 'estacoes_areas_atuacoes', 'action' => 'delete', $valor['TEaatEstacaoAreaAtuacao']['eaat_eras_codigo'], $valor['TAatuAreaAtuacao']['aatu_codigo']), array('class' => 'icon-trash', 'title' => 'Excluir área de atuação da estação'), 'Confirma exclusão?'); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
	<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
	
<?php endif ?>