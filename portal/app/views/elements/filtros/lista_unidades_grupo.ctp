<div class="well">
    Código: <b><?= $CodigoCliente ?></b> | Cliente: <b><?= $NomeCliente ?></b>
</div>
<div class="well" id="filtros">       
    <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'lista_unidades_grupo', 'cliente_principal' => $cliente_principal, 'referencia' => $referencia, 'referencia_modulo' => $referencia_modulo, 'terceiros_implantacao' => $terceiros_implantacao), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('clientes/fields_filtros_unidades_clientes') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
     jQuery(document).ready(function(){
        
        atualizaLista();

        jQuery("#limpar-filtro").click(function(){
            
            var codigo_cliente = $("#ClienteCodigoCliente").val();
            var referencia = $("#ClienteReferencia").val();
            var referencia_modulo = $("#ClienteReferenciaModulo").val();

            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Cliente/element_name:lista_unidades_grupo/cliente_principal:" + codigo_cliente + "/referencia:" + referencia + "/referencia_modulo:" + referencia_modulo + "/" + "/terceiros_implantacao:" + "'.$terceiros_implantacao.'")
        });
        
        function atualizaLista() {
            
            var codigo_cliente = $("#ClienteCodigoCliente").val();
            var referencia = $("#ClienteReferencia").val();
            var referencia_modulo = $("#ClienteReferenciaModulo").val();

            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes/lista_grupo/" + codigo_cliente + "/" + referencia + "/" + referencia_modulo + "/" + "'.$terceiros_implantacao.'");
        }    
    });', false);
?>