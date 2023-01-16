<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaListaConfiguracaoTecnologia('{$this->passedArgs[0]}');");
        exit;
    }else if($session->read('Message.flash.params.type') == MSGT_ERROR){
    	$session->delete('Message.flash');
    }
?>
<?php echo $this->Bajax->form('TPjtePjurTecn',array('url' => array('controller' => 'clientes_tecnologias_sm', 'action' => 'incluir', $this->passedArgs[0])) ) ?>
<div class="row-fluid">
	<?php echo $this->BForm->input('pjte_tecn_codigo', array('label' => 'Tecnologia','empty' => 'Selecione uma tecnologia' ,'options' => $tecnologias,'class' => 'input-xxlarge')); ?>
</div>
<div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
      <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>