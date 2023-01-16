<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaInformacoesTecnicas();");
        exit;
    }
?>
<?php echo $bajax->form('Cliente') ?>
<?php echo $this->BForm->hidden('codigo') ?>
<?php echo $this->BForm->hidden('codigo_cliente') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('descricao', array('label' => 'Descricao', 'empty' => 'Descricao da Mercadoria','type' => 'text','class' => 'input-large')) ?>
		<?php echo $this->BForm->input('representativo', array('label' => 'Representativo %', 'empty' => 'Representativo %','type' => 'text','class' => 'input-small')) ?>
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