<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');

		echo $this->Javascript->codeBlock("
    		$(document).ready(function(){
    			close_dialog();
        		atualizaFornecedorContato();
    		});

		    function atualizaFornecedorContato(){
        		var div = jQuery('#fornecedor-contato-lista');
        		bloquearDiv(div);
        		div.load(baseUrl + 'fornecedores_contatos/contatos_por_fornecedores/".$codigo_fornecedor."/' + Math.random());
    		}
    	");
		exit;
    }
?>

<?php echo $this->Bajax->form('FornecedorContato',array('url' => array('controller'=>'fornecedores_contatos','action' => 'editar', $codigo_fornecedor, $codigo))) ?>
<?php echo $this->element('fornecedores_contatos/fields'); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php  echo $this->BForm->end() ?>
<?php $this->addScript($this->Buonny->link_js('fornecedores.js')); ?>