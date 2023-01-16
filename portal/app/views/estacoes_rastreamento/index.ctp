<div class='form-procurar'>
	<div class='well'>
		<?php echo $this->BForm->create('TErasEstacaoRastreamento', array('autocomplete' => 'off', 'url' => array('controller' => 'estacoes_rastreamento', 'action' => 'index'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('eras_codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
			<?php echo $this->BForm->input('eras_descricao', array('class' => 'input-medium', 'placeholder' => 'Descrição', 'label' => false)) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<table class='table'>
	<thead>
		<th class='input-small numeric'>Código</th>
		<th>Descrição</th>
		<th></th>
	</thead>
	<tbody>			
		<?php foreach($dados as $valor): ?>
			<tr>
				<td class='numeric'><?php echo $valor['TErasEstacaoRastreamento']['eras_codigo']; ?></td>
				<td><?php echo $valor['TErasEstacaoRastreamento']['eras_descricao']; ?></td>
				<td class='action-icon'><?= $this->Html->link('', array('controller' => 'estacoes_areas_atuacoes', 'action' => 'gerenciar', $valor['TErasEstacaoRastreamento']['eras_codigo']), array('class' => 'icon-wrench', 'title' => 'gerenciar areas de atuacao do operador')) ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>