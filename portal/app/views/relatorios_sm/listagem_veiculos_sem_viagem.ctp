<?php if(!empty($relatorio)):?>
<?php if ($this->passedArgs[0] == 'export'): 
        die('ainda não implementado');
	    header(sprintf('Content-Disposition: attachment; filename="%s"', basename('acompanhamento_de_viagens.csv')));
	    header('Pragma: no-cache');
		echo 'SM;"Pedido do Cliente";"Tecnologia";"Transportadora";"Placa";"Início Previsto";"Início Real";"Último Alvo";"Previsão Último Alvo";"Entrada Último Alvo";"Saída Último Alvo";"Status Último Alvo";"Posição Atual";"Data Última Posição";"Próximo Alvo";"Previsão Próximo Alvo";"Status Próximo Alvo";"Tempo Restante";"Km Restante";"Status"';
	    foreach($relatorio as $registro):
	        $registro = $registro[0];
	        $inicioReal = AppModel::dbDateToDate($registro['InicioReal']);
	        $fimReal = AppModel::dbDateToDate($registro['FimReal']);
	        $linha = "";
	        $linha .= '"'. $registro['SM'] . '";';
	        $linha .= '"'. $registro['PedidoCliente'] . '";';
	        $linha .= '"'. $registro['Tecnologia'] . '";';
	        $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['Transportadora']) . '";';
	        $linha .= '"'. $registro['Placa'] . '";';
	        $linha .= '"'. AppModel::dbDateToDate($registro['InicioPrevisto']) . '";';
	        $linha .= '"'. $inicioReal . '";';
	        $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['UltimoAlvo']) . '";';
	        $linha .= '"'. AppModel::dbDateToDate($registro['PrevisaoUltimoAlvo']) . '";';
	        $linha .= '"'. AppModel::dbDateToDate($registro['EntradaUltimoAlvo']) . '";';
	        $linha .= '"'. AppModel::dbDateToDate($registro['SaidaUltimoAlvo']) . '";';
	        $linha .= '"'. $registro['StatusUltimoAlvo'] . '";';
	        $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['UltimaPosicaoDescricao']) . '";';
	        $linha .= '"'. AppModel::dbDateToDate($registro['DataUltimaPosicao']) . '";';
	        $linha .= '"'. iconv('ISO-8859-1', 'UTF-8', $registro['ProximoAlvo']) . '";';
	        $linha .= '"'. AppModel::dbDateToDate($registro['PrevisaoProximoAlvo']) . '";';
	        $linha .= '"'. $registro['StatusProximoAlvo'] . '";';
	        $linha .= '"'. $registro['TempoRestante'] . '";';
	        $linha .= '"'. $registro['KmRestante'] . '";';
	        $linha .= '"'. $registro['Status'] . '"';
			echo "\n".$linha;
        endforeach;    
?>
	<?php else: ?>
		<div class="well">
			<strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
			<span class="pull-right">
				<?php echo $html->link('Atualizar', 'javascript:atualizaListaRelatorioSmVeiculosSemViagem();') ?>
				<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
			</span>
		</div>
		<?php 
		    echo $paginator->options(array('update' => 'div.lista')); 
		?>
		<div class='row-fluid' style='overflow-x:auto'>
	        <table class='table table-striped' style='width:3000px;max-width:none;'>
			    <thead>
			        <tr>
			            <th>Placa</th>
			            <th>Tipo</th>
			            <th>Modelo</th>
			            <th>Última Posição</th>
			            <th>Tipo Alvo</th>
			            <th>Data Computador Bordo</th>
			        </tr>
			    </thead>
			    <tbody>
			        <?php foreach($relatorio as $registro): ?>
			        <?php $registro = $registro[0]; ?>
			        <?php $inicioReal = AppModel::dbDateToDate($registro['InicioReal']); ?>
			        <?php $fimReal = AppModel::dbDateToDate($registro['FimReal']); ?>
			        <tr>
			            <td><?php echo $this->Buonny->codigo_sm($registro['SM']); ?></td>
			            <td><?php echo $registro['PedidoCliente'] ?></td>
			            <td><?php echo $registro['Tecnologia'] ?></td>
			            <td><?php echo $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Transportadora']), 30); ?></td>
			            <td><?php echo $this->Buonny->placa($registro['Placa'], $inicioReal, $fimReal); ?></td>
			            <td><?php echo AppModel::dbDateToDate($registro['InicioPrevisto']); ?></td>
			            <td><?php echo $inicioReal; ?></td>
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
			            <td><?php echo $registro['TempoRestante'] ?></td>
			            <td><?php echo $registro['KmRestante'] ?></td>
			            <td><?php echo $registro['Status'] ?></td>
			        </tr>
			        <?php endforeach; ?>        
			    </tbody>
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
		<?php echo $this->Js->writeBuffer(); ?>
	<?php endif; ?>
<?php endif; ?>
