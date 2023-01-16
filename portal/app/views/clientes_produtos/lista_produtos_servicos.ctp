<?php
$codigo_cliente_produto_novo='';
$indice_produto = 0;
$indice_servico = 0;
$indice_detalhe = 0;
$indice_fields = 0;
?>
<table class="table cliente-produto">
    <thead>
        <tr>
            <th>Produto / Serviço</th>
            <th class="numeric">Tempo Pesquisa</th>
            <th class="numeric">Validade (meses)</th>
            <th class="acoes"></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($produtos as $produto): ?>
        <?php $indice_produto++; ?>
        <?php $class_destacar_novo_produto = $produto['ClienteProduto']['codigo'] == $codigo_cliente_produto_novo ? 'class-new' : ''; ?>
        <tr id="produto-<?php echo $indice_produto; ?>" class="expand produto root <?php echo $class_destacar_novo_produto; ?>">
            <td class="first">
                <i class="icon-chevron-right"></i>
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
                <strong>
                <?php echo $produto['Produto']['descricao']; ?>
                - Faturamento <?php echo preg_replace('/\s.*/', '', $produto['ClienteProduto']['data_faturamento']); ?>
                </strong>
                <span class="pull-right <?php echo $class_motivo_bloqueio; ?>" title="<?php echo $produto['MotivoBloqueio']['descricao']; ?>"><?php echo $motivo_bloqueio; ?></span>
            </td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php foreach ($produto['ClienteProdutoServico2'] as $servico): ?>
            <?php $indice_servico++; ?>
            <?php $indice_fields++; ?>
            <?php if (count($servico['ClienteProdutoServico2'][0]['ProfissionalTipo']) == 0): ?>
                <tr id="servico-<?php echo $indice_produto; ?>" class="produto-servico-sem-detalhe produto-<?php echo $indice_produto; ?> child-produto-<?php echo $indice_produto; ?>">
                    <td class="first"><?php echo $servico['Servico']['descricao']; ?></td>
                    <td class="numeric"><?php echo $servico['ClienteProdutoServico2'][0]['tempo_pesquisa']; ?></td>
                    <td class="numeric"><?php echo $servico['ClienteProdutoServico2'][0]['validade']; ?></td>
                    <?php if(isset($destino) && $destino == 'consulta'):?>
                    	<td></td>
  		            <?php else:?>  
                   	 	<td><?php echo $this->Html->link('', array('controller' => 'clientes_produtos_servicos', 'action' => 'atualizar_profissional_tipo', $codigo_cliente, $produto['Produto']['codigo'], $servico['Servico']['codigo'], 'todos', $servico['ClienteProdutoServico2'][0]['codigo']), array('escape' => false, 'class' => 'icon-edit evt-editar-servico', 'title' => 'Editar')); ?></td>
                   	<?php endif?> 
                </tr>
            <?php else: ?>
                <tr id="servico-<?php echo $indice_servico; ?>" class="expand produto-servico produto-<?php echo $indice_produto; ?> child-produto-<?php echo $indice_produto; ?>">
                    <td class="first">
                        <i class="icon-chevron-right"></i>
                        <?php echo $this->BForm->hidden("ClienteProdutoServic.{$indice_fields}.codigo", array('value' => $servico['ClienteProdutoServico2'][0]['codigo'])); ?>
                        <?php echo $servico['Servico']['descricao']; ?>
                    </td>
                    <td class="tempo-pesquisa col-2 numeric"><?php echo $this->BForm->input('Todos.' . $indice_detalhe . '.tempo_pesquisa', array('maxlength' => 6, 'class' => 'span1 todos servico-' . $indice_servico, 'label' => false)); ?></td>
                    <td class="validade col-3 numeric"><?php echo $this->BForm->input('Todos.' . $indice_detalhe . '.validade', array('maxlength' => 6, 'class' => 'span1 todos servico-' . $indice_servico, 'label' => false)); ?></td>
                    <?php if(isset($destino) && $destino == 'consulta'):?>
                    	<td></td>
  		            <?php else:?>  	
                    	<td><?php echo $this->Html->link('', array('controller' => 'clientes_produtos_servicos', 'action' => 'atualizar_profissional_tipo', $codigo_cliente, $produto['Produto']['codigo'], $servico['Servico']['codigo'], 'todos', $servico['ClienteProdutoServico2'][0]['codigo']), array('escape' => false, 'class' => 'icon-edit evt-editar-servico', 'title' => 'Editar')); ?></td>
                    <?php endif?>		
                </tr>
                <?php foreach ($servico['ClienteProdutoServico2'] as $k => $servico_profissional): ?>
                    <?php $indice_detalhe++; ?>
                    <?php $indice_fields++; ?>
                    <?php if ($produtos['0']['ClienteProduto']['codigo_cliente'] ==$servico_profissional['codigo_cliente_pagador']){ ?>
                    <tr class="produto-servico-detalhe produto-<?php echo $indice_produto; ?> child-servico-<?php echo $indice_servico; ?>">
                        <td class="first"><?php echo $servico_profissional['ProfissionalTipo']['descricao']; ?></td>
                        <td class="tempo-pesquisa col-2 numeric"><?php echo $servico['ClienteProdutoServico2'][$k]['tempo_pesquisa']; ?></td>
                        <td class="validade col-3 numeric"><?php echo $servico['ClienteProdutoServico2'][$k]['validade']; ?></td>
                        <?php if(isset($destino) && $destino == 'consulta'):?>
                        	<td></td>
  		                <?php else:?>   	
  		                   	<td>
  		                   		<?php echo $this->Html->link('', array('controller' => 'clientes_produtos_servicos', 'action' => 'atualizar_profissional_tipo', $codigo_cliente, $produto['Produto']['codigo'], $servico['Servico']['codigo'], $servico_profissional['ProfissionalTipo']['codigo'], $servico['ClienteProdutoServico2'][$k]['codigo']), array('escape' => false, 'class' => 'icon-edit evt-editar-servico', 'title' => 'Editar')); ?></td>
                    		</td>
                    	<?php endif?>	
                    </tr>
                  <?php } ?>  
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="form-actions">
	<?php if(isset($destino) && $destino == 'consulta'):?>
    	<?php echo $this->Html->link('Voltar', array('action' => 'consulta_index'), array('class' => 'btn')); ?>
    <?php else:?>	
    	<?php echo $this->Html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
    <?php endif?>	
</div>
<?php
echo $this->Javascript->codeBlock("
    $(document).ready(function() {
    	$('.expand').data('expanded', true);
    	atualizarIcones();
    });    
");
?>
