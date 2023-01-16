<?php if(isset($listagem) && !empty($listagem)):?>
	<div class="well">
		<span class="pull-right">
	        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => 'analitico_listagem','export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
	    </span>
	</div>	
<?php
    echo $paginator->options(array('update' => 'div.lista'));
?>
<table class='table table-striped table-bordered' style="max-width:800; white-space:nowrap">
	<thead>	
		<th class="input-mini">SM</th>
		<th class="input-mini">Placa</th>
		<th class="input-mini">Evento Gerado</th>
		<th class="input-mini">Evento Tratado</th>
		<th class="input-mini numeric">Tempo de Tratativa (minutos)</th>
		<th class="input-mini">Operador Tratamento</th>
		<th class="input-mini">Operador Geração</th>
		<th class="input-mini">Embarcador</th>
		<th class="input-mini">Transportador</th>
		<th class="input-mini">Tecnologia</th>
		<th class="input-mini">Motorista</th>
		<th class="input-mini">Evento</th>
		<th class="input-mini">Estação</th>
	</thead>
	<tbody>
		<?php foreach ($listagem as $dado): ?>
			<tr>
				<?php 
				$usuario_tratativa = '';
				$usuario_responsavel = '';
				if(!empty($dado['TEsisEventoSistema']['esis_data_leitura'])):
					$usuario_tratativa = empty($dado['TEsisEventoSistema']['esis_usu_codigo_leitura']) ? 'Sistema' : $dado['UsuarioTratativa']['usua_login'];
				endif;
				if($dado['TEsisEventoSistema']['esis_usu_pfis_responsavel'] != 0):
					$usuario_responsavel = $dado['UsuarioResponsavel']['usua_login'];
				endif;

				?>	
				<td><?= $this->Buonny->codigo_sm($dado['TViagViagem']['viag_codigo_sm'])?></td>
				<td><?= $this->Buonny->placa($dado[0]['placa'],Date('d/m/Y H:i:s'),Date('d/m/Y H:i:s'))?></td>
				<td class="input-mini"><?= $dado['TEsisEventoSistema']['esis_data_cadastro'] ?></td>
				<td class="input-mini"><?= $dado['TEsisEventoSistema']['esis_data_leitura'] ?></td>
				<td class="input-mini numeric"><?= $this->Buonny->moeda($dado[0]['tempo_tratado_minutos'], array('nozero' => true, 'places' => 0)) ?></td>
				<td class="input-mini"><?=  $usuario_tratativa?></td>
				<td class="input-mini"><?=  $usuario_responsavel?></td>
				<td class="input-mini"><?= $dado['Embarcadores']['pjur_razao_social'] ?></td>
				<td class="input-mini"><?= $dado['Transportadores']['pjur_razao_social']; ?></td>
				<td><?= $dado[0]['tecnologia'] ?></td>
				<td><?= $dado[0]['motorista'] ?></td>
				<td class="input-mini"><?= $dado[0]['evento']; ?></td>
				<td class="input-mini"><?= $dado[0]['estacao']; ?></td>

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
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages% - Total de %count%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
	<div class="alert">Nenhum registro encontrado.</div>
<?php endif;?>
