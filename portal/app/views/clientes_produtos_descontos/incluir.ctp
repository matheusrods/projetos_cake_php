<div class="formulario">
    <?php echo $this->BForm->create('ClienteProdutoDesconto', array('url' => array('controller' => 'clientes_produtos_descontos', 'action' => 'incluir'))); ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente)); ?>
            <?php echo $this->BForm->input('ano', array('type' => 'text', 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
            <?php echo $this->BForm->input('mes', array('options' => $meses, 'class' => 'input-small', 'label' => false, 'default' => date('m'))); ?>
            
            <?php echo $this->BForm->input('codigo_produto', array('class' => 'input-large', 'options' => $produtos, 'label' => false, 'empty' => 'Produtos')); ?>
            <?php echo $this->BForm->input('valor', array('class' => 'input-mini moeda', 'label' => false, 'placeholder' => 'Valor', 'type' => 'text')); ?>
        </div>
        <div class="row-fluid">
            <?php echo $this->BForm->input('observacao', array('class' => 'input-xxlarge', 'label' => 'Observação', 'type' => 'textarea')); ?>
        </div>
        <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        </div>    
        <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Javascript->codeBlock('jQuery(document).ready(function(){setup_mascaras();});')); ?>