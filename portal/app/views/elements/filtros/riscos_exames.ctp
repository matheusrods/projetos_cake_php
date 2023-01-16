<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('RiscoExame', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'RiscoExame', 'element_name' => 'riscos_exames', 'codigo_cliente' => $this->data['RiscoExame']['codigo_cliente']), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('riscos_exames/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            var codigo_cliente = $("#RiscoExameCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:RiscoExame/element_name:riscos_exames/codigo_cliente:" + codigo_cliente + "/" + Math.random())
        });
        
        function atualizaLista() {
            var codigo_cliente = $("#RiscoExameCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "riscos_exames/listagem/" +codigo_cliente + "/" + Math.random());
        }
        
    });', false);
?>
