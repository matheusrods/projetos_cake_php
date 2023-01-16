<table>
    <thead>
        <tr>
            <th>Representantes Vinculados</th>
            <th></th>
        </tr>
    </thead>
    <?php if (isset($cliente['Representante'])): ?>
	    <?php foreach ($cliente['Representante'] as $representante) : ?>
	        <tr>
	            <td><?php echo $representante['nome'] ?></td>
	            <td><?php echo $html->link('Excluir', array('action' => 'excluir', $representante['ClienteRepresentante']['codigo_cliente'], $representante['ClienteRepresentante']['codigo'])) ?></td>
	        </tr>
	    <?php endforeach; ?>
		<?php endif; ?>
</table>