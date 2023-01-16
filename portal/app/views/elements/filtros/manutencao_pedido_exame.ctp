
<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Importar', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ImportacaoPedidosExame', 'element_name' => 'manutencao_pedido_exame', 'codigo_cliente' => $this->data['Cliente']['codigo']), 'divupdate' => '.form-procurar')) ?>
	       
            <div class="row-fluid inline">                
                <?php echo $this->Buonny->input_codigo_cliente($this); ?>
                <?php echo $this->BForm->input('cpf', array('label' => false, 'placeholder' => 'CPF', 'class' => 'input-medium cpf')); ?>
            </div>
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>	        
	    <?php echo $this->BForm->end() ?>
	</div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        
        atualizaListaManutencao();
        function atualizaListaManutencao() {
          var codigo_cliente = $("#ImportarCodigoCliente").val();
          var cpf = $("#ImportarCpf").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "importar/manutencao_pedido_exame_listagem/" + codigo_cliente + "/"+cpf+"/"+ Math.random());
        }
    });', false);
?>
