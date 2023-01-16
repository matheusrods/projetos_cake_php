<h4>Veiculos</h4>
<table class='table table-striped'>
	<?php $campo = isset($remonta) && $remonta == 'S' ? 'chassi' : 'placa'; ?>
	<thead>
		<th class='input-small'><?php echo Inflector::camelize($campo); ?></th>
		<th>Tipo</th>
	</thead>
	<?php foreach ($this->data['Recebsm'][$campo] as $key => $campo_valor): ?>
        <tr>
        	<td>
        		<?php echo $this->BForm->hidden('Recebsm.'.$campo.'.'.$key, array('value'=>$campo_valor)); ?>
        		<?= $this->data['Recebsm'][$campo][$key] ?>
        	</td>
        	<td>
        		<?php echo $this->BForm->hidden('Recebsm.tipo.'.$key, array('value'=>$this->data['Recebsm']['tipo'][$key])); ?>
	        	<?= $this->data['Recebsm']['tipo'][$key] ?>
        	</td>
        </tr>
	<?php endforeach ?>
</table>