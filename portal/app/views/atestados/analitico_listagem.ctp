<?php if(is_array($dados) && count($dados) >= 1) : ?>
	<div class='well'>
	    <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
	</div>
	<div class="double-scroll">
		<table class="table table-striped">
		    <thead>
		        <tr>
					<th>Empresa</th>
					<th>Unidade</th>
					<th>CNPJ</th>
					<th>Funcionário</th>
					<th>Setor</th>
					<th>Cargo</th>
					<th>Matrícula</th>
					<th>CPF</th>
					<th>RG</th>
					<th>TIPO</th>
					<th>Data inclusão atestado</th>
					<th>Data início atestado</th>
					<th>Data final atestado</th>
					<th>Horário Inicial Atestado</th>
					<th>Horário Final Atestado</th>
					<th>Dia da Semana</th>
					<th class='numeric'>Quantidade de dias afastados</th>
					<th class='numeric'>Quantidade de horas afastadas</th>
					<th>Motivo da Licença</th>
					<th>Motivo da Licença (Tabela 18 - e-Social)</th>
					<th>Tipo de acidente de trânsito</th>
					<th>Afastamento decorre de mesmo motivo de afastamento anterior (60 dias)?</th>
					<th>Observação</th>
					<th>Ônus da cessão/requisição</th>
					<th>Ônus da Remuneração</th>
					<th>Renumeração do Cargo</th>
					<th>CNPJ</th>
					<th>Origem da retificação</th>
					<th>Tipo de processo</th>
					<th>Número do processo</th>
					<th>Restrição para o retorno</th>
					<th>Nome do médico</th>
					<th>CRM</th>
					<th>UF</th>
					<th>CID10</th>
					<th>Nome CID10</th>
					<th>CNAE</th>
					<th>Descrição CNAE</th>
					<th>Nexo</th>
					<th>Endereço do Funcionário</th>
					<th>Número</th>
					<th>Complemento</th>
					<th>Endereço da Unidade</th>
					<th>Número</th>
					<th>Complemento</th>
					<th>Local de Atendimento</th>
					<th>CEP</th>
					<th>Endereço</th>
					<th class='numeric'>Distância do endereço do funcionário(Km)</th>
					<th class='numeric'>Distância do endereço da unidade(Km)</th>
				</tr>
			</thead>
			<tbody>
				<?php $total = 0 ?>
				<?php foreach($dados as $key => $value) : ?>
					<?php $total += 1 ?>
					<tr>
						<td><?= $value[0]['cliente_razao_social'] ?></td>
						<td><?= $value[0]['unidade_nome_fantasia'] ?></td>
						<td><?= Comum::formatarDocumento($value[0]['unidade_codigo_documento']) ?></td>
						<td><?= $value[0]['funcionario_nome'] ?></td>
						<td><?= $value[0]['setor_descricao'] ?></td>
						<td><?= $value[0]['cargo_descricao'] ?></td>
						<td><?= $value[0]['cliente_funcionario_matricula'] ?></td>
						<td><?= $value[0]['funcionario_cpf'] ?></td>
						<td><?= $value[0]['funcionario_rg'] ?></td>
						<td><?= $value[0]['tipo_atestado'] ?></td>
						<td><?= AppModel::dbDateToDate($value[0]['atestado_data_inclusao']) ?></td>
						<td><?= AppModel::dbDateToDate($value[0]['atestado_afastamento_periodo']) ?></td>
						<td><?= AppModel::dbDateToDate($value[0]['atestado_data_retorno_periodo']) ?></td>
						<td><?= $value[0]['atestado_hora_afastamento'] ?></td>
						<td><?= $value[0]['atestado_hora_retorno'] ?></td>
						<td><?= Comum::diaDaSemana($value[0]['dia_semana']) ?></td>
						<td class='numeric'><?= $value[0]['atestado_afastamento_em_dias'] ?></td>
						<td class='numeric'><?= $value[0]['atestado_afastamento_em_horas'] ?></td>
						<td><?= $value[0]['motivo_afastamento_descricao'] ?></td>
						<td><?= $value[0]['esocial_descricao'] ?></td>
						<td><?= $value[0]['tipo_acidente_transito'] ?></td>
						<td><?= $value[0]['motivo_afastamento'] ?></td>
						<td><?= $value[0]['observacao'] ?></td>
						<td><?= $value[0]['onus_requisicao'] ?></td>
						<td><?= $value[0]['onus_remuneracao'] ?></td>
						<td><?= $value[0]['renumeracao_cargo'] ?></td>
						<td><?= Comum::formatarDocumento($value[0]['cnpj']) ?></td>
						<td><?= $value[0]['origem_retificacao'] ?></td>
						<td><?= $value[0]['tipo_processo'] ?></td>
						<td><?= $value[0]['numero_processo'] ?></td>
						<td><?= $value[0]['atestado_restricao'] ?></td>
						<td><?= $value[0]['medico_nome'] ?></td>
						<td><?= $value[0]['medico_numero_conselho'] ?></td>
						<td><?= $value[0]['medico_conselho_uf'] ?></td>
						<td><?= $value[0]['cid_codigo_cid10'] ?></td>
						<td><?= $value[0]['cid_descricao'] ?></td>
						<td><?= $value[0]['unidade_cnae'] ?></td>
						<td><?= $value[0]['cnae_unidade_descricao'] ?></td>
						<td><?= $value[0]['nexo'] ?></td>
						<td><?= $value[0]['funcionario_endereco'] ?></td>
						<td><?= $value[0]['funcionario_endereco_numero'] ?></td>
						<td><?= $value[0]['funcionario_end_complemento'] ?></td>
						<td><?= $value[0]['unidade_endereco'] ?></td>
						<td><?= $value[0]['unidade_endereco_numero'] ?></td>
						<td><?= $value[0]['unidade_endereco_complemento'] ?></td>
						<td><?= $value[0]['tipo_local_atend_descricao'] ?></td>
						<td><?= $value[0]['atestado_cep'] ?></td>
						<td><?= $value[0]['atestado_endereco'] ?></td>
						<td class='numeric'><?= $value[0]['distancia_funcionario'] ?></td>
						<td class='numeric'><?= $value[0]['distancia_unidade'] ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<td><?= $total ?></td>
					<td colspan="38"></td>
				</tr>
			</tfoot>
		</table>
	</div>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>

<?php echo $this->Buonny->link_js('jquery.doubleScroll'); ?>

<script>
	$(document).ready(function(){
		$('.double-scroll').doubleScroll();
	});
</script>