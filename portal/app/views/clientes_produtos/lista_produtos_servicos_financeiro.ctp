<?php
$indice_produto = 0;
$indice_servico = 0;
$indice_detalhe = 0;
$indice_fields = 0;
?>
<table class="table cliente-produto">
    <thead>
        <tr>
            <th>Produto / Serviço</th>
            <th>Status</th>
            <th>Valor (R$)</th>
            <th>Cliente Pagador</th>
            <th>Tempo Pesquisa</th>
            <th>Validade (meses)</th>
            <th class="acoes">Ações</th>
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
            </td>
            <td>
                <span class="<?php echo $class_motivo_bloqueio; ?>" title="<?php echo $produto['MotivoBloqueio']['descricao']; ?>"><?php echo $motivo_bloqueio; ?></span><br>
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
            <td></td>
            <td></td>
            <td><?php echo $this->Html->link('', array('action' => 'atualizar_status', $produto['ClienteProduto']['codigo'], $codigo_cliente), array('class' => 'icon-edit evt-alterar-status', 'title' => 'Editar')); ?></td>
        </tr>
        <?php foreach ($produto['ClienteProdutoServico2'] as $kserv => $servico): ?>
            <?php $indice_servico++; ?>
            <?php $indice_fields++; ?>
            <?php if (count($servico['ClienteProdutoServico2'][0]['ProfissionalTipo']) == 0): ?>
                <tr id="servico-<?php echo $indice_produto; ?>" class="produto-servico-sem-detalhe produto-<?php echo $indice_produto; ?> child-produto-<?php echo $indice_produto; ?>">
                    <td class="first"><?php echo $servico['Servico']['descricao']; ?></td>
                    <td></td>
                    <td><?php echo $this->Buonny->moeda($servico['ClienteProdutoServico2'][0]['valor']); ?></td>
                    <td><?php echo $servico['ClienteProdutoServico2'][0]['codigo_cliente_pagador']; ?></td>
                    <td><?php echo $servico['ClienteProdutoServico2'][0]['tempo_pesquisa']; ?></td>
                    <td><?php echo $servico['ClienteProdutoServico2'][0]['validade']; ?></td>
                    <td></td>
                </tr>
            <?php else: ?>
                <tr id="servico-<?php echo $indice_servico; ?>" class="expand produto-servico produto-<?php echo $indice_produto; ?> child-produto-<?php echo $indice_produto; ?>">
                    <td class="first">
                        <i class="icon-chevron-right"></i>
                        <?php echo $this->BForm->hidden("ClienteProdutoServic.{$indice_fields}.codigo", array('value' => $servico['ClienteProdutoServico2'][0]['codigo'])); ?>
                        <?php echo $servico['Servico']['descricao']; ?>
                    </td>
                    <td></td>
                    <td class="valor col-1"><?php echo $this->BForm->input('Todos.' . $indice_detalhe . '.valor', array('maxlength' => 6, 'class' => 'span1 todos servico-' . $indice_servico, 'label' => false)); ?></td>
                    <td class="pagador col-2"><?php echo $this->BForm->input('Todos.' . $indice_detalhe . '.codigo_cliente_pagador', array('maxlength' => 6, 'class' => 'span1 todos servico-' . $indice_servico, 'label' => false)); ?></td>
                    <td class="tempo-pesquisa col-3"><?php echo $this->BForm->input('Todos.' . $indice_detalhe . '.tempo_pesquisa', array('maxlength' => 6, 'class' => 'span1 todos servico-' . $indice_servico, 'label' => false)); ?></td>
                    <td class="validade col-4"><?php echo $this->BForm->input('Todos.' . $indice_detalhe . '.validade', array('maxlength' => 6, 'class' => 'span1 todos servico-' . $indice_servico, 'label' => false)); ?></td>
                    <td></td>
                </tr>
                <?php foreach ($servico['ClienteProdutoServico2'] as $k => $servico_profissional): ?>
                    <?php $indice_detalhe++; ?>
                    <?php $indice_fields++; ?>
                    <tr class="produto-servico-detalhe produto-<?php echo $indice_produto; ?> child-servico-<?php echo $indice_servico; ?>">
                        <td class="first"><?php echo $servico_profissional['ProfissionalTipo']['descricao']; ?></td>
                        <td></td>
                        <td class="valor col-1"><?php echo $this->Buonny->moeda($produto['ClienteProdutoServico2'][$kserv]['valor']); ?></td>
                        <td class="pagador col-2"><?php echo $servico['ClienteProdutoServico2'][$k]['codigo_cliente_pagador']; ?></td>
                        <td class="tempo-pesquisa col-3"><?php echo $servico['ClienteProdutoServico2'][$k]['tempo_pesquisa']; ?></td>
                        <td class="validade col-4"><?php echo $servico['ClienteProdutoServico2'][$k]['validade']; ?></td>
                        <td></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<div class="form-actions">
    <?php echo $this->Html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>