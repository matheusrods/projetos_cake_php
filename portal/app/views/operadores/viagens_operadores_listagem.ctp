<?php
	function constroiHead($texto){
		if(!$texto)
			$texto = '...';
		$return = "<tr class='destak'>";
		$return .= 	"<td colspan ='12'  ><h4>{$texto}</h4></td>";
		$return .= "</tr>";
		return $return;
	}

	function constroiFoot($logado = 0,$intervalo = 0,$deslogado = 0,$total = 0){
		$return = "<tr class='destak well'>";
		$return .= 	"<td colspan ='12'  >";
		$return .= 		"{$logado}<span class=\"badge-empty badge badge-success\" title=\"Operador Logado\"></span>&nbsp;&nbsp;&nbsp;&nbsp;";
		$return .= 		"{$intervalo}<span class=\"badge-empty badge badge-warning\" title=\"Operador Em Intervalo\"></span>&nbsp;&nbsp;&nbsp;&nbsp;";
		$return .=		"{$deslogado}<span class=\"badge-empty badge badge-important\" title=\"Operador Deslogado\"></span>&nbsp;&nbsp;&nbsp;&nbsp;";
		$return .=		"TOTAL: {$total}";
		$return .= 	"</td>";
		$return .= "</tr>";
		return $return;
	}

	$lastAatu    = NULL;
	$all_logado  = 0;$all_intervalo = 0;$all_deslogado = 0;$all_total = 0;
	$eras_codigo = isset($filtros['eras_codigo'])  ? $filtros['eras_codigo'] : NULL;
	$data_inicio = isset($filtros['data_inicio'])  ? $filtros['data_inicio'] : NULL;
	$data_fim 	 = isset($filtros['data_fim'])  ? $filtros['data_fim'] : NULL;
