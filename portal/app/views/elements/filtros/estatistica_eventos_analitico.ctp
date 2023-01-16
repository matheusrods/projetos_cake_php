<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $this->Bajax->form('TEeveEstatisticaEvento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TEeveEstatisticaEvento', 'element_name' => 'estatistica_eventos_analitico'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?= $this->element('/estatistica_eventos/filtros') ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('esis_usu_codigo_leitura', array('label' => 'Usuário de tratativa', 'multiple' => 'multiple', 'class' => 'input-medium multiselect-usuario-tratativa', 'options'=> $usuarios, 'style' => 'display:none')); ?>
            <?php echo $this->BForm->input('esis_usu_pfis_responsavel', array('label' => 'Usuário de Responsável', 'multiple' => 'multiple', 'class' => 'input-medium multiselect-usuario-responsavel', 'options'=> $usuarios, 'style' => 'display:none')); ?>
            <?php echo $this->BForm->input("hora", array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
            <?php echo $this->BForm->input('status_evento', array('empty' => 'Selecione o status','label'=>'Status Evento', 'options'=> array(1 => 'Tratado',2 => 'Não Tratado'), 'class' => 'inline input-medium')); ?>
            <?php echo $this->BForm->input('status_sla', array('empty' => 'Selecione o status','label'=>'Status do SLA', 'options'=> array(1 => 'Dentro',2 => 'Fora'), 'class' => 'inline input-medium')); ?>
            <?php echo $this->BForm->input("codigo_sm", array('label' => 'SM', 'class' => 'input-small')) ?>
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
        setup_datepicker();
        setup_time();
        '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "estatistica_eventos/analitico_listagem/" + Math.random());':'').'

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TEeveEstatisticaEvento/element_name:estatistica_eventos_analitico/" + Math.random())
            jQuery(".lista").empty();
           
        });

        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });

        jQuery("#FiltroSalvarFiltro").click(function(){
            jQuery("#FiltroNomeFiltro").parent().toggle()
        });

        $(".multiselect-embarcador").multiselect({
            maxHeight: 300,
            nonSelectedText: "Embarcador",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });

        $(".multiselect-transportador").multiselect({
            maxHeight: 300,
            nonSelectedText: "Transportador",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });

        $(".multiselect-evento").multiselect({
            maxHeight: 300,
            nonSelectedText: "Evento",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });

        $(".multiselect-estacao-rastreamento").multiselect({
            maxHeight: 300,
            nonSelectedText: "Estação de Rastreamento",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });

        $(".multiselect-usuario-tratativa").multiselect({
            maxHeight: 300,
            nonSelectedText: "Usuário de tratativa",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });

        $(".multiselect-usuario-responsavel").multiselect({
            maxHeight: 300,
            nonSelectedText: "Usuário de Responsável",
            numberDisplayed: 1,
            includeSelectAllOption: true
        });
 
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php else: ?>    
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").show()})');?> 
 <?php endif; ?>