<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('AtribuicaoCargo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AtribuicaoCargo', 'element_name' => 'atribuicoes_cargos'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('atribuicoes_cargos/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AtribuicaoCargo/element_name:atribuicoes_cargos/" + Math.random())
        });
        
        function atualizaLista() {
            var codigo_cliente = $("#AtribuicaoCargoCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "atribuicoes_cargos/listagem/" +codigo_cliente + "/" + Math.random());
        }
        
    });', false);
?>