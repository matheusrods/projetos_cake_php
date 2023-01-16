<?php echo $this->BForm->create('ImportacaoEstrutura', array('type' => 'file', 'enctype' => 'multipart/form-data','url' => array('controller' => 'importar', 'action' => 'importar_funcionario', $this->passedArgs[0], $referencia, $terceiros_implantacao), 'onSubmit' => '$("#carregando").show();')); ?>
<div class="well">
	<div class='row-fluid inline'>
		<p>
			<strong>Cliente: </strong><?= $grupo_economico['Cliente']['codigo'].' - '.$grupo_economico['Cliente']['razao_social'] ?>
		</p>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('nome_arquivo', array('type'=>'file', 'label' => false,'class' => 'input-xxlarge'));?> 
		<?php echo $html->link('Modelo para Importação', '/files/Modelo_Importacao_Funcionario.xls', array('class' => 'btn btn-success', 'escape' => false, 'target' => '_blank',  'title' => 'Visualizar Modelo para Importação', 'style' => 'float:right; margin-right: 50px; color:#FFF'));?>
		<div id="carregando" style="display: none; text-align: center; float: left; margin-top: 30px; margin-left: 20px;">
			<img src="/portal/img/ajax-loader.gif" border="0" />
		</div>
	</div>
	<div class='row-fluid inline'>
		<h6>(*) Somente arquivos em formato .csv - separados por ponto e vírgula( ; )</h6>
	</div>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary'));?>
	<?php if($terceiros_implantacao == 'terceiros_implantacao'): ?>
		<?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $this->passedArgs[0],'implantacao', $terceiros_implantacao), array('class' => 'btn')); ?>
	<?php else: ?>
		<?php echo $html->link('Voltar', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $this->passedArgs[0],'implantacao'), array('class' => 'btn')); ?>
	<?php endif; ?>
</div>
<div class='lista-arquivos'>
<table class='table table-striped'>
	<thead>
		<tr>
			<th>Arquivo</th>
			<th class='input-medium'>Usuario Inclusão</th>
			<th class='input-medium'>Data Inclusão</th>
			<th class='input-medium'>Status</th>
			<th class='input-medium'>Data Processamento</th>
			<th class='input-mini'></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($arquivos_importados as $key => $value): ?>
			<tr>
				<td><?= $value['ImportacaoEstrutura']['nome_arquivo'] ?></td>
				<td><?= $value['Usuario']['apelido'] ?></td>
				<td class='input-medium'><?= $value['ImportacaoEstrutura']['data_inclusao'] ?></td>
				<td class='input-medium'>
					<?php if($value['StatusImportacao']['codigo'] == 1): ?>
						<span class='label label-info'>Sem processar</span>
					<?php elseif($value['StatusImportacao']['codigo'] == 2): ?>
						<span class='label label-warning'>Em processamento</span>
					<?php elseif($value['StatusImportacao']['codigo'] == 3): ?>
						<span class='label label-success'>Processado</span>
					<?php else: ?>
						<span class='label label-important'>Erro</span>
					<?php endif; ?>
				</td>
				<td class='input-medium'><?= $value['ImportacaoEstrutura']['data_processamento'] ?></td>
				<td class='input-mini'>
					<?= $this->Html->link('', array('action' => 'gerenciar_importacao_estrutura', $this->passedArgs[0], $value['ImportacaoEstrutura']['codigo'], $referencia, $terceiros_implantacao), array('class' => 'icon-wrench', 'title' => 'Gerenciar processamento')) ?>
					<?php if ($value['ImportacaoEstrutura']['codigo_status_importacao'] == StatusImportacao::SEM_PROCESSAR): ?>
						<?= $this->Html->link('', array('action' => 'eliminar_importacao_funcionario', $this->passedArgs[0], $value['ImportacaoEstrutura']['codigo'], $referencia, $terceiros_implantacao), array('class' => 'icon-trash', 'title' => 'Eliminar arquivo para processamento')) ?>
					<?php endif ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
</div>