
<div class='form-procurar'>	
    <div class='well'>
	    <?php echo $this->BForm->create('TCtecContaTecnologia', array('autocomplete' => 'off', 'url' => array('controller' => 'contas_tecnologias', 'action' => 'consultar_placa'))) ?>
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->input('veic_placa', array('class' => 'placa-veiculo input-small', 'label' => false, 'placeholder' => 'Placa')); ?>
	    </div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	    <?php echo $this->BForm->end();?>
	</div>
</div>
<?php if (isset($dados)): ?>
	<table class='table table-striped'>
		<thead>
			<tr>
				<td>Tecnologia</td>
				<td>Último recebimento</td>
				<td>Última posicao</td>
			</tr>
		</thead>
		<tbody>
			<?php foreach($dados as $dado): ?>
				<tr>
					<td><?= $dado['TCtecContaTecnologia']['ctec_descricao'] ?></td>
					<td><?= $dado[0]['data_ultimo_recebimento'] ?></td>
					<td><?= $dado[0]['data_ultima_posicao'] ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>
<?= $this->Javascript->codeBlock('jQuery(document).ready(function () {setup_mascaras() })') ?>