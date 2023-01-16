<?php echo $this->BForm->create('MensageriaEsocial',array('url' => array('controller' => 'mensageria_esocial', 'action' => 'importacao_certificado',$codigo_cliente, $codigo_int_esocial_certificado), 'enctype' => 'multipart/form-data')); ?>
    <?php echo $this->element('mensageria_esocial/importacao_certificado', array('edit_mode' => true)); ?>    
<?php echo $this->BForm->end(); ?>

