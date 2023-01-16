<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Cbo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cbo', 'element_name' => 'cargos', 'codigo_cliente' => $this->data['Cliente']['codigo']), 'divupdate' => '.form-procurar')) ?>
      
        <div class="row-fluid inline">
          <?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
          <?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição', 'label' => false)) ?>  
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
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Cbo/element_name:cargos/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "cargos/listagem/" + codigo_cliente + "/" + Math.random());
        }
        
    });', false);
?>