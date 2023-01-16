<?php $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour')); ?>
<?php if(empty($filtros['RelatorioSm'])): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php elseif(empty($relatorio)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<div class="well">
		<?php if(!empty($cliente)): ?>
			<strong>Código: </strong><?= $cliente['cliente']['Cliente']['codigo'] ?>
    		<strong>Cliente: </strong><?= $cliente['cliente']['Cliente']['razao_social'] ?>
		<?php endif; ?>
		<strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
		<span class="pull-right">
			<?php echo $html->link('Atualizar', 'javascript:atualizaListaRelatorioSmAcompanhamentoViagensAnalitico();') ?>
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export', $status_alvo, $alvo, $agrupamento, $consulta_temperatura), array('escape' => false, 'title' =>'Exportar para Excel'));?>
			<?php if( $authUsuario['Usuario']['codigo_uperfil'] == 3 ):?>
			<?php echo $this->Buonny->exportacao_relatorio_email($this, 'RelatorioSm', 'acompanhamentoViagensAnalitico' ) ?>
		<?php endif;?>
		</span>
	</div>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	<div class='row-fluid'>
        <table class='table table-striped' style='width:4500px;max-width:none;'>
		    <thead>
		        <tr>
		            <th class='input-small'>SM</th>
		            <th class='input-medium'>Pedido do Cliente</th>
		            <th class='input-small'>Tecnologia</th>
		            <th class='input-xlarge'>Transportadora</th>
		            <th class='input-small'>Placa/Chassi</th>
		            <th class='input-medium'>Início Previsto</th>
		            <th class='input-medium'>Início Real</th>
		            <th class='input-xlarge'>Último Alvo</th>
		            <th class='input-medium'>Previsão Último Alvo</th>
		            <th class='input-medium'>Entrada Último Alvo</th>
		            <th class='input-medium'>Saída Último Alvo</th>
		            <th class='input-medium'>Status Último Alvo</th>
		            <th class='input-xlarge'>Posição Atual</th>
		            <th class='input-medium'>Data Última Posição</th>
		            <th class='input-xlarge'>Próximo Alvo</th>
		            <th class='input-medium'>Previsão Próximo Alvo</th>
		            <th class='input-medium'>Status Próximo Alvo</th>
		            <th class='input-small'>Tempo Restante</th>
		            <th class='input-small'>Km Restante</th>
		            <th class='input-medium'>Status</th>
		            <th class='input-small'>Pos.</th>
		            <th class='input-small'>Fim real</th>
		            <th class='input-xlarge'>Região 1º Entr.</th>
		            <th class='input-small numeric'>Mínima</th>
		            <th class='input-small numeric'>Máxima</th>
		            <th class='input-small numeric'>Atual</th>
		            <th class="input-small numeric" >% Dentro Temp.</th>
		            <th class="input-small numeric" >% Fora Temp.</th>
		            <th class='input-large'>Solicitante</th>
		            <th class='input-xlarge'>Alvo Origem</th>
		            <th class='input-xlarge'>Alvo Destino</th>
		        </tr>
		    </thead>
		    <tbody>
		        <?php foreach($relatorio as $registro): ?>
		        <?php $registro = $registro[0]; ?>
		        <?php $inicioReal = AppModel::dbDateToDate($registro['InicioPrevisto']); ?>
		        <?php $fimReal = empty($registro['FimReal']) ? date('d/m/Y H:i:s') : AppModel::dbDateToDate($registro['FimReal']); ?>
		        <tr>
		            <td><?php echo $this->Buonny->codigo_sm($registro['SM']); ?></td>
		            <td><?php echo $registro['PedidoCliente'] ?></td>
		            <td><?php echo $registro['Tecnologia'] ?></td>
		            <td><?php echo $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Transportadora']), 30); ?></td>
		            <td><?php echo isset($registro['Placa'][0]) && ctype_alpha($registro['Placa'][0]) ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['Placa']), $inicioReal, $fimReal) : $registro['Chassi'] ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['InicioPrevisto']); ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['InicioReal']); ?></td>
		            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $registro['UltimoAlvo']), $registro['UltimoAlvoLatitude'], $registro['UltimoAlvoLongitude']) ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['PrevisaoUltimoAlvo']); ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['EntradaUltimoAlvo']); ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['SaidaUltimoAlvo']); ?></td>
		            <td><?php echo $registro['StatusUltimoAlvo'] ?></td>
		            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $registro['UltimaPosicaoDescricao']), $registro['UltimaPosicaoLatitude'], $registro['UltimaPosicaoLongitude']) ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['DataUltimaPosicao']); ?></td>
		            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $registro['ProximoAlvo']), $registro['ProximoAlvoLatitude'], $registro['ProximoAlvoLongitude']) ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['PrevisaoProximoAlvo']); ?></td>
		            <td><?php echo $registro['StatusProximoAlvo'] ?></td>
		            <td><?php echo !empty($registro['TempoRestante']) ? $registro['TempoRestante'] : ""; ?></td>
		            <td><?php echo !empty($registro['KmRestante']) ? $registro['KmRestante'] : ""; ?></td>
		            <td><?php echo $registro['Status'] ?></td>
		            <td>
						<?php if(date('Y-m-d H:i:s',Comum::dateToTimestamp($registro['DataUltimaPosicao'])) >= $data_upos): ?>
							<span class="badge-empty badge badge-success" title="Posicionando Normal"></span>
						<?php else: ?>
							<span class="badge-empty badge" title="Sem Posicionamento"></span>
						<?php endif; ?>
					</td>
		            <td><?=$fimReal;?></td>
					<td><?=$registro['regiao_primeiro_alvo']?></td>					
					<td class="numeric"><?php echo !empty($registro['TemperaturaMinima']) ? $registro['TemperaturaMinima']:" "; ?></td>
					<td class="numeric"><?php echo !empty($registro['TemperaturaMaxima']) ? $registro['TemperaturaMaxima']:" "; ?></td>
					<td class="numeric"><?php echo !empty($registro['UltimaTemperatura']) ? $registro['UltimaTemperatura']:" "; ?></td>
					<td class="numeric"><?php echo number_format($registro['vtem_percentual_dentro'],2,',','.')."%" ?></td>
					<td class="numeric"><?php echo number_format($registro['vtem_percentual_fora'],2,',','.')."%" ?></td>
					<td><?php echo $registro['Solicitante'] ?></td>
		            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $registro['AlvoOrigem']), $registro['AlvoOrigemLatitude'], $registro['AlvoOrigemLongitude']) ?></td>
		            <td><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $registro['AlvoDestino']), $registro['AlvoDestinoLatitude'], $registro['AlvoDestinoLongitude']) ?></td>
		        </tr>
		        <?php endforeach; ?>        
		    </tbody>
		    <tfoot>
            <tr>
                <td colspan="31"><span class="totRegTxtBasico">Total de registro(s) ( </span><?php echo $this->Paginator->counter(array('format' => '%count%')); ?> <span class="totRegTxtBasico">) retornado(s)</span></td>
            </tr>
        </tfoot>
		</table>
	</div>
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
	<?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
	<?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>
	<?php echo $this->Javascript->codeBlock("
	    jQuery(document).ready(function(){
	        $('.horizontal-scroll').tableScroll({width:3000, height:(window.innerHeight-".($tipo_view != 'popup' ? "380" : "220").")});
			$('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });			
	    });", false);?>
	<?php if($this->layout != 'new_window'): ?>
		<?php echo $this->Js->writeBuffer(); ?>
	<?php endif; ?>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>