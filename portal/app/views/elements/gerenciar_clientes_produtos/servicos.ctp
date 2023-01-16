<ul>
    
    <?php foreach($servicos as $servico): ?>
    <?php
        $codigo_servico = $servico['Servico']['codigo'];
        $nome_servico = $servico['Servico']['descricao'];
        $hash_detalhes = "{$codigo_cliente}/{$codigo_produto}/{$codigo_servico}";
        $hash_excluir = "{$codigo_cliente_produto}/{$codigo_servico}/{$codigo_cliente}/{$codigo_produto}/{$nome_servico}";
    ?>
    <li>
        <div class="gerenciar_produtos_esq_produto_servicos_row">
            <a href="#excluir_servico/<?php echo $hash_excluir?>" class="gerenciar_produtos_esq_servicos_excluir">X</a>
            <a href="#detalhe_servico/<?php echo $hash_detalhes?>" class="gerenciar_produtos_esq_servicos_detalhes">&gt;&gt;</a>                    
            <a href="#detalhe_servico/<?php echo $hash_detalhes?>" class="gerenciar_produtos_esq_produto_servicos_desc">
                <?php echo $nome_servico; ?>
            </a>
        </div>
    </li>
    <?php endforeach; ?>
</ul>