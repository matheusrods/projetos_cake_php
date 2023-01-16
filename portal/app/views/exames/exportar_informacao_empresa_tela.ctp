<ul class="nav nav-tabs" id="myTab">
	<li class="active"><a href="#informacao_empresa">Informações da empresa</a></li>

</ul>

<div class="tab-content" style="width: 4050px;">
	<div class="tab-pane active" id="informacao_empresa">
		<table class="table" style="width: inherit;">
			<thead>
				<tr>
					<?php if(in_array('codigo_matriz', $campos)) { ?>
					<th>
						Código Matriz
					</th>
					<?php } ?>
					<?php if(in_array('codigo_externo_matriz', $campos)) { ?>
					<th>
						Código Externo Matriz
					</th>
					<?php } ?>
					<?php if(in_array('razao_social_matriz', $campos)) { ?>
					<th>
						Razão Social Matriz
					</th>
					<?php } ?>
					<?php if(in_array('nome_fantasia_matriz', $campos)) { ?>
					<th>
						Nome Fantasia Matriz
					</th>
					<?php } ?>
					<?php if(in_array('CNPJ_matriz', $campos)) { ?>
					<th>	
						CNPJ Matriz
					</th>
					<?php } ?>
					<?php if(in_array('codigo_unidade', $campos)) { ?>
					<th>
						Código Unidade
					</th>
					<?php } ?>
					<?php if(in_array('codigo_externo_unidade', $campos)) { ?>
					<th>
						Código Externo Unidade
					</th>
					<?php } ?>
					<?php if(in_array('razao_social_unidade', $campos)) { ?>
					<th>
						Razão Social Unidade
					</th>
					<?php } ?>
					<?php if(in_array('nome_fantasia_unidade', $campos)) { ?>
					<th>
						Nome Fantasia Unidade
					</th>
					<?php } ?>
					<?php if(in_array('CNPJ_unidade', $campos)) { ?>
					<th>
						CNPJ Unidade
					</th>
					<?php } ?>
					<?php if(in_array('tipo_unidade', $campos)) { ?>
					<th>
						Tipo de Unidade
					</th>
					<?php } ?>
					<?php if(in_array('inscricao_estadual', $campos)) { ?>
					<th>
						Inscrição Estadual
					</th>
					<?php } ?>
					<?php if(in_array('inscricao_municipal', $campos)) { ?>
					<th>
						Inscrição Municipal
					</th>
					<?php } ?>
					<?php if(in_array('regime_tributario', $campos)) { ?>
					<th>
						Regime Tributário
					</th>
					<?php } ?>
					<?php if(in_array('ativo', $campos)) { ?>
					<th>
						Ativo
					</th>
					<?php } ?>
					<?php if(in_array('cnae', $campos)) { ?>
					<th>
						CNAE
					</th>
					<?php } ?>
					<?php if(in_array('ramo_atividade', $campos)) { ?>
					<th>
						Ramo de Atividade
					</th>
					<?php } ?>
					<?php if(in_array('data_inclusao', $campos)) { ?>
					<th>
						Data da Inclusão
					</th>
					<?php } ?>
					<?php if(in_array('endereco', $campos)) { ?>
					<th>
						Endereço logradouro
					</th>
					<?php } ?>
					<?php if(in_array('numero', $campos)) { ?>
					<th>
						Número
					</th>
					<?php } ?>
					<?php if(in_array('complemento', $campos)) { ?>
					<th>
						Complemento
					</th>
					<?php } ?>
					<?php if(in_array('bairro', $campos)) { ?>
					<th>
						Bairro
					</th>
					<?php } ?>
					<?php if(in_array('cidade', $campos)) { ?>
					<th>
						Cidade
					</th>
					<?php } ?>
					<?php if(in_array('estado', $campos)) { ?>
					<th>
						Estado
					</th>
					<?php } ?>
					<?php if(in_array('gestor_comercial', $campos)) { ?>
					<th>
						Gestor Comercial
					</th>
					<?php } ?>
					<?php if(in_array('gestor_contrato', $campos)) { ?>
					<th>
						Gestor Contrato
					</th>
					<?php } ?>
					<?php if(in_array('gestor_operacao', $campos)) { ?>
					<th>
						Gestor Operação
					</th>
					<?php } ?>
					<?php if(in_array('plano_saude', $campos)) { ?>
					<th>
						Plano de Saúde
					</th>
					<?php } ?>
					<?php if(in_array('corretora', $campos)) { ?>
					<th>
						Corretora
					</th>
					<?php } ?>
					<?php if(in_array('coord_pcmso', $campos)) { ?>
					<th>
						Coord PCMSO
					</th>
					<?php } ?>
					<?php if(in_array('crm', $campos)) { ?>
					<th>
						CRM
					</th>
					<?php } ?>
					<?php if(in_array('uf', $campos)) { ?>
					<th>
						UF
					</th>
					<?php } ?>
					<?php if(in_array('nome_contato', $campos)) { ?>
					<th>
						Nome Contato
					</th>
					<?php } ?>
					<?php if(in_array('telefone_contato', $campos)) { ?>
					<th>
						Telefone Contato
					</th>
					<?php } ?>
					<?php if(in_array('email_contato', $campos)) { ?>
					<th>
						E-mail Contato
					</th>
					<?php } ?>
					<?php if(in_array('tipo_contato', $campos)) { ?>
					<th>
						Tipo Contato
					</th>
					<?php } ?>
					<?php if(in_array('historico', $campos)) { ?>
					<th>
						Histórico
					</th>
					<?php } ?>
					<?php if(in_array('quant_func_ativos', $campos)) { ?>
					<th>
						Quantidade de Funcionários Ativos
					</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($dados as $key => $dado) { ?>
				<tr>
					<?php if(in_array('codigo_matriz', $campos)) { ?>
					<td>
						<?php echo $dado[0]['codigo_matriz'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('codigo_externo_matriz', $campos)) { ?>
					<td>
						<?php echo $dado[0]['codigo_externo_matriz'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('razao_social_matriz', $campos)) { ?>
					<td>
						<?php echo utf8_encode($dado[0]['razao_social_matriz']) ?>
					</td>
					<?php } ?>
					<?php if(in_array('nome_fantasia_matriz', $campos)) { ?>
					<td>
						<?php echo utf8_encode($dado[0]['nome_fantasia_matriz']) ?>
					</td>
					<?php } ?>
					<?php if(in_array('CNPJ_matriz', $campos)) { ?>
					<td>
						<?php echo Comum::formatarDocumento(utf8_encode($dado[0]['CNPJ_matriz'])) ?>
					</td>
					<?php } ?>
					<?php if(in_array('codigo_unidade', $campos)) { ?>
					<td>
						<?php echo utf8_encode($dado[0]['codigo_unidade']) ?>
					</td>
					<?php } ?>
					<?php if(in_array('codigo_externo_unidade', $campos)) { ?>
					<td>
						<?php echo utf8_decode(utf8_encode($dado[0]['codigo_externo_unidade'])); // CDCT-666 ?>
					</td>
					<?php } ?>
					<?php if(in_array('razao_social_unidade', $campos)) { ?>
					<td>
						<?php echo utf8_encode($dado[0]['razao_social_unidade']) ?>
					</td>
					<?php } ?>
					<?php if(in_array('nome_fantasia_unidade', $campos)) { ?>
					<td>
						<?php echo $dado[0]['nome_fantasia_unidade'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('CNPJ_unidade', $campos)) { ?>
					<td>
						<?php 
						if($dado[0]['codigo_documento_real'] && in_array('tipo_unidade', $campos) && strtoupper($dado[0]['tipo_unidade']) == 'OPERACIONAL') {
							echo Comum::formatarDocumento($dado[0]['codigo_documento_real']);
						} else {
							echo Comum::formatarDocumento($dado[0]['CNPJ_unidade']);
						}?>
					</td>
					<?php } ?>
					<?php if(in_array('tipo_unidade', $campos)) { ?>
					<td>
						<?php echo $dado[0]['tipo_unidade'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('inscricao_estadual', $campos)) { ?>
					<td>
						<?php echo $dado[0]['inscricao_estadual'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('inscricao_municipal', $campos)) { ?>
					<td>
						<?php echo $dado[0]['inscricao_municipal'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('regime_tributario', $campos)) { ?>
					<td>
						<?php echo $dado[0]['regime_tributario'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('ativo', $campos)) { ?>
					<td>
						<?php echo $dado[0]['ativo'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('cnae', $campos)) { ?>
					<td>
						<?php echo $dado[0]['cnae'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('ramo_atividade', $campos)) { ?>
					<td> 
						<?php echo utf8_decode(utf8_encode( $dado[0]['ramo_atividade'] )); // CDCT-666 ?>
						
					</td>
					<?php } ?>
					<?php if(in_array('data_inclusao', $campos)) { ?>
					<td>
						<?php echo $dado[0]['data_inclusao'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('endereco', $campos)) { ?>
					<td>
						<?php echo utf8_decode(utf8_encode($dado[0]['endereco'])); // CDCT-666 ?>
					</td>
					<?php } ?>
					<?php if(in_array('numero', $campos)) { ?>
					<td>
						<?php echo $dado[0]['numero'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('complemento', $campos)) { ?>
					<td>
						<?php echo utf8_encode($dado[0]['complemento']) ?>
					</td>
					<?php } ?>
					<?php if(in_array('bairro', $campos)) { ?>
					<td>
						<?php echo utf8_decode(utf8_encode( $dado[0]['bairro'] )); // CDCT-666 ?>
					</td>
					<?php } ?>
					<?php if(in_array('cidade', $campos)) { ?>
					<td>
						<?php echo utf8_decode(utf8_encode( $dado[0]['cidade'] )); // CDCT-666 ?>
					</td>
					<?php } ?>
					<?php if(in_array('estado', $campos)) { ?>
					<td>
						<?php echo $dado[0]['estado'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('gestor_comercial', $campos)) { ?>
					<td>
						<?php echo $dado[0]['gestor_comercial'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('gestor_contrato', $campos)) { ?>
					<td>
						<?php echo $dado[0]['gestor_contrato'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('gestor_operacao', $campos)) { ?>
					<td>
						<?php echo $dado[0]['gestor_operacao'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('plano_saude', $campos)) { ?>
					<td>
						<?php echo $dado[0]['plano_saude'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('corretora', $campos)) { ?>
					<td>
						<?php echo $dado[0]['corretora'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('coord_pcmso', $campos)) { ?>
					<td>
						<?php echo $dado[0]['coord_pcmso'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('crm', $campos)) { ?>
					<td>
						<?php echo $dado[0]['crm'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('uf', $campos)) { ?>
					<td>
						<?php echo $dado[0]['uf'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('nome_contato', $campos)) { ?>
					<td>
						<?php echo $dado[0]['nome_contato'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('telefone_contato', $campos)) { ?>
					<td>
						<?php echo $dado[0]['telefone_contato'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('email_contato', $campos)) { ?>
					<td>
						<?php echo $dado[0]['email_contato'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('tipo_contato', $campos)) { ?>
					<td>
						<?php echo utf8_encode($dado[0]['tipo_contato']); ?>
					</td>
					<?php } ?>
					<?php if(in_array('historico', $campos)) { ?>
					<td>
						<?php echo utf8_decode(utf8_encode($dado[0]['historico'])); ?>
					</td>
					<?php } ?>
					<?php if(in_array('quant_func_ativos', $campos)) { ?>
					<td>
						<?php echo $dado[0]['quant_func_ativos'] ?>
					</td>
					<?php } ?>
				</tr>
				<?php } ?>

			</tbody>
		</table>
	</div>
</div>

<div class="margin-top-15 margin-bottom-20">
	<?php echo $this->Html->link('Voltar', array('action' => 'informacao_empresa'), array('class' => 'btn btn-default'));; ?>
</div>
<?php echo $this->Javascript->codeBlock('
$("#myTab a").click(function (e) {
e.preventDefault();
$(this).tab("show");
})
'); ?>
