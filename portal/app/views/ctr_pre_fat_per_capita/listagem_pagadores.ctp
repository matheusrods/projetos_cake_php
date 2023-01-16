<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $dados_matriz['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $dados_matriz['Cliente']['razao_social']); ?>
</div>
<div class="lista">
	<?php if(count($clientes_pagadores) > 0): ?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="input-mini">Código Cliente Pagador</th>
					<th class="input-mini">Nome Fantasia</th>
					<th class="input-mini numeric">Quantidade</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				
				$qtd_total = 0;

				foreach($clientes_pagadores as $cliente) : 

						$codigo_cliente = $cliente['codigo_cliente_pagador'];
				?>
					<tr>
						<td class="input-mini">
						<?= 
							$this->Html->link($codigo_cliente, "javascript:utilizacao_de_servicos_filhos_pagador('{$codigo_cliente}', '{$dados['data_inicial']}', '{$dados['data_final']}', '117')");
						?>	
						</td>
						<td><?= $cliente['nome_fantasia'] ?></td>
						<td class="input-mini numeric"><?= $cliente['qtd'] ?></td>
					</tr>
				<?php 
					$qtd_total += $cliente['qtd'];
				endforeach; 
				?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">Total</td>
					<td class="numeric"><?=$qtd_total?></td>
				</tr>
			</tfoot>
		</table>
	<?php else:?>
		<div class="alert">Nenhum resultado encontrado.</div>
	<?php endif;?>
</div>
<div class='form-actions well'>
    <?php echo $html->link('Voltar', array('controller' => 'CtrPreFatPerCapita', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
 <?php echo $this->Javascript->codeBlock("
    function utilizacao_de_servicos_filhos_pagador( codigo_cliente, data_inicial, data_final, codigo_produto ) {     
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos_filhos_pagador/1');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_produto]');
        field.setAttribute('value', codigo_produto);
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
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }
"); ?>