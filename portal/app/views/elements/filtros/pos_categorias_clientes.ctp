<div class='well'>
  <div id='filtros'>
    <?= $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'pos_categorias_clientes'), 'divupdate' => '.form-procurar')) ?>

      <div class="row-fluid inline">
          <?php

          if ($is_admin) {
              if ($this->Buonny->seUsuarioForMulticliente()) {
                  echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Cliente');
              } else {
                  echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'Cliente');
              }
          } else {
              if ($this->Buonny->seUsuarioForMulticliente()) {
                  echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Cliente');
              } else {

                  echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                  echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));
              }
          }
          ?>
      </div>

      <?php if ($is_admin || $this->Buonny->seUsuarioForMulticliente()) :?>
          <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
          <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')); ?>
      <?php endif; ?>

    <?= $this->BForm->end() ?>
  </div>
</div>

<?= $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
     
        atualizaListaCliente();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:pos_categorias_clientes/" + Math.random())
        });
            
        function atualizaListaCliente() {     
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "pos_categorias/listagem_clientes/" + Math.random()); 
        }
        
    });', false);
    
?>
