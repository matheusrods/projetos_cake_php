<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('GrupoEconomico', array('autocomplete' => 'off', 'url' => array(
      'controller' => 'filtros', 
      'action' => 'filtrar', 
      'model' => 'GrupoEconomico', 
      'element_name' => 'permissao', 
      'codigo_questionario' => $codigo_questionario), 
      'divupdate' => '.form-procurar')) ?>

      <?php echo $this->element('questionarios/fields_filtros') ?>
      
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end(); ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
   
        setup_datepicker();

        atualizaLista("'.$codigo_questionario.'");

        jQuery("#limpar-filtro").click(function(){            
            bloquearDiv(jQuery(".form-procurar"));
            //jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:GrupoEconomico/element_name:permissao/" + Math.random())
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:GrupoEconomico/element_name:permissao/codigo_questionario:" + "'.$codigo_questionario.'" + "/" + Math.random())
            jQuery("#GrupoEconomicoCodigoCliente").val("");
        });

        function atualizaLista(codigo_questionario){ //console.log(codigo_questionario);
            //verifica se existe algum codigo para pesquisar
            //if($("#GrupoEconomicoCodigoCliente").val() != "") {
                var div = jQuery("div.lista");
                bloquearDiv(div);
                div.load(baseUrl + "questionarios/lista_permissoes/" + codigo_questionario + "/" + Math.random());
            //}
        }
    });', false);
?>