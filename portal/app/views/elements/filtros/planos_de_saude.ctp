<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('PlanoDeSaude', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PlanoDeSaude', 'element_name' => 'planos_de_saude'), 'divupdate' => '.form-procurar')) ?>
      <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>  
        <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => ' ')); ?>
      </div>        
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaPlanosDeSaude();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PlanoDeSaude/element_name:planos_de_saude/" + Math.random())
        });
        
        function atualizaListaPlanosDeSaude() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "planos_de_saude/listagem/" + Math.random());
        }
        
    });', false);
?>