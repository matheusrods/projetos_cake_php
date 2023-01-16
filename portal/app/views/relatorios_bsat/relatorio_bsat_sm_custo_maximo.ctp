<?php $total = 0 ?>
<?php if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export'): ?>
<?php
	header('Content-type: text/csv');
	header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_custo_maximo.csv')));
    header('Pragma: no-cache');
   	echo iconv('UTF-8', 'ISO-8859-1', '"SM";"Cliente";"Placa";"Data";"Hora";"Origem";"UF";"Destino";"UF";"Motorista";"CPF";"Data Inicio";"Hora Inicio";"Data Fim";"Hora Fim";"Valor Carga (R$)";"Tecnologia";"Valor (R$)"')."\n";
	    foreach ($dados as $dado):
			$linha = '"'.$dado['Recebsm']['SM'].'";';
			$linha .= '"'.$dado['Recebsm']['cliente'].'";';
			$linha .= '"'.$dado['Recebsm']['placa'].'";';
			$linha .= '"'.AppModel::DbDatetoDate($dado[0]['datareceb']).'";';
			$linha .= '"'.$dado['Recebsm']['Hora_Receb'].'";';
			$linha .= '"'.$dado['CidadeOrigem']['Descricao'].'";';
            $linha .= '"'.$dado['CidadeOrigem']['Estado'].'";';
            $linha .= '"'.$dado['CidadeDestino']['Descricao'].'";';
            $linha .= '"'.$dado['CidadeDestino']['Estado'].'";';
            $linha .= '"'.$dado['Motorista']['Nome'].'";';
            $linha .= '"'.$dado['Motorista']['CPF'].'";';
            $linha .= '"'.AppModel::DbDatetoDate($dado[0]['datainc']).'";';
			$linha .= '"'.$dado['Recebsm']['Hora_Inc'].'";';
			$linha .= '"'.AppModel::DbDatetoDate($dado[0]['datafim']).'";';
			$linha .= '"'.$dado['Recebsm']['Hora_Fim'].'";';
			$linha .= '"'.$this->Buonny->moeda($dado['Recebsm']['ValSM']).'";';
			$linha .= '"'.$dado['Recebsm']['EQUIPAMENTO'].'";';
            if ($dado['Recebsm'][$dado['Recebsm']['placa']]['valor'] > $dado['MParametroFatura']['ValMaximo']) {
            	if ($dado['Recebsm'][$dado['Recebsm']['placa']]['ultimo']) {
            		$linha .= '"'.$this->Buonny->moeda($dado['MParametroFatura']['ValMaximo']).'"'."\n";
            		$total += $dado['MParametroFatura']['ValMaximo'];
            	} else {
            		$linha .= '""'."\n";
            	}
            } else {
            	$linha .= '"'.$this->Buonny->moeda($dado[0]['ValFixo']).'"'."\n";
            	$total += $dado[0]['ValFixo'];
            }

			echo iconv('UTF-8', 'ISO-8859-1', $linha);
		    
	    endforeach;

	echo iconv('UTF-8', 'ISO-8859-1', '"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"";"Valor Total (R$)";"'.$this->Buonny->moeda($total).'"');

    else:
?>
<!--<p><strong>&nbsp;&nbsp;&nbsp;.: Buonny Projetos e Servicos<br />
	Relatorio de Prestacao de Servicos<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $mes.'/'.$mesfinal ?> </strong></p>-->

<div class='well'>
    <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    <span class="pull-right">
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', 'javascript:void(0)', array('escape' => false, 'title' =>'Exportar para Excel', 'onclick' => "exportar_relatorio('{$data_inicial}', '{$data_final}', '{$codigo_cliente}', 'sm_custo_maximo')"));?>
	</span>
</div>

<br /><br />
<table class="table table-striped table-bordered" cellspacing=0>
    <thead>
        <tr>
            <th>SM</th>
            <th>Cliente</th>
            <th>Placa</th>
            <th>Data</th>
            <th>Hora</th>
            <th>Origem</th>
            <th>UF</th>
            <th>Destino</th>
            <th>UF</th>
            <th>Motorista</th>
            <th>CPF</th>
            <th>Data Inicio</th>
            <th>Hora Inicio</th>
            <th>Data Fim</th>
            <th>Hora Fim</th>
            <th style='text-align:right;'>Valor Carga (R$)</th>
            <th>Tecnologia</th>
            <th style='text-align:right;'>Valor (R$)</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach($dados as $dado): ?>
        <tr>
            <td><?= $dado['Recebsm']['SM'] ?>&nbsp;</td>
			<td><?= $dado['Recebsm']['cliente'] ?>&nbsp;</td>
			<td><?= $dado['Recebsm']['placa'] ?>&nbsp;</td>
			<td><?= AppModel::DbDatetoDate($dado[0]['datareceb']) ?> &nbsp;</td>
			<td><?= $dado['Recebsm']['Hora_Receb'] ?> &nbsp;</td>
			<td><?= $dado['CidadeOrigem']['Descricao'] ?></td>
            <td><?= $dado['CidadeOrigem']['Estado'] ?></td>
            <td><?= $dado['CidadeDestino']['Descricao'] ?></td>
            <td><?= $dado['CidadeDestino']['Estado'] ?></td>
            <td><?= $dado['Motorista']['Nome'] ?></td>
            <td><?= $dado['Motorista']['CPF'] ?></td>
            <td><?= AppModel::DbDatetoDate($dado[0]['datainc']) ?> &nbsp;</td>
			<td><?= $dado['Recebsm']['Hora_Inc'] ?> &nbsp;</td>
			<td><?= AppModel::DbDatetoDate($dado[0]['datafim']) ?> &nbsp;</td>
			<td><?= $dado['Recebsm']['Hora_Fim'] ?> &nbsp;</td>
			<td style='text-align:right;'><?= $this->Buonny->moeda($dado['Recebsm']['ValSM']) ?> &nbsp;</td>
			<td><?= $dado['Recebsm']['EQUIPAMENTO'] ?> &nbsp;</td>
            <td style='text-align:right;'><?php 
            if ($dado['Recebsm'][$dado['Recebsm']['placa']]['valor'] > $dado['MParametroFatura']['ValMaximo']) {
            	if ($dado['Recebsm'][$dado['Recebsm']['placa']]['ultimo']) {
            		echo $this->Buonny->moeda($dado['MParametroFatura']['ValMaximo']);
            		$total += $dado['MParametroFatura']['ValMaximo'];
            	} else {
            		echo "";
            	}
            } else {
            	echo $this->Buonny->moeda($dado[0]['ValFixo']);
            	$total += $dado[0]['ValFixo'];
            }

            	?>&nbsp;</td>
        </tr>
        <?php endforeach;?>
    </tbody>
    <tfoot>
    	<tr>
    		<td colspan='17'><strong>VALOR TOTAL (R$)</strong></td>
    		<td class="numeric"><strong><?= $this->Buonny->moeda($total) ?></strong></td>
    	</tr>
    </tfoot>
</table>

<div class='form-actions'>
	<?= $html->link('Voltar', array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos'), array('class' => 'btn')); ?>
</div>

<?php $this->addScript($this->Buonny->link_js('relatorios_bsat')) ?>
<?php endif ?>