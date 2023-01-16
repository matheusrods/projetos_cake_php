<div class='well'>
    <h5><?= $this->Html->link((!isset($filtrado) ? 'Listagem Filtrada' : 'Definir filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->Bajax->form('TPtvePerifericoTipoVeiculo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TPtvePerifericoTipoVeiculo', 'element_name' => 'vinculo_veiculo_periferico'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('ptve_tvei_codigo', array('type' => 'select', 'options' => $tipo_veiculo,'class' => 'input-meddium','label' =>'Tipo do Veículo','empty' => 'Selecione o tipo veiculo')); ?>
            <?php echo $this->BForm->input('ptve_ppad_codigo', array('type' => 'select', 'options' => $periferico,'class' => 'input-meddium','label' =>'Periférico','empty' => 'Selecione o periferico')); ?>


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

        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "vinculo_veiculo_periferico/listagem/" + Math.random());':'').'
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TPtvePerifericoTipoVeiculo/element_name:vinculo_veiculo_periferico/" + Math.random())
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
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>