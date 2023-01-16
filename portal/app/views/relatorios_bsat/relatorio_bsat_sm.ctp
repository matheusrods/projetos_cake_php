<?php if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export'): ?>
<?php
	header('Content-type: text/csv');
	header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_sm.csv')));
    header('Pragma: no-cache');
   	echo iconv('UTF-8', 'ISO-8859-1', '"SM";"Placa";"Data";"Hora";"Origem";"UF";"Destino";"UF";"Data Inicio";"Hora Inicio";"Data Fim";"Hora Fim";"Valor Carga (R$)";"Tecnologia";"Valor (R$)"')."\n";
	    foreach ($dados as $dado):
			$linha = '"'.$dado['Recebsm']['SM'].'";';
			$linha .= '"'.$dado['Recebsm']['placa'].'";';
			$linha .= '"'.AppModel::DbDatetoDate($dado[0]['datareceb']).'";';
			$linha .= '"'.$dado['Recebsm']['Hora_Receb'].'";';
			$linha .= '"'.$dado['CidadeOrigem']['Descricao'].'";';
            $linha .= '"'.$dado['CidadeOrigem']['Estado'].'";';
            $linha .= '"'.$dado['CidadeDestino']['Descricao'].'";';
            $linha .= '"'.$dado['CidadeDestino']['Estado'].'";';
            $linha .= '"'.AppModel::DbDatetoDate($dado[0]['datainc']).'";';
			$linha .= '"'.$dado['Recebsm']['Hora_Inc'].'";';
			$linha .= '"'.AppModel::DbDatetoDate($dado[0]['datafim']).'";';
			$linha .= '"'.$dado['Recebsm']['Hora_Fim'].'";';
			$linha .= '"'.$this->Buonny->moeda($dado['Recebsm']['ValSM']).'";';
			$linha .= '"'.$dado['Recebsm']['EQUIPAMENTO'].'";'."\n";
			echo iconv('UTF-8', 'ISO-8859-1', $linha);
	    endforeach;
    else:
?>

<div class='well'>
    <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    <span class="pull-right">
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', 'javascript:void(0)', array('escape' => false, 'title' =>'Exportar para Excel', 'onclick' => "exportar_relatorio('{$this->data['RelatorioBsat']['data_inicial']}', '{$this->data['RelatorioBsat']['data_final']}', '{$this->data['RelatorioBsat']['cliente_pagador']}', 'sm')"));?>
	</span>
</div>

<table class="table table-striped table-bordered" >
    <thead>
        <tr >
            <th >SM</th>
            <th >Placa</th>
            <th >Data</th>
            <th >Hora</th>
            <th >Origem</th>
            <th >UF</th>
            <th >Destino</th>
            <th >UF</th>
            <th >Data Inicio</th>
            <th >Hora Inicio</th>
            <th >Data Fim</th>
            <th >Hora Fim</th>
            <th class='numeric'>Valor Carga (R$)</th>
            <th >Tecnologia</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach($dados as $dado): ?>
            <tr>
                <td><?= $this->Buonny->codigo_sm($dado['Recebsm']['SM']) ?></td>
    			<td><?= $dado['Recebsm']['Placa'] ?></td>
    			<td><?= $dado[0]['dta_receb'] ?></td>
    			<td><?= $dado['Recebsm']['Hora_Receb'] ?></td>
    			<td><?= $dado['CidadeOrigem']['Descricao'] ?></td>
                <td><?= $dado['CidadeOrigem']['Estado'] ?></td>
                <td><?= $dado['CidadeDestino']['Descricao'] ?></td>
                <td><?= $dado['CidadeDestino']['Estado'] ?></td>
                <td><?= AppModel::DbDatetoDate($dado[0]['dta_inc']) ?></td>
    			<td><?= $dado['Recebsm']['Hora_Inc'] ?></td>
    			<td><?= AppModel::DbDatetoDate($dado[0]['dta_fim']) ?></td>
    			<td><?= (strlen($dado['Recebsm']['Hora_Fim'])<=5 ? $dado['Recebsm']['Hora_Fim'] : Comum::formataData($dado[0]['hora_fim_format'],'hms','hm')) ?></td>
    			<td class='numeric'><?= $this->Buonny->moeda($dado['Recebsm']['ValSM']) ?></td>
    			<td><?= $dado['Recebsm']['Equipamento'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='numeric'><?= count($dados) ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class='form-actions'>
	<?= $html->link('Voltar', array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos'), array('class' => 'btn')); ?>
</div>

<?php $this->addScript($this->Buonny->link_js('relatorios_bsat')) ?>
<?php endif ?>