?>
<?php echo $this->BForm->create('TVusuViagemUsuario1', array('type' => 'post', 'url' => array('controller' => 'Operadores', 'action' => 'viagens_operadores')));?>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th style="width:8px"></th>
			<th class="input-medium">Operador</th>
			<th class="input-small">Em Viagem</th>
			<th class="input-small">Agendada</th>
			<th class="input-small">Total</th>
			<th style="width:8px"></th>

			<th style="width:8px"></th>
			<th class="input-medium">Operador</th>
			<th class="input-small">Em Viagem</th>
			<th class="input-small">Agendada</th>
			<th class="input-small">Total</th>
			<th style="width:8px"></th>
		</thead>
		<tbody>
			<?php foreach ($listagem as $grupo => $linhas):?>
				<?php echo constroiHead($grupo); ?>
				<?php $grupo_logado = 0;$grupo_intervalo = 0;$grupo_deslogado = 0;$grupo_total = 0; ?>
				<?php foreach ($linhas as $key => $usuario):?>
				<tr>
					<td>
						<?php echo $this->BForm->input("TUsuaUsuario.{$usuario[0]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}.usua_pfis_pess_oras_codigo", array('class' => 'input-small', 'label' => FALSE, 'type' => 'checkbox', )) ?>
					</td>
					<td> 
				        <span class="pull-right icon-eye-open tp" title="Ramal: <?=$usuario[0]['TUsuaUsuario']['usua_ramal'];?>, Estação: <?=$usuario[0]['TErasEstacaoRastreamento']['eras_descricao']?>" data-toggle="tooltip"/>
						<?php echo $usuario[0]['TUsuaUsuario']['usua_login'] ?>
					</td>
					<td>
						<?=$this->Html->link($this->Buonny->moeda( $usuario[0][0]['em_viagem'], array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$usuario[0]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}', 4 )") ?>
					</td>
					<td>
						<?=$this->Html->link($this->Buonny->moeda( $usuario[0][0]['agendada'], array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$usuario[0]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}', 3 )") ?>
					</td>
					<td>
						<?=$this->Html->link($this->Buonny->moeda( ($usuario[0][0]['em_viagem']+$usuario[0][0]['agendada']), array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$usuario[0]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}', 1 )") ?>
					</td>
					<td>
						<?php $grupo_total++;$all_total++ ?>
						<?php if($usuario[0][0]['logado']): ?>
							<?php $grupo_logado++;$all_logado++ ?>
							<span class="badge-empty badge badge-success" title="Operador Logado"></span>
						<?php elseif($usuario[0][0]['intervalo'] && $usuario[0][0]['motivo'] != 5 ): ?>
							<?php $grupo_intervalo++;$all_intervalo++ ?>
							<span class="badge-empty badge badge-warning" title="Operador Em Intervalo"></span>
						<?php else: ?>
							<?php $grupo_deslogado++;$all_deslogado++ ?>
							<span class="badge-empty badge badge-important" title="Operador Deslogado"></span>
						<?php endif; ?>
					</td>

					<?php if(isset($usuario[1])): ?>
						<td>
							<?php echo $this->BForm->input("TUsuaUsuario.{$usuario[1]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}.usua_pfis_pess_oras_codigo", array('class' => 'input-small', 'label' => FALSE, 'type' => 'checkbox', )) ?>
						</td>
						<td>
							<span class="pull-right icon-eye-open tp" title="Ramal: <?=$usuario[1]['TUsuaUsuario']['usua_ramal'];?>, Estação: <?=$usuario[1]['TErasEstacaoRastreamento']['eras_descricao']?>" data-toggle="tooltip"/>
							<?php echo $usuario[1]['TUsuaUsuario']['usua_login'] ?>
						</td>
						<td>
							<?=$this->Html->link($this->Buonny->moeda( $usuario[1][0]['em_viagem'], array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$usuario[1]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}', 4 )") ?>
						</td>
						<td>
							<?=$this->Html->link($this->Buonny->moeda( $usuario[1][0]['agendada'], array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$usuario[1]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}', 3 )") ?>
						</td>
						<td>
							<?=$this->Html->link($this->Buonny->moeda( ($usuario[1][0]['em_viagem']+$usuario[1][0]['agendada']), array('nozero' => true, 'places' => 0)), "javascript:consulta_geral_sm( '{$usuario[1]['TUsuaUsuario']['usua_pfis_pess_oras_codigo']}', 1 )") ?>
						</td>
						<td>
							<?php $all_total++;$grupo_total++ ?>
							<?php if($usuario[1][0]['logado']): ?>
								<?php $grupo_logado++;$all_logado++ ?>
								<span class="badge-empty badge badge-success" title="Operador Logado"></span>
							<?php elseif($usuario[1][0]['intervalo'] && $usuario[1][0]['motivo'] != 5 ): ?>
								<?php $grupo_intervalo++;$all_intervalo++ ?>
								<span class="badge-empty badge badge-warning" title="Operador Em Intervalo"></span>
							<?php else: ?>
								<?php $grupo_deslogado++;$all_deslogado++ ?>
								<span class="badge-empty badge badge-important" title="Operador Deslogado"></span>
							<?php endif; ?>
						</td>
					<?php else: ?>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					<?php endif; ?>
				</tr>
				<?php endforeach;?>
				<?php echo constroiFoot($grupo_logado,$grupo_intervalo,$grupo_deslogado,$grupo_total); ?>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="12">
					<h5>
						<?php echo $all_logado?><span class="badge-empty badge badge-success" title="Operador Logado"></span>&nbsp;&nbsp;&nbsp;&nbsp;
						<?php echo $all_intervalo?><span class="badge-empty badge badge-warning" title="Operador Em Intervalo"></span>&nbsp;&nbsp;&nbsp;&nbsp;
						<?php echo $all_deslogado?><span class="badge-empty badge badge-important" title="Operador Deslogado"></span>&nbsp;&nbsp;&nbsp;&nbsp;
						TOTAL: <?php echo $all_total?>
					</h5>
				</td>
			</tr>
		</tfoot>
	</table>	
</div>

<div>
      <?//php echo $this->BMenu->linkOnClick('Redistribuir', array('controller' => 'Operadores','action' => 'redistribuir_viagens',rand()), array('title' => 'Redistribuir Viagens' ,'class' => 'btn btn-primary', 'id' => 'TVusuRedistribuir')); ?>
</div>

<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		var flag = 1;
		$(".tp").tooltip({
			html: true
		});
		$("#TVusuRedistribuir").click(function(){
			if(flag){
				flag = 0;
				if(confirm("Confirma a redistribuição destas viagens?")){
					var link = $( this );
					var url = link.attr("href");
					$.ajax({
						type: "POST",
						url: url,
						data: $("form#TVusuViagemUsuario1ViagensOperadoresListagemForm").serialize(),
						beforeSend: function(){
							link.html("Aguarde...");
						},
						complete: function(){
							flag = 1;
							link.html("Redistribuir");
							atualizaViagensOperador();
						}
					});
				}
			}
			return false;
		});
	});
');
?>


<?php echo $this->Javascript->codeBlock("
	function consulta_geral_sm( codigo_usuario, status_viagem ) {
		var codigo_estacao = '{$eras_codigo}';
		var status_viagem_click = 0;
		if( status_viagem == 1){
			status_viagem_click = 1;
			status_viagem = 4;
		}
 		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/relatorios_sm/listagem_consulta_geral_sm/' + Math.random());
	  	field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSmConsulta][eras_codigo]');
		field.setAttribute('value', codigo_estacao);
	    field.setAttribute('type', 'hidden');	    
	    form.appendChild(field);

	    field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
		field.setAttribute('value', status_viagem );
	    field.setAttribute('type', 'hidden');	    
	    form.appendChild(field);

		if( status_viagem == 4 ){
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
			field.setAttribute('value', 5 );
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);

		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
			field.setAttribute('value', 6 );
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);
		}

		if( status_viagem_click == 1 ){
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[RelatorioSmConsulta][codigo_status_viagem][]');
			field.setAttribute('value', 3 );
		    field.setAttribute('type', 'hidden');	    
		    form.appendChild(field);
		}

	    field = document.createElement('input');
	    field.setAttribute('name', 'data[RelatorioSmConsulta][tipo_view]');
		field.setAttribute('value', 'popup');
	    field.setAttribute('type', 'hidden');	    
	    form.appendChild(field);
	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();
	}
	");?>