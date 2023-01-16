<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('MotivosDesconto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MotivosDesconto', 'element_name' => 'motivos_desconto'), 'divupdate' => '.form-procurar')) ?>
      <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Cód.', 'label' => false, 'type' => 'text')) ?>
        <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => false)) ?> 
         <?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => false, 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Status', 'default' => "")); ?>  
      </div>        
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
      
      function atualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "motivos_desconto/listagem/" + Math.random());
      }  
      
      atualizaLista();
      
      jQuery("#limpar-filtro").click(function(){
          bloquearDiv(jQuery(".form-procurar"));
          jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MotivosDesconto/element_name:motivos_desconto/" + Math.random())
      });
        
    });', false);
?>