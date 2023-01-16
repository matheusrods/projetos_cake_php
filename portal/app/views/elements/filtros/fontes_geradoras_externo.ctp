<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('FonteGeradoraExterno', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FonteGeradoraExterno', 'element_name' => 'fontes_geradoras_externo'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('fontes_geradoras/fields_externo_filtros') ?>
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
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FonteGeradoraExterno/element_name:fontes_geradoras_externo/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "fontes_geradoras/listagem_externo/" + Math.random());
        }
        
    });', false);
?>