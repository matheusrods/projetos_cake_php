<div class='well'>
	<strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
	<strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
</div>
<ul class="nav nav-tabs" id="myTab">
	<li class="active"><a href="#concovacao_exames">Convocação de Exames</a></li>
	<li><a href="#resumo_funcionarios">Resumo de Funcionários</a></li>
	<li><a href="#resumo_exames">Resumo dos Exames</a></li>
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="concovacao_exames">
		<table class="table">
			<thead>
				<tr>
					<?php if(in_array('nome_fantasia', $campos)) { ?>
					<th>
						Apelido da Empresa
					</th>
					<?php } ?>
					<?php if(in_array('razao_social', $campos)) { ?>
					<th>
						Empresa
					</th>
					<?php } ?>
					<?php if(in_array('unidade', $campos)) { ?>
					<th>
						Unidade
					</th>
					<?php } ?>
					<?php if(in_array('setor', $campos)) { ?>
					<th>
						Setor
					</th>
					<?php } ?>
					<?php if(in_array('cargo', $campos)) { ?>
					<th>	
						Cargo
					</th>
					<?php } ?>
					<?php if(in_array('nome_funcionario', $campos)) { ?>
					<th>
						Nome
					</th>
					<?php } ?>
					<?php if(in_array('exame', $campos)) { ?>
					<th>
						Exame
					</th>
					<?php } ?>
					<?php if(in_array('ultimo_pedido', $campos)) { ?>
					<th>
						Último pedido
					</th>
					<?php } ?>
					<?php if(in_array('data_resultado', $campos)) { ?>
					<th>
						Data resultado
					</th>
					<?php } ?>
					<?php if(in_array('periodicidade', $campos)) { ?>
					<th>
						Periodicidade
					</th>
					<?php } ?>
					<?php if(in_array('refazer_em', $campos)) { ?>
					<th>
						Refazer em
					</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($dados_convocacao_exames as $key => $dados_convocacao_exame) { ?>
				<tr>
					<?php if(in_array('nome_fantasia', $campos)) { ?>
					<td>
						<?php echo $dados_convocacao_exame['Empresa']['nome_fantasia'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('razao_social', $campos)) { ?>
					<td>
						<?php echo $dados_convocacao_exame['Empresa']['razao_social'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('unidade', $campos)) { ?>
					<td>
						<?php echo $dados_convocacao_exame['Unidade']['razao_social'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('setor', $campos)) { ?>
					<td>
						<?php echo $dados_convocacao_exame['GrupoEconomicoCliente']['setor'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('cargo', $campos)) { ?>
					<td>
						<?php echo $dados_convocacao_exame['GrupoEconomicoCliente']['cargo'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('nome_funcionario', $campos)) { ?>
					<td>
						<?php echo $dados_convocacao_exame['Funcionario']['nome'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('exame', $campos)) { ?>
					<td>
						<?php echo $dados_convocacao_exame['Exame']['descricao'] ?>
					</td>
					<?php } ?>
					<?php if(in_array('ultimo_pedido', $campos)) { ?>
					<td></td>
					<?php } ?>
					<?php if(in_array('data_resultado', $campos)) { ?>
					<td></td>
					<?php } ?>
					<?php if(in_array('periodicidade', $campos)) { ?>
					<td></td>
					<?php } ?>
					<?php if(in_array('refazer_em', $campos)) { ?>
					<td></td>
					<?php } ?>
				</tr>
				<?php } ?>

			</tbody>
		</table>

	</div>
	<div class="tab-pane" id="resumo_funcionarios">
		<table class="table">
			<thead>
				<tr>
					<th>
						Empresa
					</th>
					<th>
						Unidade
					</th>
					<th>
						Setor
					</th>
					<th>
						Código funcionário
					</th>
					<th>	
						Funcionário
					</th>
					<th>
						Situação
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($resumo_funcionarios as $key => $resumo_funcionario) { ?>
				<tr>
					<td>
						<?php echo $resumo_funcionario['Empresa']['razao_social'] ?>
					</td>
					<td>
						<?php echo $resumo_funcionario['Unidade']['razao_social'] ?>
					</td>
					<td>
						<?php echo $resumo_funcionario['GrupoEconomicoCliente']['setor'] ?>
					</td>
					<td>
						<?php echo $resumo_funcionario['Funcionario']['codigo'] ?>
					</td>
					<td>
						<?php echo $resumo_funcionario['Funcionario']['nome'] ?>
					</td>
					<td>
						<?php if(!is_null($resumo_funcionario['Funcionario']['status'])) echo (($resumo_funcionario['Funcionario']['status'] > 0)? 'Ativo' : 'Inativo') ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="tab-pane" id="resumo_exames">
			<table class="table">
				<thead>
					<tr>
						<th>
							Exames
						</th>
						<th>
							Quantidade
						</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($resumo_exames as $key => $resumo_exame) { ?>
					<tr>
						<td>
							<?php echo $resumo_exame['Exame']['descricao'] ?>
						</td>
						<td>
							<?php echo $resumo_exame['Exame']['quantidade'] ?>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="margin-top-15 margin-bottom-20">
		<?php echo $this->Html->link('Voltar', array('action' => 'vencimento_exames', $this->data['Cliente']['codigo']), array('class' => 'btn btn-default'));; ?>
	</div>
	<?php echo $this->Javascript->codeBlock('
		$("#myTab a").click(function (e) {
			e.preventDefault();
			$(this).tab("show");
		})
		'); ?>
