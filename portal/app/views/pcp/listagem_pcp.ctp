<?php if(isset($listagem)): ?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
	<div class="well">
	<strong>Código:</strong> <?php echo $cliente['Cliente']['codigo']; ?>
	<strong>Razão Social:</strong> <?php echo $cliente['Cliente']['razao_social']; ?>
	</div>
	<div class='actionbar-right'>
		<?php
		echo $this->Html->Link(
	        '<span class="icon-download-alt"></span>&nbsp;Importar Planilha PCP', '/pcp/index/' . $cliente["Cliente"]["codigo"] . '',
	        array(
		        'class' => 'button',
		        'escape' => false,
			)
	    );
	    echo '&nbsp;&nbsp;&nbsp;&nbsp;';
	    ?>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir', $cliente["Cliente"]["codigo"]), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Pcp'));?>
	</div>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th>Rota</th>
			<th>Loja</th>
			<th>Tipo Carga</th>
			<th>CD</th>
			<th>Tipo Veículo</th>
			<th>Tipo Veículo Geral</th>
			<th class='numeric'>Parada</th>
			<th class='numeric'>Peso Bruto Total</th>
			<th class='numeric'>Volume Bruto Total</th>
			<th class='numeric'>Peso Utilização</th>
			<th class='numeric'>Volume Utilização</th>
			<th class="numeric input-mini">Peso</th>
			<th class='numeric'>Volume</th>
			<th>Bandeira</th>
			<th>Percurso</th>
			<th class="input-mini">Lead Time</th>
			<th>Hora Inicial</th>
			<th>Hora Final</th>
			<th>Limite Expedição Inicial</th>
			<th>Limite Expedição Intermediário</th>
			<th>Limite Expedição Final</th>
			<th>Janela Inicial</th>
			<th>Janela Intermediaria</th>
			<th>Janela Final</th>
			<th>Data</th>
			<th>Valor Total</th>
			<th class="input-mini">Estado</th>
			<th>Data Cadastro</th>
			<th>Data Alteração</th>
			<th>Usuario de Inclusão</th>
			<th>Usuario de Alteração</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
			<th>&nbsp;</th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= $dado[0]['ipcp_rota'] ?></td>
					<td><?= $dado[0]['ipcp_loja'] ?></td>
					<td><?= $dado[0]['ipcp_tipo_carga'] ?></td>
					<td><?= $dado[0]['ipcp_cd'] ?></td>
					<td><?= $dado[0]['ipcp_tipo_veiculo'] ?></td>
					<td><?= $dado[0]['ipcp_tipo_veiculo_geral'] ?></td>
					<td class='numeric'><?= $dado[0]['ipcp_paradas'] ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ipcp_peso_bruto_total']) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ipcp_volume_bruto_total']) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ipcp_peso_utilizacao']) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ipcp_volume_utilizacao']) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ipcp_peso']) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ipcp_volume']) ?></td>
					<td><?= $dado[0]['ipcp_bandeira'] ?></td>
					<td><?= $dado[0]['ipcp_percurso'] ?></td>
					<td><?= $dado[0]['ipcp_lead_time'] ?></td>
					<td><?= $dado[0]['ipcp_hora_inicial'] ?></td>
					<td><?= $dado[0]['ipcp_hora_final'] ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_limite_expedicao_inicial']) ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_limite_expedicao_intermediario']) ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_limite_expedicao_final']) ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_janela_inicial']) ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_janela_intermediaria']) ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_janela_final']) ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_data_remessa']) ?></td>
					<td><?= $dado[0]['ipcp_valor_total'] ?></td>
					<td><?= $dado[0]['ipcp_estado_destino'] ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_data_cadastro']) ?></td>
					<td><?= AppModel::dbDateToDate($dado[0]['ipcp_data_alteracao']) ?></td>
					<td><?= $dado[0]['ipcp_usuario_adicionou'] ?></td>
					<td><?= $dado[0]['ipcp_usuario_alterou'] ?></td>
					<td>
						<?php echo $this->Html->link('', array('action' => 'mudar_status', $dado[0]['ipcp_codigo'], $cliente["Cliente"]["codigo"]), array('class' => 'icon-random troca-status', 'title' => 'Mudar Status')) ?>
					</td>
					<td>
						<?php if($dado[0]['ipcp_ativo']): ?>
							<span class="badge-empty badge badge-success" title="Ativo"></span>
						<?php else: ?>
							<span class="badge-empty badge badge-important" title="Inativo"></span>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $html->link('', array('action' => 'atualizar',$cliente["Cliente"]["codigo"], $dado[0]['ipcp_codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
					</td>
					<td>
						<?php echo $this->Html->link('', array('action' => 'excluir', $cliente["Cliente"]["codigo"],$dado[0]['ipcp_codigo']), array('class' => 'icon-trash excluir', 'title' => 'Excluir'), 'Confirma a exclusão?') ?>
					</td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<div class='row-fluid'>
	    <div class='numbers span6'>
	    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?php echo $this->Paginator->numbers(); ?>
	    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	    </div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
	<?php echo $this->Javascript->codeBlock('
		$(function(){
			$("a.icon-random").click(function(){
				if(confirm("Deseja mudar o status?")){
					$.ajax({
						url:$(this).attr("href"),
						dataType: "json",
						success: function(data){
							var div = jQuery("div.lista");
							bloquearDiv(div);
							div.load(baseUrl + "pcp/listagem_pcp/" + Math.random());
						}
					});
				}

				return false;
			});

		});
	'); ?>
<?php endif; ?>