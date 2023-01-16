<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Cnae', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cnae', 'element_name' => 'cnae'), 'divupdate' => '.form-procurar')) ?>
      <div class="row-fluid inline">
        <?php echo $this->BForm->input('cnae', array('class' => 'input-mini', 'placeholder' => 'CNAE', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>  
        <?php echo $this->BForm->input('secao', array('class' => 'input-small', 'label' => false, 'options' => $secao, 'empty' => 'Seção', 'default' => ' ')); ?>
        <?php echo $this->BForm->input('grau_risco', array('class' => 'input-xlarge', 'placeholder' => 'Grau de Risco', 'label' => false)) ?>  
        </div>        
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cnae/element_name:cnae/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "cnae/listagem/" + Math.random());
        }
        
    });', false);
?>