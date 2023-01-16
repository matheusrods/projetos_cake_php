<div class='row-fluid inline'>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $usua_oras_codigo ?>
		<strong>Operador: </strong><?= $descricao['TPessPessoa']['pess_nome'] ?>
	</div>
</div>

<?php echo $this->BForm->create('TUaatUsuarioAreaAtuacao', array('autocomplete' => 'off', 'url' => array('controller' => 'operadores', 'action' => 'gerenciar_areas_atuacoes'))) ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('uaat_usua_oras_codigo', array('value' => $usua_oras_codigo)) ?>
    <?php echo $this->BForm->input('uaat_aatu_codigo', array('label' => false, 'options' => $areas_atuacoes, 'empty' => 'Selecione uma área de atuação')); ?>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'operadores', 'action' => 'index'), array('class' => 'btn')); ?>
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
					<td class='action-icon'><?= $html->link('', array('controller' => 'operadores', 'action' => 'delete', $valor['TUaatUsuarioAreaAtuacao']['uaat_usua_oras_codigo'], $valor['TAatuAreaAtuacao']['aatu_codigo']), array('class' => 'icon-trash', 'title' => 'Excluir área de atuação da estação'), 'Confirma exclusão?'); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
	<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
	
<?php endif ?>