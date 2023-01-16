<div class='form-procurar'>	
    <div class='well'>
	    <?php echo $this->BForm->create('Produto', array('autocomplete' => 'off', 'url' => array('controller' => 'produtos', 'action' => 'utilizacao'))) ?>
	    <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
            <?php echo $this->Buonny->input_periodo($this) ?>
	    </div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	    <?php echo $this->BForm->end();?>
	</div>
	<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){ setup_datepicker(); });', false); ?>
</div>
<table class='table table-striped'>
	<thead>
		<th class='input-small'>Código</th>
		<th>Produto</th>
		<th class='input-small'>Naveg</th>
		<th class='input-medium numeric'>Valor</th>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php if (count($utilizacoes)): ?>
			<?php foreach ($utilizacoes as $utilizacao): ?>
				<?php $total += $utilizacao[0]['valor_a_pagar'] ?>
				<tr>
					<td><?= $this->Html->link($utilizacao[0]['codigo_produto'], "javascript:utilizacao_de_servicos('{$this->data['Produto']['codigo_cliente']}', '{$this->data['Produto']['data_inicial']}','{$this->data['Produto']['data_final']}', '{$utilizacao[0]['codigo_produto']}')") ?></td>
					<td><?= $utilizacao[0]['descricao'] ?></td>
					<td><?= $utilizacao[0]['codigo_naveg'] ?></td>
					<td class='input-medium numeric'><?= $this->Buonny->moeda($utilizacao[0]['valor_a_pagar']) ?></td>
				</tr>
			<?php endforeach ?>
		<?php endif ?>
	</tbody>
	<tfoot>
		<td></td>
		<td></td>
		<td></td>
		<td class='input-medium numeric'><?= $this->Buonny->moeda($total) ?></td>
	</tfoot>
</table>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
<?php echo $this->Javascript->codeBlock("
    function utilizacao_de_servicos( codigo_cliente, data_inicial, data_final, codigo_produto ) {   
        var form = document.createElement('form');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos/0/' + Math.random());
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_produto]');
        field.setAttribute('value', codigo_produto);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        form.submit();
    }");
?>