<div class="row-fluid">
	<?php echo $this->BForm->input('codigo_cliente_produto', array('type' => 'hidden')); ?>
	<?php echo $this->BForm->input('Produto.codigo', array('type' => 'hidden')); ?>
	<?php echo $this->BForm->input('Outros.possui_tipos_profissionais', array('type' => 'hidden'));?>
    <div>
        <strong>Produto..:</strong>
        <?php echo $this->data['Produto']['descricao'];?>
    </div>

    <div>
        <strong>Serviço..:</strong>
        <?php echo $this->BForm->input('codigo_servico', array('label' => false, 'empty' => 'Serviço', 'options' => $servicos)); ?>
    </div>
</div>

<div class="row-fluid inline">
    <?php echo $this->BForm->input('valor', array('placeholder' => 'Valor', 'type' => 'text', 'label' => 'Valor (R$)', 'class' => 'moeda input-medium', 'maxlength' => 11)); ?>
    <?php echo $this->BForm->input('validade', array('placeholder' => 'Validade', 'label' => 'Validade (meses)', 'class' => 'numero input-medium', 'maxlength' => 2)); ?>
    <?php echo $this->BForm->input('tempo_pesquisa', array('placeholder' => 'Tempo de pesquisa', 'label' => 'Pesquisa (minutos)', 'class' => 'numero input-medium', 'maxlength' => 3)); ?>
    <?php echo $this->BForm->input('codigo_cliente_pagador', array('placeholder' => 'Cód. Cliente Pagador', 'label' => 'Cód. Cliente Pagador', 'class' => 'numero input-medium', 'maxlength' => 10)); ?>
</div>

<div class="row-fluid inline">
    <?php echo $this->BForm->input('consistencia_motorista', array('label' => 'Consistência Motorista', 'options' => array('0' => 'Não', '1' => 'Sim'))); ?>
    <?php echo $this->BForm->input('consulta_embarcador', array('label' => 'Consulta Embarcador', 'options' => array('0' => 'Não', '1' => 'Sim'))); ?>
</div>
<?php echo $this->Javascript->codeBlock("setup_mascaras();"); ?>