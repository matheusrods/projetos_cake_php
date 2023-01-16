<div class='actionbar-right'>
	<?php echo $this->Html->link('Incluir', array('action' => 'incluir', rand()), array('title' => 'Incluir', 'class' => 'btn btn-success'));?>
</div>
<div class='row-fluid inline'>
	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Descrição</th>
				<th style='width:40px'></th>
	        </tr>
	    </thead>
	    <tbody>
	        <?php foreach ($alertasTipos as $alertaTipo): ?>
	        <tr>
	            <td><?= $alertaTipo['AlertaTipo']['descricao'] ?></td>
	            <td>
					<?= $this->Html->link('', array('action' => 'editar', $alertaTipo['AlertaTipo']['codigo'], rand()), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
					<?= $this->Html->link('', array('action' => 'excluir', $alertaTipo['AlertaTipo']['codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash')) ?>
				</td>
	        </tr>
	        <?php endforeach; ?>        
	    </tbody>
	</table>
</div>
