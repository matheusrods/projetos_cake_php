<?php
    $message = $this->Buonny->flash();
    $session->delete('Message.flash');    
    if (!empty($message)) {
        echo "<div class='message'>". $message ."</div>";
        echo $this->Javascript->codeBlock("jQuery('div.message').delay(4000).animate({opacity:0,height:0,margin:0}, function(){ jQuery(this).slideUp() })");
    }    
?>
<?php if (isset($clientes) && count($clientes)>0): ?>
    <?php 
        echo $paginator->options(array('update' => 'div.lista')); 
        $total_paginas = $this->Paginator->numbers();
    ?>
    <div class='actionbar-right'>
        <?php echo $html->link('<img src="/portal/img/icone_excel.gif"/> Exportar', array('controller' => 'clientes', 'action' => 'listagem_clientes_cadastrados', 'exportar'), array('escape' => false, 'class' => 'btn btn-filtro')); ?>
    </div>
    <div class="text-group">
        <table class="table table-condensed table-striped">
            <tbody>
                <?php foreach ($clientes as $cliente): ?>
                    <tr>
                        <td>
                            <div class='row-fluid'>
                                <?= $this->Html->tag('label', 'Cliente:', array('class' => 'title')) ?><?= $this->Html->tag('label', $cliente['Cliente']['codigo']) ?>
                                <?= $this->Html->tag('label', 'CPF/CNPJ:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Cliente']['codigo_documento']) ?>
                                <?= $this->Html->tag('label', 'Razão Social:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Cliente']['razao_social']) ?>
                            </div>
                            <div class='row-fluid'>
                                <?= $this->Html->tag('label', 'Nome Fantasia:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Cliente']['nome_fantasia']) ?>
                                <?= $this->Html->tag('label', 'Seguradora:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Seguradora']['nome']) ?>
                                <?= $this->Html->tag('label', 'Corretora:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Corretora']['nome']) ?>
                            </div>
                            <div class='row-fluid'>
                                <?= $this->Html->tag('label', 'Pagador:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['ClienteProdutoServico2']['codigo_cliente_pagador']) ?>
                                <?= $this->Html->tag('label', 'Produto:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Produto']['descricao']) ?>
                                <?= $this->Html->tag('label', 'Serviço:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Servico']['descricao']) ?>
                                <?= $this->Html->tag('label', 'Categoria:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['ProfissionalTipo']['descricao']) ?>
                                <?= $this->Html->tag('label', 'Valor:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['ClienteProdutoServico2']['valor']) ?>
                            </div>
                            <div class='row-fluid'>
                                <?= $this->Html->tag('label', 'Motivo Bloqueio:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['MotivoBloqueio']['descricao']) ?>
                                <?= $this->Html->tag('label', 'Status:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Cliente']['ativo'] == 1 ? 'ativo': 'inativo') ?>
                                <?= $this->Html->tag('label', 'Faturamento:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['Cliente']['regiao_tipo_faturamento'] == 1 ? 'total': 'parcial') ?>
                                <?= $this->Html->tag('label', 'Região:', array('class' => 'title')) ?> <?= $this->Html->tag('label', $cliente['EnderecoRegiao']['descricao']) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>        
            </tbody>
        </table>
    </div>
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
<?php endif ?>