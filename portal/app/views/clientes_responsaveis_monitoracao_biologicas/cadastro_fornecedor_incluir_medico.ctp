<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');

		echo $this->Javascript->codeBlock("
    		$(document).ready(function(){
    			close_dialog();
        		atualizaFornecedorMedico();
    		});

		    function atualizaFornecedorMedico(){
        		var div = jQuery('#fornecedor-medico-lista');
        		bloquearDiv(div);
		        div.load(baseUrl + 'fornecedores_medicos/listagem/".$codigo_fornecedor."/' + Math.random());

    		}
    	");
		exit;
    }
?>
<?php echo $this->Bajax->form('Medico', array('url' => array('controller' => 'medicos','action' => 'cadastro_fornecedor_incluir_medico',$codigo_fornecedor))); ?>
<?php echo $this->element('medicos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>

<?php $this->addScript($this->Buonny->link_js('fornecedores.js')); ?>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>