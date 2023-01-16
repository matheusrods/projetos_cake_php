

<?php 

if (isset($this->params['pass'][1]) && $this->params['pass'][1] == "consulta_agendada") {
    echo $this->BForm->create('FichaPsicossocial', array('url' => array('controller' => 'ficha_psicossocial','action' => 'incluir', $dados['PedidoExame']['codigo'], $this->params['pass'][1])));
} else {
    echo $this->BForm->create('FichaPsicossocial', array('url' => array('controller' => 'ficha_psicossocial','action' => 'incluir', $dados['PedidoExame']['codigo'])));
}
 ?>
<?php echo $this->element('ficha_psicossocial/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>

