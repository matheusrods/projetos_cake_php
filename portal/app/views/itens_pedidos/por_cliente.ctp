<div class='well'>
    <?php echo $this->BForm->create('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'por_cliente'))) ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador', false, 'ItemPedido') ?>
        <?php echo $this->BForm->input('mes_referencia', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-small', 'options' => $meses)) ?>
        <?php echo $this->BForm->input('ano_referencia', array('label' => false, 'placeholder' => 'Ano','class' => 'input-small', 'options' => $anos)) ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end();?>
</div>
<?php if(isset($cliente)): ?>
<div class="well">
        <strong>Cliente:</strong> <?php echo $cliente; ?>
    </div>
<?php endif; ?>

<table class='table table-striped tablesorter'>
	<thead>
		<th>                <?php echo $this->Html->link('Produto',         'javascript:void(0)') ?></th>
		<th class='numeric'><?php echo $this->Html->link('Taxa Bancária',   'javascript:void(0)') ?></th>
		<th class='numeric'><?php echo $this->Html->link('Prêmio Mínimo',   'javascript:void(0)') ?></th>
		<th class='numeric'><?php echo $this->Html->link('Total Utilizado', 'javascript:void(0)') ?></th>
		<th class='numeric'><?php echo $this->Html->link('Descontos',       'javascript:void(0)') ?></th>
		<th class='numeric'><?php echo $this->Html->link('Total',           'javascript:void(0)') ?></th>
	</thead>
	<tbody>
	 <?php $total_utilizado = 0 ?>
	 <?php $total_desconto  = 0 ?>
	 <?php $total           = 0 ?>
	 <?php if ($itens_pedidos_totalizados) :?>
		 <?php foreach($itens_pedidos_totalizados as $totalizado): ?>
		    <tr>
		        <td><?php echo $this->Html->link($totalizado[0]['descricao'],
		                                         'javascript:void(0)',
		                                          array( 'onclick' => "por_servico('ItemPedido',
		                                                                           '{$this->data['ItemPedido']['codigo_cliente_pagador']}',
		                                                                           '{$this->data['ItemPedido']['mes_referencia']}',
		                                                                           '{$this->data['ItemPedido']['ano_referencia']}',
		                                                                           '{$totalizado[0]['codigo']}'
		                                                                          )" )) ?></td>

		        <td class='numeric'><?= $this->Buonny->moeda($totalizado[0]['valor_taxa_bancaria']) ?></td>
		        <td class='numeric'><?= $this->Buonny->moeda($totalizado[0]['valor_premio_minimo']) ?></td>
		        <td class='numeric'><?= $this->Buonny->moeda($totalizado[0]['valor_utilizado']) ?></td>
		        <td class='numeric'><?= $this->Buonny->moeda($totalizado[0]['desconto']) ?></td>
		        <td class='numeric'><?= $this->Buonny->moeda($totalizado[0]['valor']) ?></td>

		        <?php $total_utilizado += $totalizado[0]['valor_utilizado'] ?>
		        <?php $total_desconto  += $totalizado[0]['desconto'] ?>
		        <?php $total           += $totalizado[0]['valor'] ?>
		    </tr>
		<?php endforeach ?>
	<?php endif ?>
	</tbody>
	<tfoot>
		<tr>
		  <td></td>
		  <td></td>
		  <td></td>
		  <td class='numeric'><?= $this->Buonny->moeda($total_utilizado) ?></td>
		  <td class='numeric'><?= $this->Buonny->moeda($total_desconto) ?></td>
		  <td class='numeric'><?= $this->Buonny->moeda($total) ?></td>
		</tr>
	</tfoot>
</table>

<?php if (isset($clientes_monitora) && $clientes_monitora): ?>
	<h5>Configuração Utilizadores</h4>
	<table class='table table-striped'>
		<thead>
			<th>Código</th>
			<th>Razão Social</th>
			<th class='numeric'>Determinado</th>
			<th class='numeric'>Frota</th>
			<th class='numeric'>Placa Avulsa</th>
			<th class='numeric'>Por Dia</th>
			<th class='numeric'>Por KM</th>
			<th class='numeric'>Por SM Monitorada</th>
			<th class='numeric'>Por SM TeleMonitorada</th>
			<th class='numeric'>Máximo</th>
			<th class='numeric'>Prêmio Mínimo</th>
		</thead>
		<tbody>
			<?php foreach ($clientes_monitora as $cliente_monitora): ?>
				<tr>
					<td><?= $cliente_monitora['ClientEmpresa']['Codigo'] ?></td>
					<td><?= $cliente_monitora['ClientEmpresa']['Raz_Social'] ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['ValDeterminado'] > 0 ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValDeterminado']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['Frota'] == 'S' ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValFrota']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['Avulso'] == 'S' ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValAvulso']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['DIA'] == 'S' ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValDia']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['KM'] == 'S' ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValKm']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['SM'] == 'S' && $cliente_monitora['MParametroFatura']['Fixo'] == 'S' ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValFixo']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['SM'] == 'S' && $cliente_monitora['MParametroFatura']['SMTele'] == 'S' ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValTele']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['Maximo'] == 'S' ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['ValMaximo']) : '') ?></td>
					<td class='numeric'><?= ($cliente_monitora['MParametroFatura']['PremioMinimo'] > 0 ? $this->Buonny->moeda($cliente_monitora['MParametroFatura']['PremioMinimo']) : '') ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php endif ?>


<?php $this->addScript($this->Buonny->link_js('pedidos')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();

        $.tablesorter.addParser({
            id: "brazil",
            is: function(s) {
                return false;
            },
            format: function(s) {
               s = s.replace(/\./g,"");
               s = s.replace(/\,/g,".");
               return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9.-]/g),""));
            },
            type: "numeric"
        });

        jQuery("table.table").tablesorter({
            headers: {
                2: {sorter: "brazil"}
            },
            widgets: ["zebra"]
        });
    });', false);
?>