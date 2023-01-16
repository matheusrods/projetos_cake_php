<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaInformacoesTecnicas();");
        exit;
    }else if($session->read('Message.flash.params.type') == MSGT_ERROR){
    	$session->delete('Message.flash');
        //echo $javascript->codeBlock("$this->Buonny->flash();");
    }
?>
<?php echo $bajax->form('Cliente') ?>
<?php echo $this->BForm->hidden('codigo') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('possui_conta', array('label' => 'Possui Conta', 'options' => array('S'=>'SIM','N'=>'NÃO'),'class' => 'input-small')) ?>
		<?php echo $this->BForm->input('conta_buonny', array('label' => 'Conta Buonny', 'options' => array('PRINCIPAL'=>'PRINCIPAL','SUB CONTA'=>'SUB CONTA'),'class' => 'input-medium')) ?>
		<?php echo $this->BForm->input('macro_buonny', array('label' => 'Macro Buonny', 'options' => array('S'=>'SIM','N'=>'NÃO'),'class' => 'input-small')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('analista', array('label' => 'Analista', 'empty' => 'Analista','type' => 'text','class' => 'input-medium')) ?>
		<?php echo $this->BForm->input('telefone_contato', array('label' => 'Telefone', 'empty' => 'Telefone','type' => 'text','class' => 'input-medium telefone')) ?>
	</div>
	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        
    });', false); 