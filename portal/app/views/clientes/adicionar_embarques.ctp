<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaInformacoesTecnicas();");
        exit;
    }
?>
<?php echo $bajax->form('Cliente') ?>
<?php echo $this->BForm->hidden('codigo') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('estado_origem', array('label' => 'UF Origem', 'options' => $estados,'class' => 'input-small')) ?>
		<?php echo $this->BForm->input('cidade_origem', array('label' => 'Cidade de Origem', 'options' => '', 'empty' => 'Cidade de Origem','class' => 'input-xlarge')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('estado_destino', array('label' => 'UF Destino', 'options' => $estados,'class' => 'input-small')) ?>
		<?php echo $this->BForm->input('cidade_destino', array('label' => 'Cidade de Destino', 'options' => '', 'empty' => 'Cidade de Destino','class' => 'input-xlarge')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('percentual', array('label' => 'Percentual', 'empty' => 'Percentual','type' => 'text','class' => 'input-small')) ?>
	</div>
	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        buscar_cidade("#ClienteEstadoOrigem","#ClienteCidadeOrigem");
        buscar_cidade("#ClienteEstadoDestino","#ClienteCidadeDestino");
        $("#ClienteEstadoOrigem").change(function(){
        	buscar_cidade("#ClienteEstadoOrigem","#ClienteCidadeOrigem");
        });
		$("#ClienteEstadoDestino").change(function(){
        	buscar_cidade("#ClienteEstadoDestino","#ClienteCidadeDestino");
        });
        
    });', false);