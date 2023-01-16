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
<?php echo $bajax->form('SinistroUltimoMes', array('url' => array('controller' => 'informacoes_tecnicas', 'action' => 'adicionar_sinistro_ultimo_mes', $codigo_cliente),'type' => 'post') ) ?>

<?php echo $bajax->form('SinistroUltimoMes') ?>
<?php echo $this->BForm->hidden('codigo_cliente') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('data', array('label' => 'Data','class' => 'input-small data', 'type'  => 'text')) ?>
	
		<?php echo $this->BForm->input('local', array('label' => 'Local (Cidade / UF)','class' => 'input-xlarge')) ?>
    </div>
    <div class='row-fluid inline'>
		<?php echo $this->BForm->input('mercadoria', array('label' => 'Mercadoria','class' => 'input-medium')) ?>

		<?php echo $this->BForm->input('codigo_tipo_sinistro', array('label' => 'Tipo','class' => 'input-medium', 'options' => $tipo_sinistro, 'empty' => 'Selecione')) ?>
		<?php echo $this->BForm->input('valor', array('label' => 'Valor (R$)','class' => 'input-medium moeda')) ?>
	</div>
	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker()       ;  
        setup_mascaras();
    });', false);