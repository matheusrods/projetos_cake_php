<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');

		echo $this->Javascript->codeBlock("
    		$(document).ready(function(){
    			close_dialog();
        		atualizaFornecedorHorario();
    		});

		    function atualizaFornecedorHorario(){
        		var div = jQuery('#fornecedor-horario-lista');
        		bloquearDiv(div);
        		div.load(baseUrl + 'fornecedores_horarios/listagem/".$codigo_fornecedor."/' + Math.random());
    		}
    	");
		exit;
    }
?>
<?php echo $this->Bajax->form('FornecedorHorario', array('url' => array('controller' => 'fornecedores_horarios','action' => 'incluir',$codigo_fornecedor))); ?>
<?php echo $this->element('fornecedores_horarios/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>

<?php $this->addScript($this->Buonny->link_js('fornecedores.js')); ?>
<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>