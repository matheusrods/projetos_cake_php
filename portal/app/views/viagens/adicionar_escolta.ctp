<?php echo $bajax->form('TVescViagemEscolta',array('url' => array('controller' => 'viagens', 'action' => 'adicionar_escolta', $this->data['TViagViagem']['viag_codigo']),'type' => 'post') ) ?>
<?php echo $this->element('viagens/fields_escolta'); ?>
<?php echo $this->BForm->end(); ?>