<?php echo $this->element('grupos_economicos/matriz_unidade'); ?>
<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('Funcionario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Funcionario', 'element_name' => 'laudo_pcd', 'codigo_cliente' => $this->data['Unidade']['codigo']), 'divupdate' => '.form-procurar')) ?>
            <?php echo $this->BForm->hidden('Funcionario.codigo_cliente', array('value' => $this->data['Unidade']['codigo'])); ?>
            <?php echo $this->element('funcionarios/fields_filtros') ?>
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
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Funcionario/element_name:laudo_pcd/codigo_cliente:" + codigo_cliente + "/" + Math.random())
        });
        
        function atualizaLista() {
          var codigo_cliente = $("#FuncionarioCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "funcionarios/listagem_laudo_pcd/" + codigo_cliente + "/" + Math.random());
        }
        
    });', false);
?>