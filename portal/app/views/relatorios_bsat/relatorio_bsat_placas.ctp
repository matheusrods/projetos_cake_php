<?php 
$valor_total = 0;
$linha = null;
?>
<?php if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export'): ?>
<?php
		header('Content-type: text/csv');
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename('relatorio_placas.csv')));
	    header('Pragma: no-cache');
	   	echo iconv('UTF-8', 'ISO-8859-1', '"Placa";"Valor dos Servicos"')."\n";
	    foreach ($dados as $dado):
	    	$linha = '"'.$dado['0']['placa'].'";';
			$linha .= '"'.$this->Buonny->moeda($dado['0']['valor']).'"'."\n";
			$placa = $dado['0']['placa'];
			echo iconv('UTF-8', 'ISO-8859-1', $linha);
	    endforeach;
    else:
?>

<div class='well'>
    <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    <span class="pull-right">
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', 'javascript:void(0)', array('escape' => false, 'title' =>'Exportar para Excel', 'onclick' => "exportar_relatorio('{$this->data['RelatorioBsat']['data_inicial']}', '{$this->data['RelatorioBsat']['data_final']}', '{$this->data['RelatorioBsat']['codigo_cliente']}', 'placas')"));?>
	</span>
</div>


<table class="table table-striped table-bordered">
    <thead>
        <tr >
            <th>Placa</th>
            <th class='numeric'>Valor dos Serviços (R$)</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach($dados as $dado): ?>
    		<?php $valor_total += $dado['0']['valor'] ?>
	        <tr >
	            <td><?= $dado['0']['placa'] ?></td>
	            <td class='numeric'><?= $this->Buonny->moeda($dado['0']['valor']) ?></td>
	        </tr>
	        <?php $placa = $dado['0']['placa'] ?>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
    	<td class='numeric'><?= count($dados) ?></td>
    	<td class='numeric'><?= $this->Buonny->moeda($valor_total) ?></td>
    </tfoot>
</table>

<div class='form-actions'>
	<?= $html->link('Voltar', array('controller' => 'clientes', 'action' => 'utilizacao_de_servicos'), array('class' => 'btn')); ?>
</div>

<?php $this->addScript($this->Buonny->link_js('relatorios_bsat')) ?>
<?php endif ?>