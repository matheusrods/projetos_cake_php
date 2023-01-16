<table class="gerenciar_produtos_dir_profissionais">
    <tr>
        <th>Editar</th>
        <th>Tipo Profissional</th>
        <th>Valor</th>
        <th>Validade</th>
        <th>Pesquisa</th>
        <th>Pagador</th>
        <th>C.E</th>
        <th>C.M</th>
    </tr>
    <?php foreach($profissionais as $profissional): ?>
    
    <?php
        $codigo_cliente_produto_servico = $profissional['ClienteProdutoServico']['codigo'];
        $profissional_codigo = $profissional['ProfissionalTipo']['codigo'];
        $profissional_nome = $profissional['ProfissionalTipo']['descricao'];
        $profissional_valor = $profissional['ClienteProdutoServico']['valor'];
        $profissional_validade = $profissional['ClienteProdutoServico']['validade'];
        $profissional_pesquisa = $profissional['ClienteProdutoServico']['tempo_pesquisa'];
        $profissional_consulta_embarcador = $profissional['ClienteProdutoServico']['consulta_embarcador'];
        $profissional_consistencia_motorista = $profissional['ClienteProdutoServico']['consistencia_motorista'];
    ?>
    <tr>
        <td>
            <?php
                $params = "{$codigo_produto}/{$codigo_servico}/{$profissional_codigo}/{$codigo_cliente_produto_servico}/{$codigo_cliente}";
            ?>
            <a class="gerenciar_produtos_dir_profissionais_editar" href="#editar_profissional_tipo/<?php echo $params?>">editar</a>
        </td>
        <td><?php echo $profissional_nome?></td>
        <td><?php echo $buonny->moeda($profissional_valor)?></td>
        <td><?php echo $profissional_validade?></td>
        <td><?php echo $profissional_pesquisa?></td>
        <td><?php echo $cliente_pagador?></td>
        <td><?php echo ($profissional_consulta_embarcador) ? "Sim" : "Não" ?></td>
        <td><?php echo ($profissional_consistencia_motorista) ? "Sim" : "Não" ?></td>
    </tr>  
    <?php endforeach; ?>
</table>