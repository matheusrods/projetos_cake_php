<?php echo $this->BForm->create('ImportacaoPedidosExame', array('type' => 'file', 'enctype' => 'multipart/form-data','url' => array('controller' => 'importar', 'action' => 'importar_pedido_exame	', $this->passedArgs[0]), 'onSubmit' => '$("#carregando").show();')); ?>
<div class="well">
	<div class='row-fluid inline'>
		<p>
			<strong>Cliente: </strong><?= $grupo_economico['Cliente']['codigo'].' - '.$grupo_economico['Cliente']['razao_social'] ?>
		</p>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('nome_arquivo', array('type'=>'file', 'label' => false,'class' => 'input-xxlarge'));?> 
		<?php echo $html->link('Modelo para Importação de Pedidos de Exame', '/files/Modelo_Importacao_Pedidos_Exame.xls', array('class' => 'btn btn-success', 'escape' => false, 'target' => '_blank',  'title' => 'Visualizar Modelo para Importação de Pedidos de Exame', 'style' => 'float:right; margin-right: 50px; color:#FFF'));?>
		<div id="carregando" style="display: none; text-align: center; float: left; margin-top: 30px; margin-left: 20px;">
			<img src="/portal/img/ajax-loader.gif" border="0" />
		</div>
	</div>
	<div class='row-fluid inline'>
		<h6>(*) Somente arquivos em formato .csv - separados por ponto e vírgula( ; )</h6>
	</div>
</div>
<div class='form-actions text-left'>
	<?php echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary'));?>
	<?php echo $html->link('Voltar', array('controller' => 'clientes_funcionarios/selecao_funcionarios'), array('class' => 'btn')); ?>
</div>
<div class='lista-arquivos'>
<table class='table table-striped'>
	<thead>
		<tr>
			<th>Arquivo</th>
			<th class='input-medium'>Data Inclusão</th>
			<th class='input-medium'>Status</th>
			<th class='input-medium'>Data Processamento</th>
			<th class='input-mini'></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($arquivos_importados as $key => $value): ?>
			<tr>
				<td><?= $value['ImportacaoPedidosExame']['nome_arquivo'] ?></td>
				<td class='input-medium'><?= Comum::formataData($value['ImportacaoPedidosExame']['data_inclusao']) ?></td>
				<td class='input-medium'><?= $value['StatusImportacao']['descricao'] ?></td>
				<td class='input-medium'><?= Comum::formataData($value['ImportacaoPedidosExame']['data_processamento']) ?></td>
				<td class='input-mini'>
					<?= $this->Html->link('', array('action' => 'gerenciar_importacao_pedidos_exame', $this->passedArgs[0], $value['ImportacaoPedidosExame']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Processamento de Arquivo')) ?>
					<?php if ($value['ImportacaoPedidosExame']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR): ?>
						<?= $this->Html->link('', array('action' => 'eliminar_importacao_pedido_exame', $this->passedArgs[0], $value['ImportacaoPedidosExame']['codigo']), array('class' => 'icon-trash', 'title' => 'Eliminar Arquivo para Processamento')) ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
</div>