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

<div class="row-fluid inline" style="text-align:right;">

	<?php echo '<a id="incluir_cat" href="/portal/cat/incluir/'.$codigo_funcionario.'/'.$codigo_cliente.'/'.$codigo_funcionario_setor_cargo.'" class="btn btn-success btn-lg" ><i class="glyphicon glyphicon-share"></i> <i class="icon-plus icon-white"></i> Incluir Cat </a>'; ?>
</div>

<?php if(isset($lista_cats) && count($lista_cats)) : ?>
	<div id="listagem">
	    <table class="table table-striped">
	        <thead>
	            <tr>
		            <th class="input-small">Código CAT</th>
		            <th class="input-xlarge">Cliente</th>
		            <th class="input-small">Funcionário</th>		            
		            <th class="input-small">Matrícula</th>
		            <th class="input-xlarge" style="text-align: right;">Ações:</th>
	            </tr>
	        </thead>
	        <tbody>
	        	<?php if(count($lista_cats)) : ?>	        		
		        	<?php foreach($lista_cats as $key => $item) : ?>
			            <tr id="pedido_<?php echo $item['Cat']['codigo']; ?>">
			                <td class="input-small"><?php echo $item['Cat']['codigo']; ?></td>
			                <td class="input-xlarge"><?php echo $item['Cliente']['razao_social']; ?></td>
			                <td class="input-xlarge"><?php echo $item['Funcionario']['nome']; ?></td>
			                <td class="input-xlarge"><?php echo $item['ClienteFuncionario']['matricula']; ?></td>
			                <td class="input-xlarge">
			                	<div style="text-align: right;">				                	
				                	<?php echo $this->Html->link('', array('action' => 'imprimir_relatorio', $item['Cat']['codigo']), array('data-toggle' => 'tooltip', 'title' => 'Imprimir relatório', 'class' => 'icon-print ')); ?>
				                	<?php echo $this->Html->link('', array('action' => 'editar', $item['Cat']['codigo'], 'retificar'), array('data-toggle' => 'tooltip', 'class' => 'icon-edit ', 'title' => 'Editar')); ?>
				                	 <a href="javascript:void(0);" onclick="cat_log('<?php echo $item['Cat']['codigo']; ?>');"><i class="icon-eye-open" title="Log Cat"></i></a>
			                	</div>								
			                </td>
			            </tr>
		        	<?php endforeach; ?>	        	
	        	<?php endif; ?> 
	    	</tbody>
	    </table>
	</div>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>
<div class='form-actions well'>
	<?php echo $html->link('Voltar', array('controller' => 'cat', 'action' => 'index'), array('class' => 'btn btn-default')); ?>
</div>
	
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras(); setup_time(); setup_datepicker();
	});

	function cat_log(codigo_cat)
    {
        var janela = window_sizes();
        window.open(baseUrl + "cat/cat_log/" + codigo_cat + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    }	
'); ?>