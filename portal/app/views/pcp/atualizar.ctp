<?php echo $this->BForm->create('TIpcpInformacaoPcp', array('type' => 'post' ,'url' => array('controller' => 'pcp','action' => 'atualizar',$cliente['Cliente']['codigo'], $ipcp_codigo)));?>
<?php echo $this->element('pcp/fields'); ?>
<?php echo $this->BForm->end(); ?>