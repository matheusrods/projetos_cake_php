<?php echo $this->BForm->create('Rota', array('url'=>array('controller'=>'rotas', 'action'=>'editar')));?>
<?php echo $this->element('rotas/fields'); ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>