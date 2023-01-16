<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('GrupoEconomico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'GrupoEconomico', 'element_name' => 'separacao_grupos_economicos'), 'divupdate' => '.form-procurar')) ?>

      <?php echo $this->element('grupos_economicos/fields_filtros_sep_ge') ?>
      
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
   
        setup_datepicker();

        atualizaLista();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:GrupoEconomico/element_name:separacao_grupos_economicos/" + Math.random())
        });

        function atualizaLista(){
            //verifica se existe algum codigo para pesquisar
            if($("#GrupoEconomicoCodigoCliente").val() != "") {
                var div = jQuery("div.lista");
                bloquearDiv(div);
                div.load(baseUrl + "grupos_economicos/lista_unidades_grupo_economico/" + Math.random());
            }
        }
    });', false);
?>