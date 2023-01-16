<?php if ($this->passedArgs[0] == 'export'):
	header('Content-type: application/vnd.ms-excel'); 
    header(sprintf('Content-Disposition: attachment; filename="%s"', basename('faturamento_analitico_buonnysat.csv')));
    header('Pragma: no-cache');
	echo iconv('UTF-8', 'ISO-8859-1', 'Cliente;Razão Social;Região;Tipo do Faturamento;Gestor;Corretora;Seguradora;Qtd. Frota;Valor Total Frota;Qtd. Avulso;Valor Total Avulso;Qtd. SM Monitorada;Valor Total SM Monitorada;Qtd. SM Telemonitorada;Valor Total SM Telemonitorada;Qtd. Dia;Valor Total Dia;Qtd. KM;Valor Total KM');
    foreach($detalhesItensPedidos as $registro):

        $linha = "";
        $linha .= $registro['Pedido']['codigo_cliente_pagador'] . ';';
        $linha .= $registro['Cliente']['razao_social'] . ';';
        $linha .= $registro['EnderecoRegiao']['descricao'] . ';';
        if($registro[0]['regiao_tipo_faturamento'] == 2)
    		$linha .= 'Total' . ';';
    	elseif($registro[0]['regiao_tipo_faturamento'] == 1)
    		$linha .= 'Parcial' . ';';
    	else
    		$linha .= '--' . ';';
        $linha .= $registro['Gestor']['nome'] . ';';
        $linha .= $registro['Corretora']['nome'] . ';';
        $linha .= $registro['Seguradora']['nome'] . ';';
        $linha .= $this->Buonny->moeda($registro['Frota']['qtd_frota'],array('places' => 0, 'nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro['Frota']['valor_frota'],array('nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro['Avulso']['qtd_placa_avulsa'],array('places' => 0, 'nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro['Avulso']['valor_placa_avulsa'],array('nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['qtd_sm_monitorada'],array('places' => 0, 'nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['valor_sm_monitorada'],array('nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['qtd_sm_telemonitorada'],array('places' => 0, 'nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['valor_sm_telemonitorada'],array('nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['qtd_dia'],array('places' => 0, 'nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['valor_dia'],array('nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['qtd_km'],array('places' => 0, 'nozero' => true)) . ';';
        $linha .= $this->Buonny->moeda($registro[0]['valor_km'],array('nozero' => true)) . ';';
        
		echo "\n".iconv('UTF-8', 'ISO-8859-1', $linha);
    endforeach;    
?>
<?php else: ?>
	<?php if(!empty($detalhesItensPedidos)): ?>
	<div class="well">
		<strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
		<span class="pull-right">
			<?php echo $html->link('Atualizar', 'javascript:atualizaListaDetalhesItensPedidos();') ?>
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
		</span>
	</div>
	<div class='row-fluid'>
		<table class='table table-striped horizontal-scroll' style='width:3000px;max-width:none;'>
		    <thead>
		        <tr>
		            <th>Cliente</th>
		            <th>Razão Social</th>
		            <th>Região</th>
		            <th>Tipo do Faturamento</th>
		            <th>Gestor</th>
		            <th>Corretora</th>
		            <th>Seguradora</th>
		            <th class='numeric'>Qtd. Frota</th>
		            <th class='numeric'>Valor Total Frota</th>
		            <th class='numeric'>Qtd. Avulso</th>
		            <th class='numeric'>Valor Total Avulso</th>
		            <th class='numeric'>Qtd. SM Monitorada</th>
		            <th class='numeric'>Valor Total SM Monitorada</th>
		            <th class='numeric'>Qtd. SM Telemonitorada</th>
		            <th class='numeric'>Valor Total SM Telemonitorada</th>
		            <th class='numeric'>Qtd. Dia</th>
		            <th class='numeric'>Valor Total Dia</th>
		            <th class='numeric'>Qtd. KM</th>
		            <th class='numeric'>Valor Total KM</th>
		        </tr>
		    </thead>
			<?php foreach ($detalhesItensPedidos as $detalheItemPedido): ?>
			    <tr>
			        <td><?php echo $detalheItemPedido['Pedido']['codigo_cliente_pagador'] ?></td>
			        <td><?php echo $detalheItemPedido['Cliente']['razao_social'] ?></td>
			        <td><?php echo $detalheItemPedido['EnderecoRegiao']['descricao'] ?></td>
			        <td><?php 
			        	if($detalheItemPedido[0]['regiao_tipo_faturamento'] == 2)
			        		echo 'Total';
			        	elseif($detalheItemPedido[0]['regiao_tipo_faturamento'] == 1)
			        		echo 'Parcial';
			        	else
			        		echo '--';
			        ?></td>
			        <td><?php echo $detalheItemPedido['Gestor']['nome'] ?></td>
			        <td><?php echo $detalheItemPedido['Corretora']['nome'] ?></td>
			        <td><?php echo $detalheItemPedido['Seguradora']['nome'] ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido['Frota']['qtd_frota'],array('places' => 0, 'nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido['Frota']['valor_frota'],array('nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido['Avulso']['qtd_placa_avulsa'],array('places' => 0, 'nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido['Avulso']['valor_placa_avulsa'],array('nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['qtd_sm_monitorada'], array('places' => 0, 'nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['valor_sm_monitorada'], array('nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['qtd_sm_telemonitorada'], array('places' => 0, 'nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['valor_sm_telemonitorada'], array('nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['qtd_dia'], array('places' => 0, 'nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['valor_dia'], array('nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['qtd_km'], array('places' => 0, 'nozero' => true)) ?></td>
			        <td class='numeric'><?php echo $this->Buonny->moeda($detalheItemPedido[0]['valor_km'], array('nozero' => true)) ?></td>
			    </tr>
			<?php endforeach; ?>
		</table>
	</div>
	<?php endif; ?>
<?php endif; ?>