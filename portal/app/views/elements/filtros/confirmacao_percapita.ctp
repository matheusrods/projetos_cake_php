<div class='well'>
  	<div id='filtros'>
	  	<?php echo $bajax->form('DisparoLink', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'DisparoLink', 'element_name' => 'confirmacao_percapita'), 'divupdate' => '.form-procurar')) ?>
			
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this); ?>

                <?php echo $this->BForm->input('mes_confirmacao', array('options' => $mes_confirmacao, 'class' => 'input-medium', 'label' => false)); ?>
                <?php echo $this->BForm->input('ano_confirmacao', array('label' => false, 'placeholder' => 'Ano','class' => 'input-mini numeric just-number', 'title' => 'Ano de Faturamento')) ?>

                <?php echo $this->BForm->input('status_confirmacao', array('options' => $status, 'class' => 'input-medium', 'label' => false)); ?>
            </div>
	        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
	        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	    <?php echo $this->BForm->end() ?>
	</div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
          var codigo_cliente = $("#FuncionarioCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:DisparoLink/element_name:confirmacao_percapita/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "funcionarios/listagem_confirmacao_percapita/" + Math.random());
        }
        
    });', false);
?>
