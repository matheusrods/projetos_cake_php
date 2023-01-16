<?php if($esta_configurado):?>
<h2>Registro de Ponto</h2>
<?php echo $this->BForm->create('PontoEletronico', array('type' => 'post' ,'url' => array('controller' => 'ponto_eletronico','action' => 'ponto2')));?>
<?php echo $this->BForm->input('codigo_usuario', array('type' => 'hidden')); ?>
<?php echo $this->BForm->input('codigo_tipo_ponto_eletronico', array('type' => 'select', 'options' => $tipos_ponto_eletronico, 'class' => 'input-large', 'label' => 'Tipo de Registro','empty' => 'Selecione o tipo')); ?>
<?= ($mobile) ? '' : $this->BForm->submit('Registrar ponto', array('div' => false, 'class' => 'btn btn-success')); ?>
<?php echo $this->BForm->end(); ?>
	<table class='table table-striped table-bordered alvos'>
		<thead>
			<tr>
				<th>Usuário</th>
				<th>IP</th>
				<th>Data Ponto</th>
				<th>Data Registro</th>
				<th>Tipo de Registro</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($registros_de_ponto as $dado): ?>
				<tr>
					<td><?= $usuarios[$dado['PontoEletronico']['codigo_usuario']]; ?></td>
					<td><?= $dado['PontoEletronico']['numero_ip']; ?></td>
					<td><?= $dado['PontoEletronico']['data_ponto']; ?></td>
					<td><?= $dado['PontoEletronico']['created']; ?></td>
					<td><?= $dado['TipoPontoEletronico']['descricao_ponto_eletronico']; ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php else:?>	
	<?php echo $html->link('Voltar',array('action'=>'index') , array('class' => 'btn')); ?>	
<?php endif;?>	