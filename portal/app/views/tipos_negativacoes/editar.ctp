<?php echo $this->BForm->create('TipoNegativacao', array('url' => array('controller' => 'tipos_negativacoes', 'action' => 'editar')));?>
<?php echo $this->Form->hidden('codigo'); ?>
    <div class='row-fluid inline parent'>        
        <?php echo $this->BForm->input('descricao',array('class' => 'input-xlarge', 'label' => 'Descrição' )) ?>
    </div>
    <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
          <?php echo $html->link('Voltar',array('controller' => 'tipos_negativacoes', 'action' => 'index'), array('class' => 'btn')) ;?>
    </div>
<?php echo $this->BForm->end() ?>
