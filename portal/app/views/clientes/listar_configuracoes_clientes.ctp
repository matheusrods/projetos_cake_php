	<?php if(empty($vppj_configuracoes_cliente)):?>		
		<div class='actionbar-right'>
		    <?php echo $this->Html->link('Incluir', array('action' => 'dados_padrao_sm', $this->passedArgs['0'], rand()), array( 'title' => 'Adicionar Dados Padrão na SM', 'class' => 'btn btn-success',)) ?>
		</div>
		<br/>
		<div class="alert alert-warning">Não possui configuração para o cliente</div>		
	<?php else:?>
		<table id='configuracoes-clientes' class='table table-striped table-bordered' style='width:1800px;max-width:none;overflow:auto'>
			<thead>
				<th>&nbsp;</th>
				<th style="text-align:center" colspan="2">Temperatura</th>
				<th style="text-align:center" colspan="2">Checklist</th>
				<th style="text-align:center" colspan="2">Monitoramento</th>
				<th style="text-align:center" colspan="2">Bloqueio</th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</thead>
			<thead>
				<th class='input-small'>Data Validade Apolice</th>
				<th class='input-micro'>De</th>
				<th class='input-micro'>Até</th>
				<th class='input-small'>Inicio saida automático</th>
				<th class='input-small'>Atraso Checklist Online</th>
				<th class='input-small'>Monitorar Retorno</th>
				<th class='input-medium'>Monitorar Isca como terminal principal</th>
				<th class='input-medium'>Boquear Inclusão de SM sem Rota</th>
				<th class='input-medium'>Boquear Veiculo sem sinal (Acima de 3 horas sem envio)</th>
				<th class='input-medium'>Não permite SM concorrente</th>
				<th class='input-micro'>&nbsp;</th>
			</thead>
			<tbody>
				<?php foreach ($vppj_configuracoes_cliente as $key => $configuracao): ?>
					<tr>
						<td><?= substr($configuracao['vppj_validade_apolice'], 0,10);?></td>
						<td class="numeric"><?= $configuracao['vppj_temperatura_de'];?></td>
						<td class="numeric"><?= $configuracao['vppj_temperatura_ate'];?></td>
						<td><?= ($configuracao['vppj_inicio_checklist']) ? 'Sim' : 'Não';?></td>
						<td class='numeric'><?= $configuracao['vppj_minutos_atraso_checklist'];?></td>
						<td><?= ($configuracao['vppj_monitorar_retorno'])? 'Sim' : 'Não';?></td>
						<td><?= ($configuracao['vppj_monitorar_isca']) ? 'Sim' : 'Não';?></td>
						<td><?= ($configuracao['vppj_bloquear_sem_rota']) ? 'Sim' : 'Não';?></td>
						<td><?= ($configuracao['vppj_bloq_sem_sinal'] == 'S') ? 'Sim' : 'Não';?></td>
						<td><?= ($configuracao['vppj_nao_permite_sm_concorrente']) ? 'Sim' : 'Não';?></td>
						 <td class="numeric">
		                  	<?= $this->Html->link('', array('action' => 'dados_padrao_sm', $this->passedArgs['0'],rand()), array('escape' => false, 'class' => 'icon-edit', 'title' => 'Editar')); ?>    
                     		<?php echo $html->link('', "javascript:void(0)", array('class' => 'icon-trash', 'title' => 'Excluir', 'onclick' => "excluir_conf('{$configuracao['vppj_codigo']}','{$this->passedArgs['0']}')")) ?>
                     	</td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	<?php endif;?>	
<?php echo $this->Javascript->codeBlock("
	function excluir_conf(codigo_conf) {
		if (confirm('Confirma exclusao?')) {
			$.ajax({
				url: baseUrl + 'clientes/excluir_configuracao_cliente/'+ codigo_conf + '/' + Math.random(),
				success: function(data){
					atualizaListaConfiguracoesCliente('{$this->passedArgs['0']}');
				}
			});
		}
		return false;
	}
")?>