<div class="well">
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id="filtros">
        <?php echo $this->Bajax->form('Tpecas', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Tpecas', 'element_name' => 'tpecas_analitico'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente','Tpecas') ?>
            <?php echo $this->Buonny->input_periodo($this, 'Tpecas','data_inicial', 'data_final', true) ?>
            <?php echo $this->BForm->input('local', array('class' => 'input-mini local','label' => 'Local Vistoria' )); ?>      
        </div>
        <?php echo $this->BForm->submit('Filtrar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
        <?php echo $html->link('Limpar filtro', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end();?>
    </div>
</div>

<?php echo $this->Javascript->codeBlock('
    function atualizaListaTpecas() {
        var div = jQuery("div.lista");
        bloquearDiv(div);   
        div.load(baseUrl + "tpecas/listagem_analitico/" + Math.random());
    }
    jQuery(document).ready(function(){      
        setup_mascaras();     
        atualizaListaTpecas();   
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Tpecas/element_name:tpecas_analitico/" + Math.random())
            jQuery(".lista").empty();
        });
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });
    });', false);
?>
<?php 
    if (isset($filtrado) && $filtrado):
        echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');
    endif; ?>