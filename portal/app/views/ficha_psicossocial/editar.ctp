<?php 

    if (isset($this->params['pass'][2]) && $this->params['pass'][2] == "consulta_agendada") {
        echo $this->BForm->create('FichaPsicossocial', array('url' => array('controller' => 'ficha_psicossocial', 'action' => 'editar',$codigo_pedido_exame, $codigo_ficha_psicossocial,$this->params['pass'][2]), 'type' => 'post'));
    } else {
        echo $this->BForm->create('FichaPsicossocial', array('url' => array('controller' => 'ficha_psicossocial', 'action' => 'editar',$codigo_pedido_exame, $codigo_ficha_psicossocial), 'type' => 'post')); 
    }

?>

<?php echo $this->element('ficha_psicossocial/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>