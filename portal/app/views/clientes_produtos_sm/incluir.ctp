<?php	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaListaConfiguracaoTipoProduto('{$this->passedArgs[0]}');");
        exit;
    }else if($session->read('Message.flash.params.type') == MSGT_ERROR){
    	$session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('TPjprPjurProd',array('url' => array('controller' => 'clientes_produtos_sm', 'action' => 'incluir', $this->passedArgs[0]),'type' => 'post') ) ?>

<div class="row-fluid">
	<?php echo $this->BForm->input('pjpr_prod_codigo', array('label' => 'Tipo produto','empty' => 'Selecione um produto' ,'options' => $produtos,'class' => 'input-xxlarge')); ?>
</div>
<div class='row-fluid inline' id="checkboxes">     
      <span class="label label-info">Tipo profissional</span>
      <span class='pull-right'>
        <?= $html->link('Desmarcar todas', 'javascript:void(0)', array('onclick' => 'desmarcarTodos("checkboxes")')) ?>
        <?= $html->link('Marcar todas', 'javascript:void(0)', array('onclick' => 'marcarTodos("checkboxes")')) ?>
      </span>
      <?php echo $this->BForm->input('tipo_profissional', array('label'=>false, 'options'=> $profissionais_tipos, 'multiple'=>'checkbox', 'class' => 'checkbox inline input-xlarge')); ?>
  </div>
<div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
      <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>