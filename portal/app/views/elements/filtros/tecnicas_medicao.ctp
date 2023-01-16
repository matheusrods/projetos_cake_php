<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('TecnicaMedicao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TecnicaMedicao', 'element_name' => 'tecnicas_medicao'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('tecnicas_medicao/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaTecnicasMedicao();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TecnicaMedicao/element_name:tecnicas_medicao/" + Math.random())
        });
        
        function atualizaListaTecnicasMedicao() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "tecnicas_medicao/listagem/" + Math.random());
        }
        
    });', false);
?>