<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('MotivoRecusa', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MotivoRecusa', 'element_name' => 'motivos_recusa'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('motivos_recusa/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
          var codigo_cliente = $("#MotivoRecusaCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MotivoRecusa/element_name:motivos_recusa/" + Math.random())
        });
        
        function atualizaLista() {
          var codigo_cliente = $("#MotivoRecusaCodigoCliente").val();
          var div = jQuery("div.lista");
          bloquearDiv(div);
          div.load(baseUrl + "motivos_recusa/listagem/" + Math.random());
        }
        
    });', false);
?>