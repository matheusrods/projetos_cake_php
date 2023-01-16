<?php
if ($this->Session->read('Message.flash.params.type') == MSGT_SUCCESS):
	$this->Session->delete('Message.flash');
    echo $javascript->codeBlock("close_dialog();atualizaListaClientesProdutos('gerenciar', " . $codigo_cliente . ");");
    exit;
endif;
?>

<?php echo $bajax->form('ClienteProduto', array('url' => array('controller' => 'ClientesProdutos', 'action' => 'incluir', $codigo_cliente))); ?>
<?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => $codigo_cliente)) ?>
        
<div class="fullwide">
    <?php echo $this->BForm->input('codigo_produto', array(
        'label' => 'Escolha o produto',
        'empty' => 'Selecione um produto', 
        'options' => $produtos,
        'class' => 'codigo_produto'
    )); ?>
</div>

<div class="form-actions submit_box">
    <input type="submit" value="Salvar" class="btn btn-primary" />
    <?php echo $html->link('Voltar', '#', array('class' => 'btn', 'onclick' => 'close_dialog();')); ?>
</div>


<?php echo $this->BForm->end() ?>