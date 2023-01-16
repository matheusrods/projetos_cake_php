<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('FichaClinica', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaClinica', 'element_name' => 'selecionar_pedido_de_exame'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('fichas_clinicas/fields_filtros_ficha_clinica') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
      <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaFichasClinicas();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:FichaClinica/element_name:selecionar_pedido_de_exame/" + Math.random())
        });
        
        function atualizaListaFichasClinicas() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "fichas_clinicas/listagemPedidoDeExame/" + Math.random());
        }
        
    });', false);
?>