<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('ServicoPlanoSaude', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ServicoPlanoSaude', 'element_name' => 'servicos_planos_saude'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('selecao_servicos_planos_saude/fields_filtros') ?>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php // $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
  function atualizaListaPlanos() {
    var div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "servicos_planos_saude/listagem_servicos/" + Math.random());
  }
  jQuery(document).ready(function(){
    atualizaListaPlanos();
    setup_datepicker();
    jQuery("#limpar-filtro").click(function(){
      bloquearDiv(jQuery(".form-procurar"));
      jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ServicoPlanoSaude/element_name:servicos_planos_saude/" + Math.random())
    });

  });', false);
  ?>