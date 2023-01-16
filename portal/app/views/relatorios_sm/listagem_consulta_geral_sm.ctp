<?php $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour')); ?>
<?php if(empty($filtros['RelatorioSmConsulta'])): ?>
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
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => 'relatorios_sm', 'action' => 'listagem_consulta_geral_sm', 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
			<?php echo $this->Buonny->exportacao_relatorio_email($this, 'RelatorioSm', 'consultaGeralSm' ) ?>
		</span>
	</div>
	<?php 
	    echo $paginator->options(array('update' => 'div.lista')); 
	?>
	<div class='row-fluid' style='overflow-x:auto'>
        <table class='table table-striped horizontal-scroll' style='width:3000px;max-width:none;'>
		    <thead>
		        <tr>
                    <th class='input-small'  title="Código SM">SM</th>
                    <th class='input-medium' title="Placa">Placa</th>
                    <th class='input-medium' title="Início">Início</th>
                    <th class='input-medium' title="Fim">Fim</th>
                    <th class='input-xxlarge' title="Transportador">Transportador</th>
                    <th class='input-xxlarge' title="Embarcador">Embarcador</th>
                    <th class='input-xxlarge' title="Gerenciadora">Gerenciadora</th>
                    <th class='input-large' title="Estação">Estação</th>
                    <th class='input-large' title="Tecnologia">Tecnologia</th>
                    <th class='input-large' title="Número Terminal">Número Terminal</th>
                    <th class='input-medium' title="Previsão de Inicio">Previsão de Inicio</th>
                    <th class='input-medium' title="Previsão de Fim">Previsão de Fim</th>
                    <th class='input-xlarge' title="Cidade Origem">Cidade Origem</th>
                    <th class='input-small' title="Estado Origem">Estado Origem</th>
                    <th class='input-xlarge' title="Cidade Destino">Cidade Destino</th>
                    <th class='input-small' title="Estado Destino">Estado Destino</th>
                    <th class='input-xlarge' title="Nome">Nome Motorista</th>
                    <th class='input-medium' title="CPF">CPF Motorista</th>
                    <th style="text-align: right;" class='input-small' title="Valor SM">Valor SM</th>
		        </tr>
		    </thead>
		    <tbody>
		        <?php foreach($relatorio as $registro): ?>
		        <?php $registro = $registro[0]; ?>
		        <?php $inicioReal = AppModel::dbDateToDate($registro['InicioPrevisto']); ?>
		        <?php $fimReal = empty($registro['FimReal']) ? date('d/m/Y H:i:s') : AppModel::dbDateToDate($registro['FimReal']); ?>
		        <tr>
		            <td><?php echo $this->Buonny->codigo_sm($registro['SM']); ?></td>
		            <td><?php echo isset($registro['Placa'][0]) && ctype_alpha($registro['Placa'][0]) ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $registro['Placa']), $inicioReal, $fimReal) : $registro['Chassi'];?></td>
		            <td><?php echo $inicioReal ?></td>
		            <td><?php echo isset($registro['FimReal']) ? AppModel::dbDateToDate($registro['FimReal']) : NULL; ?></td>
		            <td><?php echo $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Transportadora']), 30); ?></td>
		            <td><?php echo $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Embarcador']), 30); ?></td>
		            <td><?php echo isset($registro['Gerenciadora']) ? $this->Buonny->truncate(iconv('ISO-8859-1', 'UTF-8', $registro['Gerenciadora']), 30) : 'Não Possui Gerenciadora'; ?></td>
					<td><?php echo $registro['estacao'] ?></td>
		            <td><?php echo $registro['Tecnologia'] ?></td>
					<td><?php echo $registro['numero_terminal'] ?></td>
		            <td><?php echo AppModel::dbDateToDate($registro['InicioPrevisto']); ?></td>		            
		            <td><?php echo AppModel::dbDateToDate($registro['FimPrevisto']); ?></td>
					<td><?php echo $registro['cidade_origem'] ?></td>
					<td><?php echo $registro['estado_origem'] ?></td>
					<td><?php echo $registro['cidade_destino'] ?></td>
					<td><?php echo $registro['estado_destino'] ?></td>
		            <td><?php echo $registro['pess_nome'] ?></td>
		            <td><?php echo comum::formatarDocumento($registro['pfis_cpf']);?></td>
		            <td style="text-align: right;" ><?= $this->Buonny->moeda( $registro['valor_carga'] ); ?></td>					
		        </tr>
		        <?php endforeach; ?>        
		    </tbody>
		    <tfoot>
            <tr>
                <td id="boxTotReg" class='numeric'><span class="totRegTxtBasico">Total de registro(s) ( </span><?php echo $this->Paginator->counter(array('format' => '%count%')); ?> <span class="totRegTxtBasico">) retornado(s)</span></td>
                <td colspan="19"></td>
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
	        $('.horizontal-scroll').tableScroll({width:3000, height:(window.innerHeight-380)});
			$('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });
	    });", false);
	?>
	<?php if($this->layout != 'new_window'): ?>
		<?php echo $this->Js->writeBuffer(); ?>
	<?php endif; ?>
<?php endif; ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>