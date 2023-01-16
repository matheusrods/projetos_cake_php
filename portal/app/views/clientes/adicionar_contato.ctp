<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaInformacoesTecnicas();");
        exit;
    }
?>
<?php echo $bajax->form('Cliente') ?>
<?php echo $this->BForm->hidden('CON_Codigo') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('CON_Nome', array('label' => 'Nome', 'empty' => 'Nome do Contato','type' => 'text','class' => 'input-xlarge')) ?>
		<?php echo $this->BForm->input('CON_Cargo', array('label' => 'Cargo', 'empty' => 'Cargo do Contato','type' => 'text','class' => 'input-medium')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('CON_Filial', array('label' => 'Filial', 'empty' => 'Filial do Contato','type' => 'text','class' => 'input-xxlarge')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('CON_Telefone', array('label' => 'Tel. Comercial', 'empty' => 'Telefone do Contato','type' => 'text','class' => 'input-small telefone')) ?>
		<?php echo $this->BForm->input('CON_Celular', array('label' => 'Tel. Celular', 'empty' => 'Celular do Contato','type' => 'text','class' => 'input-small telefone')) ?>
		<?php echo $this->BForm->input('CON_EMail', array('label' => 'E-mail', 'empty' => 'E-mail do Contato','type' => 'text','class' => 'input-xlarge')) ?>
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
?>