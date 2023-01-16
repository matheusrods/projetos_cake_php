<div class='well'>
  <?php echo $bajax->form('CtrPreFatPerCapita', array('autocomplete' => 'off', 
                                                       'url' => array('controller' => 'filtros', 
                                                                      'action' => 'filtrar', 
                                                                      'model' => 'CtrPreFatPerCapita', 
                                                                      'element_name' => "ctr_pre_fat_per_capita"), 
                                                        'divupdate' => '.form-procurar')
        ) 
    ?>
    <?php echo $this->element('ctr_pre_fat_per_capita/fields_filtros') ?>
    <?php echo $this->BForm->end() ?>
</div> 

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaCtrPreFatPerCapita();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:CtrPreFatPerCapita/element_name:ctr_pre_fat_per_capita/" + Math.random())
        });
    });', false);
?>
<script type="text/javascript">
    
    function atualizaListaCtrPreFatPerCapita() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "ctr_pre_fat_per_capita/listagem/" + Math.random());
    }
</script>