<div class="lista">
	<div class="well">
		<strong>Objeto: </strong><?php echo $objeto['ObjetoAcl']['descricao']; ?>
	</div>

	<table class="table table-striped">
	    <thead>
	        <tr>
	            <th>Perfil</th>
	            <th>Código Cliente</th>
	            <th style="width:13px"></th>
	        </tr>
	    </thead>
	    <tbody>
	    	<?php foreach($permitidos as $permitido): ?>
	    		<tr>
	    			<td><?php echo $permitido['Uperfil']['descricao']; ?></td>
	    			<td><?php echo $permitido['Uperfil']['codigo_cliente']; ?></td>
	    			<td><?php echo $this->Html->link('', array('controller' => 'usuarios', 'action' => 'por_perfil', $permitido['Uperfil']['codigo']), array('class' => 'icon-eye-open')); ?></td>
	    		</tr>
	    	<?php endforeach; ?>
	    </tbody>
	</table> 
</div>
<div class="well">
	<?php echo $this->Html->link('Voltar', array('controller' => 'objetos_acl', 'action' => 'index'), array('class' => 'btn')); ?>
</div>