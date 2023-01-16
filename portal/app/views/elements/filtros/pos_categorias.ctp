<div class='well'>
  <div id='filtros'>
    <?= $bajax->form('PosCategorias', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PosCategorias', 'element_name' => 'pos_categorias', $codigo_cliente), 'divupdate' => '.form-procurar')) ?>

      <div class="row-fluid inline">
          <?= $this->BForm->input('codigo_cliente', array( 'type' => 'hidden', 'label' => false, 'value' => $codigo_cliente)) ?>

          <?= $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => 'Razão Social', 'value' => $nome_fantasia, 'readonly'=>true)) ?>
	      <?= $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => 'Nome Fantasia', 'value' => $nome_fantasia, 'readonly'=>true)) ?>
      </div>
      <div class="row-fluid inline">
        <?= $this->BForm->input('codigo_categoria', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => 'Código')); ?>
        <?= $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Tipo de Observação')); ?>
      </div>

      <?= $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?= $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?= $this->BForm->end() ?>
  </div>
</div>

<?= $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
     
      $("#limpar-filtro").click(function(){
          bloquearDiv(jQuery(".form-procurar"));
          $(".form-procurar").load(baseUrl + "/filtros/limpar/model:PosCategorias/element_name:pos_categorias/'.$codigo_cliente.'/" + Math.random())
      });
        
      function atualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "pos_categorias/listagem/'.$codigo_cliente.'/" + Math.random());
      }  
      
      atualizaLista();

    });', false);

?>
