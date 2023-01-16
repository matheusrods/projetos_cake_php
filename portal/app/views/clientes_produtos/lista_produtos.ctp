<?php foreach ($produtos as $key_produto => $produto): ?>
    <div class="produto">
        <div class="titulo-produto">
            <label><?= $produto['Produto']['descricao']?></label>
            <label>Faturamento <?= substr($produto['ClienteProduto']['data_faturamento'],0,10)?></label>
        </div>
        <ul>
        <table>
            <thead>
                <th><label class="servico">Servico</label><span> / </span><label>Tipo Profissional</label><?= $html->link('detalhar', 'javascript:void(0)', array('onclick' => "mostra_detalhe(this, $key_produto)")) ?></th>
                <th class="numeric">Valor</th>
                <th class="numeric">Pagador</th>
                <th class="numeric">Tempo Pesquisa</th>
                <th class="numeric">Validade</th>
            </thead>
            <?php foreach ($produto['ClienteProdutoServico2'] as $key_servico => $servico): ?>
            <tr id=<?= 'servico'.$key_produto.$key_servico ?>>
                <td>
                    <label class="servico"><?= $servico['Servico']['descricao']?></label>
                </td>
                <?php if (count($servico['ClienteProdutoServico2'][0]['ProfissionalTipo']) == 0): ?>
                    <td class="numeric">
                        <?= $buonny->moeda($servico['Servico']['valor'])?>
                    </td>
                    <td class="numeric">
                        <?= $servico['ClienteProdutoServico2'][0]['codigo_cliente_pagador'] ?>
                    </td>
                    <td class="numeric">
                        <?= $servico['ClienteProdutoServico2'][0]['tempo_pesquisa'] ?>
                    </td>
                    <td class="numeric">
                        <?= $servico['ClienteProdutoServico2'][0]['validade'] ?>
                    </td>
                <?php else: ?>
                    <td id="valor" class="numeric"></td>
                    <td id="codigo_cliente_pagador" class="numeric"></td>
                    <td id="tempo_pesquisa" class="numeric"></td>
                    <td id="validade" class="numeric"></td>
                    <?php $tudo_igual = true; ?> 
                    <?php $ultimo_valor = $servico['ClienteProdutoServico2'][0]['valor']; ?>
                    <?php $ultimo_codigo_cliente_pagador = $servico['ClienteProdutoServico2'][0]['codigo_cliente_pagador']; ?>
                    <?php $ultimo_tempo_pesquisa = $servico['ClienteProdutoServico2'][0]['tempo_pesquisa']; ?>
                    <?php $ultima_validade = $servico['ClienteProdutoServico2'][0]['validade']; ?>
                    <?php foreach ($servico['ClienteProdutoServico2'] as $profissional): ?>
                        <?php if ($tudo_igual && ($profissional['valor'] != $ultimo_valor || $profissional['codigo_cliente_pagador'] != $ultimo_codigo_cliente_pagador || $profissional['tempo_pesquisa'] != $ultimo_tempo_pesquisa || $profissional['validade'] != $ultima_validade) ) $tudo_igual = false; ?>
                        <tr class="<?= 'detalhe'.$key_produto?>" style='display:none'>
                            <td>
                                <label><?= isset($profissional['ProfissionalTipo']['descricao']) ? $profissional['ProfissionalTipo']['descricao'] : '' ?></label>
                            </td>
                            <td class="numeric">
                                <?= $buonny->moeda($profissional['valor']) ?>
                            </td>
                            <td class="numeric">
                                <?= $profissional['codigo_cliente_pagador'] ?>
                            </td>
                            <td class="numeric">
                                <?= $profissional['tempo_pesquisa'] ?>
                            </td>
                            <td class="numeric">
                                <?= $profissional['validade'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$tudo_igual): ?>
                        <?php $aviso = '<label title="Existem valores diferenciados. Clique em detalhar">-</label>' ?>
                        <?php $ultimo_valor = $aviso ?>
                        <?php $ultimo_codigo_cliente_pagador = $aviso ?>
                        <?php $ultimo_tempo_pesquisa = $aviso ?>
                        <?php $ultima_validade = $aviso ?>
                    <?php else: ?>
                        <?php $ultimo_valor = $buonny->moeda($ultimo_valor) ?>
                    <?php endif ?>
                    <?= $javascript->codeBlock("jQuery('#servico".$key_produto.$key_servico." #valor').html('{$ultimo_valor}')") ?>
                    <?= $javascript->codeBlock("jQuery('#servico".$key_produto.$key_servico." #codigo_cliente_pagador').html('{$ultimo_codigo_cliente_pagador}')") ?>
                    <?= $javascript->codeBlock("jQuery('#servico".$key_produto.$key_servico." #tempo_pesquisa').html('{$ultimo_tempo_pesquisa}')") ?>
                    <?= $javascript->codeBlock("jQuery('#servico".$key_produto.$key_servico." #validade').html('{$ultima_validade}')") ?>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endforeach; ?>