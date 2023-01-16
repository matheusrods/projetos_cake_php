<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('Veiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Veiculo', 'element_name' => 'veiculos'), 'divupdate' => '.form-procurar')) ?>
        <?php echo $this->element('veiculos/fields') ?>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function(){
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "veiculos/listagem/" + Math.random());':'').'

        $("#limpar-filtro").click(function(){
            bloquearDiv($(".form-procurar"));
            $(".form-procurar").load(baseUrl + "/filtros/limpar/model:Veiculo/element_name:veiculos/" + Math.random())
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
        
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>