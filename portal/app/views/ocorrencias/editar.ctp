<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("window.opener='x';window.close()");
        exit;
    }
?>
<?php echo $this->BForm->create('Ocorrencia', array('action' => 'editar', $this->data['Ocorrencia']['codigo_sm'])); ?>
<?php echo $this->element('ocorrencias/fields'); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();});'); ?>