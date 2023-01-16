<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('UsuarioMultiCliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'UsuarioMultiCliente', 'element_name' => 'multi_clientes_selecionar'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('usuarios_multi_cliente/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaMultiCliente();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:UsuarioMultiCliente/element_name:multi_clientes_selecionar/" + Math.random())
        });
        
        function atualizaListaMultiCliente() {
            var div = jQuery("div.listaSelecionar");
		
            bloquearDiv(div);
            div.load(baseUrl + "usuarios_multi_cliente/selecionar_cliente_listagem/" + Math.random());
        }
        
    });', false);
?>