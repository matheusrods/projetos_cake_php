<?php foreach($produtos as $produto): ?>
<?php 
    $codigo_cliente_produto = $produto['ClienteProduto']['codigo'];
    $produto_codigo = $produto['Produto']['codigo'];
    $produto_descricao = $produto['Produto']['descricao'];
    $descricao_motivo_bloqueio = $produto['MotivoBloqueio']['descricao'];
    $codigo_motivo_bloqueio = $produto['MotivoBloqueio']['codigo'];
?>

<div class="gerenciar_produtos_esq_produto">
    <div class="gerenciar_produtos_esq_produto_info">
        
            <?php
                $info_label = 'gerenciar_produtos_esq_produto_label_pendente';
                if($codigo_motivo_bloqueio == MotivoBloqueio::MOTIVO_OK) {
                    $info_label = 'gerenciar_produtos_esq_produto_label_ok';
                }
            ?>
        
            <a href="#excluir_produto/<?php echo "{$codigo_cliente}/{$codigo_cliente_produto}/{$produto_descricao}"?>" class="gerenciar_produtos_esq_produto_excluir">X</a>
<!--            <a href="#novo_servico/<?php echo $produto_codigo?>/<?php echo $codigo_cliente?>" class="gerenciar_produtos_esq_produto_novoservico">+ novo serviço</a>-->
            <div class="<?php echo $info_label?>">
                <?php echo $produto_descricao?>
            </div>
            <a href="#editar_status/<?php echo $codigo_cliente?>/<?php echo $codigo_cliente_produto?>" class="gerenciar_produtos_esq_produto_info_status">STATUS: <?php echo $descricao_motivo_bloqueio?> <u>(alterar)</u></a>
    </div>

    <div class="gerenciar_produtos_esq_servicos">
        <?php echo $this->element('gerenciar_clientes_produtos/servicos', array(
            'servicos' => $produto['ClienteProdutoServico2'],
            'codigo_cliente' => $codigo_cliente,
            'codigo_cliente_produto' => $codigo_cliente_produto,
            'codigo_produto' => $produto_codigo
        )); ?>
    </div>
</div>  
<?php endforeach; ?>