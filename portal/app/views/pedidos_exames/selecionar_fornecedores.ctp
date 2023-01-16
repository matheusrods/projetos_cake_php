<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $dados_cliente_funcionario['Empresa']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $dados_cliente_funcionario['Cliente']['razao_social'], 'class' => 'input-xlarge', 'label' => 'Unidade' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $dados_cliente_funcionario['Empresa']['codigo_documento'], 'class' => 'input-xlarge', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Setor.descricao', array('value' => $dados_cliente_funcionario['ClienteFuncionario']['setor'], 'class' => 'input-xlarge', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
	<div class="clear"></div>
	<?php echo $this->BForm->input('Funcionario.nome', array('value' => $dados_cliente_funcionario['Funcionario']['nome'], 'class' => 'input-xlarge', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.cpf', array('value' => $dados_cliente_funcionario['Funcionario']['cpf'], 'class' => 'input-xlarge', 'label' => 'CPF' , 'readonly' => true, 'type' => 'text')); ?>		
	<?php echo $this->BForm->input('Funcionario.data_nascimento', array('value' => $dados_cliente_funcionario['Funcionario']['data_nascimento'], 'class' => 'input-xlarge', 'label' => 'Data nascimento' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cargo.descricao', array('value' => $dados_cliente_funcionario['ClienteFuncionario']['cargo'], 'class' => 'input-xlarge', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>
	<div class="clear"></div>
</div>

<div class='inline well' id="parametros">
	<img src="/portal/img/default.gif" style="padding: 10px;">
	Carregando parametrizações do pedido...
</div>

<div class="inline well">
	<h4 style="color: #888;">Endereço Referência p/ Busca dos Fornecedores:</h4>

	<?php echo $this->BForm->input('cep', array('value' => $_SESSION['cliente_funcionario'][$this->passedArgs[0]]['parametros_busca']['endereco']['cep'], 'class' => 'input-small', 'label' => 'Cep' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('endereco', array('value' => $_SESSION['cliente_funcionario'][$this->passedArgs[0]]['parametros_busca']['endereco']['endereco'], 'class' => 'input-xlarge', 'label' => 'Logradouro', 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('numero', array('value' => $_SESSION['cliente_funcionario'][$this->passedArgs[0]]['parametros_busca']['endereco']['numero'], 'class' => 'input-small', 'label' => 'Número' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('cidade', array('value' => $_SESSION['cliente_funcionario'][$this->passedArgs[0]]['parametros_busca']['endereco']['cidade'], 'class' => 'input-large', 'label' => 'Cidade' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('estado', array('value' => $_SESSION['cliente_funcionario'][$this->passedArgs[0]]['parametros_busca']['endereco']['estado'], 'class' => 'input-small', 'label' => 'Estado' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('raio', array('value' => ($raio ? $raio : '30'), 'class' => 'input-small', 'label' => 'Raio Km', 'type' => 'text')); ?>
	
	<label><br /></label>
	<a href="javascript:void(0);" onclick="recarrega_raio( '<?php echo $this->passedArgs[0]; ?>', $('#raio').val() );" class="btn btn-primary">Refazer Busca!</a>
	
	<div style="clear: both;"></div>
</div>
	
<div id="caminho-pao"></div>	
	
<?php if(count($dados_fornecedores_disponiveis)): ?>
	
	<?php echo $this->BForm->create('PedidosExames', array('url' => array('controller' => 'pedidos_exames','action' => 'selecionar_fornecedores', $this->passedArgs[0]))); ?>
		<?php echo $this->BForm->hidden('codigo_cliente_funcionario', array('value' => $this->passedArgs[0])); ?>
	    <table class="table table-striped">
	        <thead>
	            <tr>
	            	<th class="input-small">Codigo</th>
		            <th style="width: 600px;">Fornecedor</th>
		            <th>Endereço</th>
	                <th style="width: 300px;">
	                	Atendimento
	                </th>		            
		            <?php foreach($array_exames as $key => $exame) : ?>
		            	<th class="input-small center" style="width: 280px; text-align: center; background: <?php echo (isset($SelecionaFornecedores) && !isset($SelecionaFornecedores["seleciona_exame_{$key}"])) ? '#FFDBDB; border: 2px solid #888' : ''; ?>;">
		            		<?php echo $exame; ?>
		            	</th>
		            <?php endforeach; ?>
	                <th class="input-small" style="text-align: right;">Distância</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php foreach ($array_ordenado as $k_tudo => $fornecedores): ?>
	            <tr>
	            	<td class="input-small"><?php echo $k_tudo; ?></td>
	                <td style="width: 600px; font-size: 11px;">
	                	<span data-toggle="tooltip" title="<?php echo $array_fornecedores[$k_tudo]['razao_social'] ?>"><?php echo $this->Buonny->leiaMais($array_fornecedores[$k_tudo]['razao_social'], 35); ?></span><br />
	                	(<span data-toggle="tooltip" title="<?php echo $array_fornecedores[$k_tudo]['nome_fantasia'] ?>" style="font-size: 9px;"><?php echo $this->Buonny->leiaMais($array_fornecedores[$k_tudo]['nome_fantasia'], 40); ?></span>)<br />
	                	<?php if(!empty($array_fornecedores[$k_tudo]['telefone'])) { ?>
	                	<strong>Telefone:</strong> <?php  echo $array_fornecedores[$k_tudo]['telefone']; ?>
	                	<?php } ?>
	                </td>
	                <td>
	                	<i data-toggle="tooltip" data-html="true" title="<address><?php echo $array_fornecedores[$k_tudo]['endereco'] ?>, <?php echo $array_fornecedores[$k_tudo]['numero'] ?>, <?php echo $array_fornecedores[$k_tudo]['complemento'] ?><br /> <?php echo $array_fornecedores[$k_tudo]['cidade'] ?> - <?php echo $array_fornecedores[$k_tudo]['estado'] ?></address>" class="icon-home"></i>
	                </td>
	                <td style="width: 300px; font-size: 11px;">
						<?php echo $array_organizado_fornecedor[$k_tudo]['ListaPrecoProdutoServico']['tipo_atendimento'] == '0' ? 'Ordem de Chegada' : 'Hora Marcada'; ?>
	                </td>	                
	                <?php foreach($array_exames as $k_exame => $item_exame) : ?>
	                	<td class="input-small center" style="width: 280px; text-align: center; font-size: 11px; background: <?php echo (isset($SelecionaFornecedores) && !isset($SelecionaFornecedores["seleciona_exame_{$k_exame}"])) ? '#FFDBDB; border: 1px solid #CCC;' : ''; ?>">
	                		<?php if(isset($fornecedores[$k_exame]['Exame']['descricao'])) : ?>
	                			<input type="radio" name="data[SelecionaFornecedores][seleciona_exame_<?php echo $k_exame; ?>]" value="<?php echo $k_tudo; ?>" multiple="multiple" <?php echo ((isset($SelecionaFornecedores) && isset($SelecionaFornecedores["seleciona_exame_{$k_exame}"]) && $SelecionaFornecedores["seleciona_exame_{$k_exame}"] == $k_tudo)  || (isset($array_exame_mais_barato[$k_exame]) && $array_exame_mais_barato[$k_exame]['fornecedor'] == $k_tudo)) ? ' checked="checked"' : ''; ?> /> <br />
	                			Selecionar
	                			<br />
		                		<?php if((isset($array_exame_mais_barato[$k_exame]) && $array_exame_mais_barato[$k_exame]['fornecedor'] == $k_tudo) && !$eh_cliente) : ?>
		                			<label class="label label-success">Melhor Custo</label>
		                		<?php endif; ?>
	                		<?php else : ?>
	                			---
	                		<?php endif; ?>
	                	</td>
	                <?php endforeach; ?>
	                <td class="input-small" style="text-align: right;"><?php echo isset($array_fornecedores_distancia[$k_tudo]) ? $array_fornecedores_distancia[$k_tudo] : '-'; ?></td>
	            </tr>
	        	<?php endforeach ?>
	    	</tbody>
	    </table>
		<div class='form-actions well'>
			<a href="/portal/pedidos_exames/incluir/<?php echo $this->passedArgs[0]; ?>" class="btn">Voltar</a>
			<a href="javascript:void(0);" onclick="$('#PedidosExamesSelecionarFornecedoresForm').submit();" class="btn btn-primary">Avançar</a>
		</div>
	<?php echo $this->BForm->end(); ?>
	
<?php else:?>
	<div class="alert">Nenhum fornecedor foi encontrato no raio de <?php echo $raio; ?> Km do endereço selecionado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		setup_mascaras();
		
		atualiza_parametros("'.$codigo_cliente_funcionario.'");
		
		// seta etapa
		$("#caminho-pao").load("/portal/pedidos_exames/caminho_pao/2");
	});
		
	function atualiza_parametros(codigo_cliente_funcionario) {
		$("#parametros").load("/portal/pedidos_exames/carrega_parametros/'.$codigo_cliente_funcionario.'");
	}		
		
	function recarrega_raio(codigo_cliente_funcionario, raio) {
		window.location = "/portal/pedidos_exames/selecionar_fornecedores/" + codigo_cliente_funcionario + "/" + raio;
	}
'); ?>