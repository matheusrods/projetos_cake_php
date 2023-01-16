<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Nome', 'razao_social') ?></th>
            <th colspan="3"><?php echo $this->Paginator->sort('Valor', 'valor_total') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($clientes as $cliente): ?>
        <tr>
			<td class="input-mini"><?= $this->Html->link($cliente['Cliente']['codigo'], "javascript:utilizacao_de_servicos_historico('{$cliente['Cliente']['codigo']}', '{$filtros['mes_referencia']}', '{$filtros['ano_referencia']}')") ?></td>
            <td><?php echo $cliente['Cliente']['razao_social'] ?></td>
            <td><?php echo $this->Buonny->moeda($cliente[0]['valor_total']) ?></td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
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
<?php if (!empty($clientes)): ?>
    <?php echo $this->Javascript->codeBlock("
		function utilizacao_de_servicos_historico( codigo_cliente, data_inicial, data_final ) {   
			var form = document.createElement('form');
			var form_id = ('formresult' + Math.random()).replace('.','');
			form.setAttribute('method', 'post');
			form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos_historico/1');
			form.setAttribute('target', form_id);
			field = document.createElement('input');
			field.setAttribute('name', 'data[Cliente][codigo_cliente]');
			field.setAttribute('value', codigo_cliente);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
			field = document.createElement('input');
			field.setAttribute('name', 'data[Cliente][mes_faturamento]');
			field.setAttribute('value', data_inicial);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
			field = document.createElement('input');
			field.setAttribute('name', 'data[Cliente][ano_faturamento]');
			field.setAttribute('value', data_final);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
			document.body.appendChild(form);
			var janela = window_sizes();
			window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
			form.submit();
		}
    "); ?>
<?php endif ?>