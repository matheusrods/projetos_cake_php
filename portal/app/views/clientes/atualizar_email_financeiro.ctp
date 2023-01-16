<?php if ($this->Session->read('Message.flash.params.type') == MSGT_SUCCESS):
        echo $this->Javascript->codeBlock("
            close_dialog('{$this->Buonny->flash()}');
            atualizarListaEmailsFinanceiros('{$this->data['ClienteContato']['codigo_cliente']}');
        ");
    exit;
endif; ?>

<?php echo $bajax->form('ClienteContato', array('url' => array('controller' => 'clientes', 'action' => 'atualizar_email_financeiro'))); ?>
<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $this->data['ClienteContato']['codigo_cliente'])); ?>
    <?php echo $this->BForm->hidden('codigo', array('value' => $this->data['ClienteContato']['codigo'])); ?>
    <?php echo $this->BForm->hidden('codigo_tipo_retorno',array('value' => '2')); ?>
    <?php echo $this->BForm->hidden('codigo_tipo_contato',array('value' => '3')); ?>
    <?php echo $this->BForm->input('nome', array('class' => 'input-large', 'label' => 'Nome', 'value' => $this->data['ClienteContato']['nome'])); ?>
    <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'label' => 'Email', 'descricao' => $this->data['ClienteContato']['descricao'])); ?>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', '#', array('class' => 'btn closeDialog', 'onclick' => 'close_dialog();')); ?>
</div>
<?php echo $this->BForm->end(); ?>