<?php echo $this->BForm->hidden('codigo'); ?>
<?php echo $this->BForm->input('descricao', array('label' => false, 'placeHolder' => 'Descrição do modelo de contrato', 'class' => 'input-xxlarge')); ?>
<?php echo $this->BForm->input('modelo', array('label' => 'Texto Padrão do Contrato', 'type'  => 'textarea', 'class' => 'input-xxlarge')); ?>
<div class = "">
    <h5>Utilize as seguintes tags para os campos do contrato:</h5>
    <ul class="unstyled">
        <li>##razao_social## para Razão Social</li>
        <li>##endereco_tipo## para Tipo de Endereço</li>
        <li>##endereco_logradouro## para o logradouro</li>
        <li>##numero## para o número do endereço</li>
        <li>##endereco_bairro## para o bairro</li>
        <li>##endereco_cidade## para a cidade</li>
        <li>##endereco_estado## para o estado</li>
        <li>##cnpj## para o CNPJ</li>
        <li>##data_contrato## para a data do contrato</li>
    </ul>
</div>
<div class = "form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
