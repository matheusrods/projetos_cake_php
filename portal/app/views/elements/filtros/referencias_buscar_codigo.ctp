<div class="well">
    <?php 
    $codigo2 = !empty($this->passedArgs['codigo2']) ? $this->passedArgs['codigo2'] : null;
    echo $this->Bajax->form('Referencia', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Referencia', 'element_name' => 'referencias_buscar_codigo', 'codigo' => $this->passedArgs['codigo'], 'codigo2' => $codigo2, 'searcher' => $this->passedArgs['searcher'], 'display' => $this->passedArgs['display']), 'divupdate' => '.form-procurar-codigo-referencia')) ?>
  		<?php echo $this->element('referencias/fields_filtros') ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-referencia"));
            jQuery(".form-procurar-codigo-referencia").load(baseUrl + "/filtros/limpar/model:Referencia/element_name:referencias_buscar_codigo/searcher:'.$this->passedArgs['searcher'].'/display:'.$this->passedArgs['display'].'/codigo:'.$this->passedArgs['codigo'].'/codigo2:'.$codigo2.'/" + Math.random())
        });
        atualizaListaReferenciasVisualizar("referencias_buscar_codigo", "'.$this->passedArgs['searcher'].'", "'.$this->passedArgs['display'].'");
    });', false);
?>