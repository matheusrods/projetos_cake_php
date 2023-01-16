<div class="well">
    <?php echo $this->Bajax->form('Escolta', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Escolta','element_name' => 'escolta_buscar_codigo', 'searcher' => $this->passedArgs['searcher'], 'display' => $this->passedArgs['display']), 'divupdate' => '.form-procurar-codigo-escolta')) ?>
    <div class='row-fluid inline'>
    <?php echo $this->BForm->input('descricao', array('label' => false, 'placeholder' => 'Descrição','type' => 'text', 'class' => 'input-xlarge')) ?>
    </div>
    <div class='row-fluid inline'>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-primary')); ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    </div>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-escolta"));
            jQuery(".form-procurar-codigo-escolta").load(baseUrl + "/filtros/limpar/model:Escolta/element_name:escolta_buscar_codigo/searcher:'.$this->passedArgs['searcher'].'/display:'.$this->passedArgs['display'].'/" + Math.random())
        });
		atualizaListaEscoltaVisualizar("'.$this->passedArgs['searcher'].'", "'.$this->passedArgs['display'].'");
});', false);
?>