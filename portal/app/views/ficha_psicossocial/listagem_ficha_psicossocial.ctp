<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $dados_cliente_funcionario['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $dados_cliente_funcionario['Cliente']['nome_fantasia'], 'class' => 'input-xlarge', 'label' => 'Unidade' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $dados_cliente_funcionario['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Setor.descricao', array('value' => $dados_cliente_funcionario['Setor']['descricao'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
	
	<div class="clear"></div>
	<?php echo $this->BForm->input('Funcionario.nome', array('value' => $dados_cliente_funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.cpf', array('value' => $dados_cliente_funcionario['Funcionario']['cpf'], 'class' => 'input-xlarge', 'label' => 'CPF' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.data_nascimento', array('value' => $dados_cliente_funcionario['Funcionario']['data_nascimento'], 'class' => 'input-xlarge', 'label' => 'Data nascimento' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cargo.descricao', array('value' => $dados_cliente_funcionario['Cargo']['descricao'], 'class' => 'input-xlarge', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>	
	<div class="clear"></div>
</div>
<div class="row-fluid inline" style="text-align:right; padding: 10px 0;">
	<?php echo $this->BForm->create('FuncionarioSetorCargo', array('type' => 'post' ,'url' => array('controller' => 'ficha_psicossocial','action' => 'listagem_pedido_de_exame', $codigo_funcionario_setor_cargo, $codigo_cliente_funcionario), 'title' =>'Cadastrar Novas Fichas Psicossocial')); ?>
	<?php echo $this->BForm->input('FuncionarioSetorCargo.0.codigo', array('type' => 'hidden', 'value' => $codigo_funcionario_setor_cargo)); ?>
	<button id="botao" type="submit" class="btn btn-success btn-lg" ><i class="glyphicon glyphicon-share"></i> <i class="icon-plus icon-white"></i> Incluir Ficha </button>
	<?php echo $this->BForm->end(); ?>	
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-medium">Código Ficha</th>
			<th class="input-medium">Código Pedido</th>
			<th class="input-medium">Cliente</th>
			<th class="input-medium">Funcionário</th>
			<th class="acoes" style="width:25px">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($ficha_psicossocial as $dados): ?>
			<tr>
				<td class="input-mini"><?php echo $dados['FichaPsicossocial']['codigo'] ?></td>
				<td class="input-mini"><?php echo $dados['FichaPsicossocial']['codigo_pedido_exame'] ?></td>
				<td class="input-mini"><?php echo $dados['Cliente']['razao_social'] ?></td>
				<td class="input-mini"><?php echo $dados['Funcionario']['nome'] ?></td>
				
				<td>
					<?php echo $this->Html->link('', array('action' => 'editar',$dados['FichaPsicossocial']['codigo_pedido_exame'], $dados['FichaPsicossocial']['codigo']), array('data-toggle' => 'tooltip', 'class' => 'icon-edit ', 'title' => 'Editar')); ?>&nbsp;
					<?php echo $this->Html->link('', array('action' => 'imprimir_relatorio', $dados['FichaPsicossocial']['codigo']), array('data-toggle' => 'tooltip', 'title' => 'Imprimir relatório', 'class' => 'icon-print ')); ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FichaPsicossocial']['count']; ?></td>
		</tr>
	</tfoot>    
</table>
<div class='row-fluid'>
	<div class='numbers span6'>
		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	</div>
	<div class='counter span7'>
		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		
	</div>
</div>
<div class="modal fade" id="modal_carregando">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, carregando informações...</h4>
			</div>
			<div class="modal-body">
				<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
			</div>
		</div>
	</div>
</div>
<div class='form-actions well'>
	<?php echo $html->link('Voltar', array('controller' => 'ficha_psicossocial', 'action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>