<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('AtendimentoSac', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AtendimentoSac', 'element_name' => 'atendimentos_sac_sintetico'), 'divupdate' => '.form-procurar')) ?>
        <?= $this->element('filtros/atendimentos_sac') ?>
        <span class="label label-info">Agrupar por:</span>
        <div id='agrupamento'>
            <?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-medium'))) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-atendimentos', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>     
</div>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    setup_mascaras();
    $(".hora").mask("99:99");       
    
    HoraInicial = document.getElementById("AtendimentoSacHoraInicial").value.split(":");
    HoraFinal = document.getElementById("AtendimentoSacHoraFinal").value.split(":");
    
    IncialHora = parseInt(HoraInicial[0]);
    IncialMinutos = parseInt(HoraInicial[1]);
    FinalHora = parseInt(HoraFinal[0]);
    FinalMinutos = parseInt(HoraFinal[1]);
   
    if(IncialHora > 23 || IncialMinutos > 59){
        alert("Hora Incial Inválida");
        document.getElementById("AtendimentoSacHoraInicial").value = "00:00";

    }else if(FinalHora > 23 || FinalMinutos > 59){
        alert("Hora Final Inválida");
        document.getElementById("AtendimentoSacHoraFinal").value = "23:59";
    }
    
    '.(isset($filtrado) && ($filtrado) ? 'var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "atendimentos_sacs/sintetico_listagem/" + Math.random());':'').'

    jQuery("#limpar-filtro-atendimentos").click(function(){
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AtendimentoSac/element_name:atendimentos_sac_sintetico/" + Math.random())
    });
    jQuery("a#filtros").click(function(){
        jQuery("div#filtros").slideToggle("slow");
    });
    jQuery("#FiltroSalvarFiltro").click(function(){
        jQuery("#FiltroNomeFiltro").parent().toggle()
    });
});', false);?>
<?php
if (!empty($filtrado)):
    echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');
endif; 
?>