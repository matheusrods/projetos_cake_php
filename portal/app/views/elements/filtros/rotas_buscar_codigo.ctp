<div class="well">
  <?php if (isset($this->passedArgs['codigo_embarcador']) || isset($this->passedArgs['codigo_transportador'])): ?>
    <?php echo $this->Bajax->form('TRotaRota', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TRotaRota', 'element_name' => 'rotas_buscar_codigo','codigo_embarcador' => $this->passedArgs['codigo_embarcador'],'codigo_transportador' => $this->passedArgs['codigo_transportador'], 'searcher' => $this->passedArgs['searcher'], 'display' => $this->passedArgs['display']), 'divupdate' => '.form-procurar-codigo-rota')) ?>
  <?php else: ?>    
    <?php echo $this->Bajax->form('TRotaRota', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TRotaRota', 'element_name' => 'rotas_buscar_codigo','codigo' => $this->passedArgs['codigo'], 'searcher' => $this->passedArgs['searcher'], 'display' => $this->passedArgs['display']), 'divupdate' => '.form-procurar-codigo-rota')) ?>
  <?php endif; ?>
  <div class='row-fluid inline'>

    <?php echo $this->BForm->input('descricao', array('label' => false, 'placeholder' => 'Descrição','type' => 'text', 'class' => 'input-xlarge')) ?>

  </div>
  <div class='row-fluid inline'>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  </div>
  <?php echo $this->BForm->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock('
  jQuery(document).ready(function(){
    jQuery("#limpar-filtro").click(function(){
      bloquearDiv(jQuery(".form-procurar-codigo-rota"));
      jQuery(".form-procurar-codigo-rota").load(baseUrl + "/filtros/limpar/model:TRotaRota/element_name:rotas_buscar_codigo/'.$filtro_rota.'/searcher:'.$this->passedArgs['searcher'].'/display:'.$this->passedArgs['display'].'/" + Math.random())
    });
    atualizaListaRotasVisualizar("'.$this->passedArgs['searcher'].'", "'.$this->passedArgs['display'].'");
});', false);
?>