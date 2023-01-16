<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaInformacoesTecnicas();");
        exit;
    }
    if($session->read('Message.flash.params.type') == MSGT_ERROR){        
        echo '<div class="'.MSGT_ERROR.'">'.$session->read('Message.flash.message').'</div>';
        $session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('ValorEmbarque', array('url' => array('controller' => 'informacoes_tecnicas', 'action' => 'adicionar_valor_embarque', $codigo_cliente),'type' => 'post') ) ?>

<?php echo $bajax->form('ValorEmbarque') ?>
<?php echo $this->BForm->hidden('codigo_cliente') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('minimo', array('label' => 'Mínimo','class' => 'input-medium moeda', 'maxlength' => 15)) ?>
	
		<?php echo $this->BForm->input('medio', array('label' => 'Médio','class' => 'input-medium moeda', 'maxlength' => 15)) ?>
	
		<?php echo $this->BForm->input('maximo', array('label' => 'Máximo','class' => 'input-medium moeda', 'maxlength' => 15)) ?>
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