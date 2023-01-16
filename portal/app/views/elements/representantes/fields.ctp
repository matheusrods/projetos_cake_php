<div class='row-fluid inline'>  
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('codigo_documento', array('label' => false, 'placeHolder' => 'CPF/CNPJ', 'class' => 'input-small')); ?>
    <?php echo $this->BForm->input('nome', array('label' => false, 'placeHolder' => 'Representante', 'class' => 'input-xxlarge')); ?>
</div>
<div class='row-fluid inline'>
    <?php echo $this->BForm->input('codigo_endereco_regiao', array('label' => 'Endereço Região', 'options' => $regioes, 'empty' => 'Selecione uma região', 'class' => 'input-large')); ?>
    <?php echo $this->BForm->input('ativo', array('label' => 'Status', 'options' => array('inativo', 'ativo'), 'empty' => 'Selecione um status', 'class' => 'input-large')); ?>
    <?php echo $this->BForm->input('libera_outro_setor', array('label' => 'Liberar em outro Setor', 'options' => array('não', 'sim'), 'empty' => 'Selecione uma opção', 'class' => 'input-large')); ?>
</div>

<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>