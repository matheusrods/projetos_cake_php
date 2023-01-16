<?php if( isset($invalido) && ($invalido == 0) ) : ?>
	<?php if(isset($array_fornecedores[$codigo_cliente]) && count($array_fornecedores[$codigo_cliente])) : ?>
		<table class="table table-striped" style="border: 2px solid #EFEFEF">
	        <thead>
	            <tr>
	            	<th class="input-small">Codigo</th>
		            <th style="width: 600px;">Fornecedor</th>
		            <th>Endereço</th>
	                <th>Atendimento</th>
		            <?php foreach($array_exames[$codigo_cliente] as $k_exame => $exame) : ?>
		            	<th class="input-small center" style="width: 280px; text-align: center; background: <?php echo (isset($SelecionaFornecedores) && !isset($SelecionaFornecedores["seleciona_exame_{$codigo_cliente}_{$k_exame}"])) ? '#FFDBDB; border: 2px solid #888' : ''; ?>;">
		            		<?php echo $exame; ?>
		            	</th>
		            <?php endforeach; ?>					
	                <th class="input-small" style="text-align: right;">Distância</th>
					<th></th>
	            </tr>
	        </thead>
	        <?php if(isset($array_fornecedores[$codigo_cliente])) : ?>
				<tbody>	
					<?php foreach($array_fornecedores[$codigo_cliente] as $k_fornecedor => $fornecedores) : ?>
			            <tr>
			            	<td class="input-small"><?php echo $k_fornecedor; ?></td>
			                <td style="width: 600px; font-size: 11px;">
			                	<?php echo $this->Buonny->leiaMais($fornecedores['Fornecedor']['razao_social'], 40); ?><br />
			                	(<span style="font-size: 9px;"><?php echo $this->Buonny->leiaMais($fornecedores['Fornecedor']['nome'], 60); ?></span>)<br />
			                	<?php if(!empty($fornecedores['Servico']['telefone'])) : ?>
			                		<strong>Telefone:</strong> <?php  echo $fornecedores['Servico']['telefone']; ?>
			                	<?php endif; ?>	                	
			                </td>
			                <td>
			                	
			                	<?php 
			                	$fornecedor_endereco = $fornecedores['FornecedorEndereco']['logradouro'];

			                	if(!empty($fornecedores['FornecedorEndereco']['numero']) && trim($fornecedores['FornecedorEndereco']['numero']) <> '') {
			                		$fornecedor_endereco .= ", ".$fornecedores['FornecedorEndereco']['numero'];
			                	}

			                	if(!empty($fornecedores['FornecedorEndereco']['complemento']) && trim($fornecedores['FornecedorEndereco']['complemento']) <> '') {
			                		$fornecedor_endereco .= ", ".$fornecedores['FornecedorEndereco']['complemento'];
			                	}

			                	if(!empty($fornecedores['FornecedorEndereco']['bairro']) && trim($fornecedores['FornecedorEndereco']['bairro']) <> '') {
			                		$fornecedor_endereco .= ", ".$fornecedores['FornecedorEndereco']['bairro'];
			                	}

			                	if(!empty($fornecedores['FornecedorEndereco']['cidade']) && trim($fornecedores['FornecedorEndereco']['cidade']) <> '') {
			                		$fornecedor_endereco .= ", ".$fornecedores['FornecedorEndereco']['cidade'];
			                	}

			                	if(!empty($fornecedores['FornecedorEndereco']['estado_descricao']) && trim($fornecedores['FornecedorEndereco']['estado_descricao']) <> '') {
			                		$fornecedor_endereco .= " - ".$fornecedores['FornecedorEndereco']['estado_descricao'];
			                	}
			                	?>

			                	<i data-toggle="tooltip" data-html="true" title="<?php echo $fornecedor_endereco; ?>" class="icon-home"></i>

			                </td>
			                <td style="width: 300px; font-size: 11px;">
			                	<!-- trecho alterado pra enxergar somente o tipo de atendimento do fornecedor nao do servico -->
								<?php echo $fornecedores['Fornecedor']['tipo_atendimento'] == '0' ? 'Ordem de Chegada' : 'Hora Marcada'; ?>
			                </td>
	                
	                		<?php foreach($array_exames[$codigo_cliente] as $k_exame => $exame) : ?>
			                	<td class="input-small center" style="width: 280px; text-align: center; font-size: 11px; background: <?php echo (isset($SelecionaFornecedores) && !isset($SelecionaFornecedores["seleciona_exame_{$codigo_cliente}_{$k_exame}"])) ? '#FFDBDB; border: 1px solid #CCC;' : ''; ?>">
			                	
			                		<?php if(isset($array_exames_fornecedores[$codigo_cliente][$k_fornecedor][$k_exame])) : ?>
			                			<input type="radio" name="data[SelecionaFornecedores][seleciona_exame_<?php echo $codigo_cliente; ?>_<?php echo $k_exame; ?>]" value="<?php echo $k_fornecedor; ?>" multiple="multiple" <?php echo ((isset($SelecionaFornecedores) && isset($SelecionaFornecedores["seleciona_exame_{$codigo_cliente}_{$k_exame}"]) && $SelecionaFornecedores["seleciona_exame_{$codigo_cliente}_{$k_exame}"] == $k_fornecedor) || isset($array_exame_mais_barato[$k_exame]) && $array_exame_mais_barato[$k_exame]['fornecedor'] == $k_fornecedor) ? ' checked="checked"' : ''; ?> /> <br />
			                			Selecionar
			                			<br />
				                		<?php if((isset($array_exame_mais_barato[$codigo_cliente][$k_exame]) && $array_exame_mais_barato[$codigo_cliente][$k_exame]['fornecedor'] == $k_fornecedor) && empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) : ?>
				                			<label class="label label-success">Melhor Custo</label>
				                		<?php endif; ?>
			                		<?php else : ?>
			                			---
			                		<?php endif; ?>
			                	</td>
							<?php endforeach; ?>
							<td class="input-small" style="text-align: right;"><?php echo isset($fornecedores['Km']) ? $fornecedores['Km'] : '-'; ?></td>
					
							<td class="input-small center" style="width: 280px; text-align: center; font-size: 11px;">
								<input id="id_<?php echo $k_fornecedor; ?>" type="checkbox" class="forn_select_all_<?php echo $k_fornecedor; ?>">
								<p>Selecionar todos</p>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>				        
	        <?php endif; ?>
		</table>
	<?php else : ?>
		<div class="alert alert-error">
			Nenhum Fornecedor Encontrado!
		</div>
	<?php endif; ?>
<?php else : ?>
	<div class="alert alert-error">
		Foi encontrado um problema nos dados de endereço!
	</div>
<?php endif; ?>

<script>

	$(function(){

		$("input[class^='forn_select_all_']").on("change", function(){

			if ($(this).is(":checked")) {
		
				var id = $(this).attr("id");
			
				$("input[class^='forn_select_all_']").not("#" + id).removeProp('checked');

				var tr = $(this).closest("tr");
			
				tr.find("td input:radio").each(function(){

					$(this).prop('checked','checked');
				});

			} else {
				
				var id = $(this).attr("id");
			
				$("input[class^='forn_select_all_']").removeProp('checked');

				var tr = $(this).closest("tr");
		
				tr.find("td input:radio").each(function(){

					$(this).removeProp('checked');
				});
			}
		});	

	})
</script>