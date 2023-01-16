<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('ClienteSetorCargo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteSetorCargo', 'element_name' => 'clientes_setores_cargos', 'codigo_cliente' => $codigo_cliente), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('clientes_setores_cargos/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            var codigo_cliente = $("#ClienteSetorCargoCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:ClienteSetorCargo/element_name:clientes_setores_cargos/codigo_cliente:" + codigo_cliente + "/" + Math.random())
        });
        
        function atualizaLista() {
            var codigo_cliente = $("#ClienteSetorCargoCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes_setores_cargos/listagem/" + codigo_cliente + "/" + Math.random());
        }
    });
    
    $(document).ready(function(){
      $(".bselect2").select2();
    });', false);


?>