<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('MetodosTipo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'MetodosTipo', 'element_name' => 'tipos_metodos'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('tipos_metodos/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php
    $btn_incluir = "<a href='/portal/metodos_tipo/incluir/' id='link_add_tipo_metodos' class='btn btn-success' title='Incluir'><i class='icon-plus icon-white'></i></a>"; 
echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){

        var incluir_tipos_metodos = $("#incluir_tipo_metodo");
        
        listagem();
        
        function listagem() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "metodos_tipo/listagem/" + Math.random());
        }

        jQuery("#limpar-filtro").click(function(){

            bloquearDiv(jQuery(".form-procurar"));
            
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:MetodosTipo/element_name:tipos_metodos/" + Math.random())
            
            if(incluir_tipos_metodos.html()) {
                incluir_tipos_metodos.html(" ");
            }
            
            listagem();
        });

        $(function() {
            var codigo_cliente = $("#MetodosTipoCodigoCliente");
                        
            if(codigo_cliente.val() > 0) {
                criaBotaoIncluir(incluir_tipos_metodos);
            }

            codigo_cliente.blur(function(){
                if(codigo_cliente.val() > 0) {
                    criaBotaoIncluir(incluir_tipos_metodos);
                } else {
                    incluir_tipos_metodos.html(" ");
                }
            });

            function criaBotaoIncluir(incluir_tipos_metodos) {
                var btn_incluir  = "' . $btn_incluir . '";
                incluir_tipos_metodos.html(btn_incluir);
                $("#link_add_tipo_metodos").attr("href", "/portal/metodos_tipo/incluir/" + codigo_cliente.val())
            }
        });       
    });', false);
?>