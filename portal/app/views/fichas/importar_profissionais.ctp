<?php echo $this->BForm->create('Ficha', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'fichas', 'action' => 'importar_profissionais'))); ?>
	
<?php if(!$cliente): ?>
	<div class="row-fluid inline">
	    <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true); ?>
	    
    </div>
   	<?php echo $this->BForm->submit('Consultar', array('div' => false, 'class' => 'btn btn-primary')); ?>

<?php else: ?>
	<?php echo $this->BForm->hidden('codigo_cliente'); ?>
	<div class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
		<span class="pull-right">
			<?php echo $this->Html->Link(
			'<i class="cus-page-white-excel"></i>&nbsp;Arquivo Exemplo', 
			'/files/Modelo_de_Importacao_Profissional.xls', 
			array(
				'class'  => 'button', 
				'target' => '_blank',
				'escape' => false,
				)
			);?>
	    </span>
	</div>
	<div class='row-fluid inline'>      
	    <?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label'=>'Selecione o arquivo CSV', 'class' => 'input-xlarge' )); ?>
	</div>
	<div class="form-actions">
	    <?php echo $this->BForm->submit('Processar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	    <?php if(!$authUsuario['Usuario']['codigo_cliente']) echo $this->Html->link('Voltar',array('controller' => 'fichas','action' => 'importar_profissionais'),array('class' => 'btn')); ?>
	</div>
	<?php if(count($arquivos_processando_cliente) > 0): ?>
		<h4>Arquivos sendo processados</h4>
		<table class='table table-striped'>
			<thead>
				<th class='input-medium'>Data Importação</th>
				<th>Arquivo</th>
			</thead>
			<tbody>
				<?php foreach($arquivos_processando_cliente as $arquivo): ?>
					<tr>
						<td><?php echo $arquivo['data'] ?></td>
						<td><?php echo $arquivo['name'] ?></td>
					</tr>
				<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
	<?php if(count($arquivos_processados_cliente) > 0): ?>
		<h4>Arquivos processados</h4>
		<table class='table table-striped'>
			<thead>
				<th class='input-medium'>Data Importação</th>
				<th>Arquivo</th>
				<th class="action-icon"></th>
			</thead>
			<tbody>
				<?php foreach($arquivos_processados_cliente as $key => $arquivo): ?>
					<tr>
						<td><?php echo $arquivo['data'] ?></td>
						<td><?php echo $arquivo['name'] ?></td>
						<td><?php echo $this->Html->link('<i class="cus-page-white-excel"></i>',array('controller' => 'fichas','action' => 'ver_arquivo_importado',$arquivo['name_encoded']),array('class' => 'icon-edit','escape' => false)) ?></td>
					</tr>
				<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
<?php endif; ?>	