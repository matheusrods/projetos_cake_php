
	<div class='form-procurar'>	
		<div class='well'>
			<?php echo $this->BForm->create('ClienteProduto', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes_produtos', 'action' => 'assinatura_visualizar'))) ?>
			<div class="row-fluid inline">
				<?php echo $this->Buonny->input_codigo_cliente($this); ?>
			</div>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $this->BForm->end();?>
		</div>
	</div>
<?php if (isset($cliente)): ?>
	<div class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
	</div>
    <div style="margin-bottom:20px;">
        <h4>Contratos do Cliente</h4>
    </div>
	<div style="margin-bottom:20px;">
        <strong>Legenda, tipo de bloqueio:</strong>&nbsp;
        <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;Comercial&nbsp;&nbsp;
        <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;Financeira&nbsp;&nbsp;
        <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>&nbsp;Jurídica
    </div>
    <table class='table cliente-produto'>
		<thead>
			<th>Produto / Serviço</th>
			<th class='input-mini'>Status</th>
			<th class='input-mini'>Pagador</th>
            <th class='input-mini' title="Exibir Número de liberação no sistema Teleconsult">Protocolo</th>
			<th class='numeric'>Taxa Bancária</th>
			<th class='numeric'>Taxa Corretora</th>
			<th class='numeric' title="Valor do Prêmio Mínimo">R$ PM</th>
			<th class='numeric' title="Quantidade do Prêmio Mínimo">Qtd PM</th>
            <th class='numeric'>Teto Máximo</th>
            <th class='numeric'>Valor</th>
            <th class='action-icon' colspan='2'>Ações</th>
		</thead>
		<tbody>
			<?php if (count($produtos)): ?>

			<?php 
                $Produto_Toggle_anterior = null;
                $Produto_Toggle = 0;
            ?>

				<?php foreach($produtos as $produto): ?>
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
                    $motivo_bloqueio = preg_replace($pattern, $replacement, $produto['MotivoBloqueio']['descricao']);
                    switch ($motivo_bloqueio) {
                        case 'OK':
                            $class_motivo_bloqueio = 'label label-success';
                            break;
                        case 'DESATUALIZADO':
                            $class_motivo_bloqueio = 'label label-warning';
                            break;
                        case 'PENDÊNCIA FIN.':
                            $class_motivo_bloqueio = 'label label-important';
                            break;
                        case 'INATIVO':
                        default:
                            $class_motivo_bloqueio = 'label';
                            break;
                    }
                    ?>
					<tr id="<?php echo $produto['Produto']['codigo']; ?>" class="produto" style="cursor:pointer"  
								ProdutoToggle="<?=++$Produto_Toggle?>" >
						<td><i class="icon-chevron-down"></i> 

							<strong>
							<?php echo $produto['Produto']['descricao']; ?>
                			- Faturamento <?php echo preg_replace('/\s.*/', '', $produto['ClienteProduto']['data_faturamento']); ?>
						</strong></td>
						<td>
                            <span style="margin-bottom:5px;" title="<?= $produto['MotivoBloqueio']['descricao'] ?>" class="<?= $class_motivo_bloqueio ?>"><?= $motivo_bloqueio ?></span><br />
                            <?php if($produto['ClienteProduto']['pendencia_comercial']): ?>
                                <span class="badge-empty badge" title="Pendência Comercial"></span>&nbsp;
                            <?php endif; ?>

                            <?php if($produto['ClienteProduto']['pendencia_financeira']): ?>
                                <span class="badge-empty badge badge-important" title="Pendência Financeira"></span>&nbsp;&nbsp;
                            <?php endif; ?>

                            <?php if($produto['ClienteProduto']['pendencia_juridica']): ?>
                                <span class="badge-empty badge badge-warning" title="Pendência Jurídica"></span>
                            <?php endif; ?>
                        </td>
						<td></td>
                        <td></td>
						<td class='numeric'><?= $this->Buonny->moeda($produto['ClienteProduto']['valor_taxa_bancaria'], array('nozero' => true)) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($produto['ClienteProduto']['valor_taxa_corretora'], array('nozero' => true)) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($produto['ClienteProduto']['valor_premio_minimo'], array('nozero' => true)) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($produto['ClienteProduto']['qtd_premio_minimo'], array('nozero' => true, 'places' => 0)) ?></td>
						<td class='numeric'></td>
                        <td class='numeric'></td>
                        <td class='action-icon'><?php echo $this->Html->link('', array('controller' => 'clientes_produtos', 'action' => 'editar', $produto['ClienteProduto']['codigo'], $cliente['Cliente']['codigo'], 1), array('class' => 'icon-edit evt-editar-cliente-produto', 'title' => 'Editar')); ?></td>					
                    </tr>
					<?php foreach($produto['ClienteProdutoServico2'] as $servico): ?>
						<tr class="ProdutoToggle-<?=$Produto_Toggle?> produto-servico-detalhe">
							<td style='padding-left:27px' class='first'><?= $servico['Servico']['descricao'] ?></td>
							<td></td>
							<td><?= $servico['codigo_cliente_pagador'] ?></td>
                            <td><?= $servico['Servico']['codigo'] == 3 ? ($servico['consulta_embarcador'] ? 'sim': 'não'):'' ?>
							<td></td>
							<td></td>
							<td class='numeric'><?= $this->Buonny->moeda($servico['valor_premio_minimo'], array('nozero' => true)) ?></td>
							<td class='numeric'><?= $this->Buonny->moeda($servico['qtd_premio_minimo'], array('nozero' => true, 'places' => 0)) ?></td>
                            <td class='numeric'><?= $this->Buonny->moeda($servico['valor_maximo'], array('nozero' => true)) ?></td>
                            <td class='numeric'><?= $this->Buonny->moeda($servico['valor']) ?></td>
                            <td></td>
						</tr>
					<?php endforeach ?>

					<?php $Produto_Toggle_anterior = $produto['Produto']['codigo'] ?>
				<?php endforeach ?>
			<?php endif ?>


		</tbody>
	</table>
	<div class="form-actions">
    	
	</div>

	<?php echo $this->Javascript->codeBlock("
        $(function() {
            $('tr a').click(function(){
                window.location = $(this).attr('href');
                return false;
            });

            $('tr').click(function(){
                $('.ProdutoToggle-'+$(this).attr('ProdutoToggle')).toggle();
                
                if($(this).find('i.icon-chevron-down').length > 0){
                    $(this).find('i').addClass('icon-chevron-right');
                    $(this).find('i').removeClass('icon-chevron-down');
                }else{
                    $(this).find('i').addClass('icon-chevron-down');
                    $(this).find('i').removeClass('icon-chevron-right');
                }

                return false;
            });
		});

    ");	 ?>
<?php endif ?>