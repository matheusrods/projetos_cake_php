<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Crra', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Crra', 'element_name' => 'clientes_responsaveis_registros_ambientais'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('clientes_responsaveis_registros_ambientais/fields_filtros') ?>
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
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Crra/element_name:clientes_responsaveis_registros_ambientais/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "clientes_responsaveis_registros_ambientais/listagem/" + Math.random());
        }
        
    });', false);
?>