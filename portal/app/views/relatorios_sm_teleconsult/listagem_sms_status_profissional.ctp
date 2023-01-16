<?php $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour')); ?>
<?php if(empty($filtros['RelatorioSmTeleconsult'])): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php elseif(empty($relatorio)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<div class="well">
		<span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
	</div>
	<?php 
	    echo $paginator->options(array('update' => 'div.lista')); 
	?>
	<div class='row-fluid' >
        <table class='table table-striped horizontal-scroll' style='width:2200px;max-width:none'>
		    <thead>
		        <tr>
		            <th class="input-mini">SM</th>
		            <th class="input-xxlarge">Embarcador</th>
		            <th class="input-xxlarge">Transportador</th>
		            <th class="input-medium">Placa</th>
		            <th class="input-small">Tipo do Veículo</th>
		            <th class="input-xlarge">Alvo Origem</th>
		            <th class="input-medium">CPF Motorista</th>
		            <th class="input-xlarge">Nome Motorista</th>
		            <th class="input-xlarge">Tipo Profissional TLC</th>
		            <th class="input-xxlarge">Último Status Antes SM</th>
		            <th class="input-small">Data Último Status Antes SM</th>
		            <th class="input-xxlarge">Último Status Atual</th>
		            <th class="input-small">Data Último Status Atual</th>
		        </tr>
		    </thead>
		    <tbody>
		        <?php foreach($relatorio as $registro): ?>
		        	<? $registro = $registro[0] ?>
			        <?php $inicioReal = AppModel::dbDateToDate($filtros['RelatorioSmTeleconsult']['data_previsao_de']); ?>
			        <?php $fimReal = AppModel::dbDateToDate($filtros['RelatorioSmTeleconsult']['data_previsao_ate']); ?>
		        <tr>
		            <td class="input-mini"><?php echo $this->Buonny->codigo_sm($registro['viag_codigo_sm']); ?></td>
		            <td><?php echo $registro['embarcador'] ?></td>
		            <td><?php echo $registro['transportador'] ?></td>
		            <td class="input-medium"><?php echo isset($registro['veic_placa'][0]) && ctype_alpha($registro['veic_placa'][0])
		                ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['veic_placa']), $inicioReal, $fimReal)
		                : '';
		            ?></td>
		            <td class="input-small"><?php echo $registro['tvei_descricao'] ?></td>
		            <td class="input-small"><?php echo $this->Buonny->posicao_geografica(iconv('ISO-8859-1', 'UTF-8', $registro['refe_descricao']), $registro['refe_latitude'], $registro['refe_longitude']) ?></td>
		            <td class="input-medium"><?php echo Comum::formatarDocumento($registro['pfis_cpf']) ?></td>
		            <td><?php echo $registro['pess_nome'] ?></td>
		            <td class="input-medium"><?php echo iconv('ISO-8859-1', 'UTF-8', $registro['tipo_profissional']) ?></td>
		            <td><?php echo (!empty($registro['ultimo_status_antes_sm']) ? $status_teleconsult[$registro['ultimo_status_antes_sm']] : '') ?></td>
		            <td class="input-medium"><?php echo $registro['data_ultimo_status_antes_sm']; ?></td>
		            <td><?php echo (!empty($registro['codigo_status']) ? $status_teleconsult[$registro['codigo_status']] : '') ?></td>
		            <td class="input-small"><?php echo $registro['data_ultimo_status']; ?></td>
		        </tr>
		        <?php endforeach; ?>        
		    </tbody>
		    <tfoot>
            <tr>
                <td id="boxTotReg" colspan="12"><span class="totRegTxtBasico">Total de registro(s) ( </span><?php echo $this->Paginator->counter(array('format' => '%count%')); ?> <span class="totRegTxtBasico">) retornado(s)</span></td>
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
	<?php echo $this->Js->writeBuffer(); ?>
	
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>