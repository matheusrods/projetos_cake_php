<div class="gerenciar_produtos_dir_cabecalho">
    <h1>Categorias</h1>
    <p><?php echo $nome_servico?></p>
</div>
        
<?php if ($tem_profissionais): ?>
    <?php
        echo $this->element('gerenciar_clientes_produtos/profissionais', array(
            'profissionais' => $servico_profissionais,
            'cliente_pagador' => $codigo_cliente,
        ));
    ?>
<?php else: ?>
    <?php echo $this->BForm->create('ClienteProdutoServico', array('url' => array('action' => 'atualizar_servico', 'method' => 'put'))); ?>
    <?php echo $this->BForm->input('codigo', array('type' => 'hidden')); ?>
    <?php echo $this->BForm->input('Outros.codigo_cliente', array('type' => 'hidden', 'value' => $codigo_cliente)); ?>
    <div class="fullwide submit_box">
        <?php echo $this->BForm->input('valor', array('label' => 'Valor:', 'type' => 'text', 'class' => 'moeda numeric text-small', 'value' => $buonny->moeda($this->data['ClienteProdutoServico']['valor'], array('edit' => true)))); ?>
        <?php echo $this->BForm->input('codigo_cliente_pagador', array('label' => 'Cliente Pagador:', 'type' => 'text', 'class' => 'text-small')); ?>
    </div>
    <div class="fullwide submit_box">
        <?php echo $this->BForm->end('salvar'); ?>
    </div>
<?php endif; ?>
<?php echo $javascript->codeblock('setup_mascaras();'); ?>