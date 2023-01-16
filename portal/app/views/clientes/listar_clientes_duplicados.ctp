<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th><?= $this->Html->link('Cód.', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('CNPJ', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Razão Social', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Possui produto TLC?', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Status do produto TLC', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Pagador do TLC', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Faturou TLC (últimos 6 meses)?', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Possui produto BSAT?', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Status do produto BSAT', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Pagador do BSAT', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Faturou BSAT (últimos 6 meses)?', 'javascript:void(0)') ?></th>
			<th>Manter Cliente</th>
			
		</thead>
		<tbody>
			<?php foreach ($clientes_duplicados as $cliente): ?>

			<?php
                    $pattern = array(
                        '/(.*inativ.*)/i',
                    	'/(.*pend.+ncia.*)/i',
                    	'/(.*desatualizad.*)/i',
                    );
                    $replacement = array(
                        'INATIVO',
                    	'PENDÊNCIA FIN.',
                    	'DESATUALIZADO',
                    );
                    $motivo_bloqueio_tlc  = preg_replace($pattern, $replacement, $cliente[0]['TLCMotivo']);
                    $motivo_bloqueio_bsat = preg_replace($pattern, $replacement, $cliente[0]['BSATMotivo']);
                    
                    switch ($motivo_bloqueio_tlc) {
                        case 'OK':
                            $class_motivo_bloqueio_tlc = 'label label-success';
                            break;
                        case 'DESATUALIZADO':
                            $class_motivo_bloqueio_tlc = 'label label-warning';
                            break;
                        case 'PENDÊNCIA FIN.':
                            $class_motivo_bloqueio_tlc = 'label label-important';
                            break;
                        case 'INATIVO':
                        default:
                            $class_motivo_bloqueio_tlc = 'label';
                            break;
                    }

                    switch ($motivo_bloqueio_bsat) {
                        case 'OK':
                            $class_motivo_bloqueio_bsat = 'label label-success';
                            break;
                        case 'DESATUALIZADO':
                            $class_motivo_bloqueio_bsat = 'label label-warning';
                            break;
                        case 'PENDÊNCIA FIN.':
                            $class_motivo_bloqueio_bsat = 'label label-important';
                            break;
                        case 'INATIVO':
                        default:
                            $class_motivo_bloqueio_bsat = 'label';
                            break;
                    }
                ?>
				<tr>
					<td><?= $cliente[0]['codigo'] ?></td>
					<td><?= $buonny->documento($cliente[0]['codigo_documento']) ?></td>
					<td><?= $cliente[0]['razao_social'] ?></td>
					<td><?= ($cliente[0]['TLCProduto'] == 1 || $cliente[0]['TLCProduto'] == 2)?"SIM":"NÃO" ?></td>
					<td><span class="pull-right <?php echo $class_motivo_bloqueio_tlc; ?>" title="<?php echo $cliente[0]['TLCMotivo']; ?>"><?php echo $motivo_bloqueio_tlc; ?></span></td>
					<td><?= $cliente[0]['TLCPagador'] ?></td>
					<td><?= $cliente[0]['logs'] ?></td>
					<td><?= ($cliente[0]['BSATProduto'] == 82)?"SIM":"NÃO"; ?></td>
					<td><span class="pull-right <?php echo $class_motivo_bloqueio_bsat; ?>" title="<?php echo $cliente[0]['BSATMotivo']; ?>"><?php echo $motivo_bloqueio_bsat; ?></span></td>
					<td><?= $cliente[0]['BSATPagador'] ?></td>
					<td><?= $cliente[0]['itens'] ?></td>
					<td><?php echo $this->Html->link('','javascript:void(0)',array('onclick' => "javascript:manter_cliente({$cliente[0]['codigo']},'{$cliente[0]['codigo_documento']}')",'class' => 'icon-thumbs-up evt-alterar-status', 'title' => 'Manter Cliente')); ?></td>
					
				</tr>
		
			<?php endforeach ?>
		</tbody>
	</table>
</div>
<?php if (!empty($clientes_duplicados)): ?>
	<?php echo $this->Javascript->codeBlock("jQuery('table.table').tablesorter({sortList: [[1]],})"); ?>
<?php endif ?>
<?php 
echo $this->Javascript->codeBlock("
	function manter_cliente(cliente,cnpj) {
		if (confirm('Confirma o cancelamento de todos os outros clientes com o CNPJ ' + cnpj + ' com exceção do cliente ' + cliente + ' ?'))
			location.href = '/portal/clientes/eliminar_clientes_duplicados/' + cliente + '/' + cnpj ;
	}
	"); ?>