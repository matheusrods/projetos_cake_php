<div class="well" id="filtros">       
    <?php echo $bajax->form('ClienteValidador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteValidador', 'element_name' => 'configuracao_cliente_validador'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('clientes/fields_filtros_cliente_validador') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
     jQuery(document).ready(function(){

        atualizaLista();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteValidador/element_name:configuracao_cliente_validador/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/lista_cliente_validadores/" + Math.random());
        }   
    });', false);
?>