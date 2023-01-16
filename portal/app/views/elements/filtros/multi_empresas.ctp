<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('MultiEmpresa', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MultiEmpresa', 'element_name' => 'multi_empresas'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('multi_empresas/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MultiEmpresa/element_name:multi_empresas/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "multi_empresas/listagem/" + Math.random());
        }
        
    });', false);
?>