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
		<?php echo $this->BForm->input('estado_origem', array('label' => 'UF Origem', 'options' => $estados, 'value' => $estadoOrigem['EnderecoEstado']['codigo'],'class' => 'input-small')) ?>
		<?php echo $this->BForm->input('cidade_origem', array('label' => 'Cidade de Origem', 'options' => $cidadesOrigem, 'empty' => 'Cidade de Origem', 'value' => $cidadeOrigem['EnderecoCidade']['codigo'],'class' => 'input-xlarge')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('estado_destino', array('label' => 'UF Destino', 'options' => $estados, 'value' => $estadoDestino['EnderecoEstado']['codigo'],'class' => 'input-small')) ?>
		<?php echo $this->BForm->input('cidade_destino', array('label' => 'Cidade de Destino', 'options' => $cidadesDestino, 'empty' => 'Cidade de Destino', 'value' => $cidadeDestino['EnderecoCidade']['codigo'],'class' => 'input-xlarge')) ?>
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
        $("#ClienteEstadoOrigem").change(function(){
        	buscar_cidade("#ClienteEstadoOrigem","#ClienteCidadeOrigem");
        });
		$("#ClienteEstadoDestino").change(function(){
        	buscar_cidade("#ClienteEstadoDestino","#ClienteCidadeDestino");
        });
        
    });', false);