<div class='well'>
    <h5><?= $this->Html->link((!isset($filtrado) ? 'Listagem Filtrada' : 'Definir filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->Bajax->form('ObjetivoComercial', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ObjetivoComercial', 'element_name' => 'objetivo_comercial_sintetico'), 'divupdate' => '.form-procurar')) ?>
        <?= $this->element('/objetivo_comercial/filtros') ?>
        <span class="label label-info">Agrupar por:</span>
        <div class="row-fluid inline">
            <div id='agrupamento'>
                <?php echo $this->BForm->input('ObjetivoComercial.agrupamento', array('type' => 'radio', 'options' => $listaAgrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline visualizacao input-medium'))) ?>
            </div>
        </div>        
        <div class="row-fluid inline">
            <span class="label label-info">Gráfico por:</span>
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('ObjetivoComercial.tipoVisualizacao', array('type' => 'radio', 'options' => $listaTipoVisualizacao, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
            </div>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
            <?php echo $this->BForm->end() ?>
        </div>
    </div>
</div>
 <?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "objetivos_comerciais/listagem_sintetico/" + Math.random());':'').'

        invalida();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ObjetivoComercial/element_name:objetivo_comercial_sintetico/" + Math.random())
            jQuery(".lista").empty();
           
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });

       if($("#ObjetivoComercialAgrupamento2").is(":checked")){
            $( "#ObjetivoComercialTipoVisualizacao1" ).hide();
            document.getElementById("ObjetivoComercialTipoVisualizacao2").checked = true;
       }

        function invalida(){
            $(".visualizacao").click(function(){
                if($("#ObjetivoComercialAgrupamento2").is(":checked")){   
                    $( "#ObjetivoComercialTipoVisualizacao1" ).hide();                    
                    if(document.getElementById("ObjetivoComercialTipoVisualizacao3").checked === false){
                        document.getElementById("ObjetivoComercialTipoVisualizacao2").checked = true;
                    }
                }else{
                    $( "#ObjetivoComercialTipoVisualizacao1" ).show();
                    document.getElementById("ObjetivoComercialTipoVisualizacao1").checked = true;
               }
            }); 
        }
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>