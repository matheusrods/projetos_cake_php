<div class='well'>
    <div id='filtros'>
        <?php echo $bajax->form('ModIvrPesquisa', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ModIvrPesquisa', 'element_name' => 'mods_ivrs_pesquisas_analitico'), 'divupdate' => '.form-procurar')) ?>
        <?= $this->element('filtros/mods_ivrs_pesquisas_filtros') ?>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn' )) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-atendimentos', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>    
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista");bloquearDiv(div);div.load(baseUrl + "mods_ivrs_pesquisas/analitico_listagem/" + Math.random());
        
        jQuery("#limpar-filtro-atendimentos").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ModIvrPesquisa/element_name:mods_ivrs_pesquisas_analitico/" + Math.random())
            jQuery(".lista").empty();
        });
    });', false);
?>