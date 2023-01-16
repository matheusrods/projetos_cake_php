<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('TecnicaMedicaoPpra', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'TecnicaMedicaoPpra', 'element_name' => 'tecnicas_medicao_terceiro'), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('tecnicas_medicao/fields_filtros_terceiros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php
    $btn_incluir = "<a href='/portal/tecnicas_medicao/incluir_terceiros/' id='link_incluir_tecnicas' class='btn btn-success' title='Incluir'><i class='icon-plus icon-white'></i></a>"; 
echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){

        var incluir_incluir_tecnicas_medicao_botao = $("#incluir_tecnicas_medicao");
        
        atualizaListaTecnicasMedicao();
        
        function atualizaListaTecnicasMedicao() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "tecnicas_medicao/lista_terceiros/" + Math.random());
        }

        jQuery("#limpar-filtro").click(function(){

            bloquearDiv(jQuery(".form-procurar"));
            
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:TecnicaMedicaoPpra/element_name:tecnicas_medicao_terceiro/" + Math.random())
            
            if(incluir_incluir_tecnicas_medicao_botao.html()) {
                incluir_incluir_tecnicas_medicao_botao.html(" ");
            }
            
            atualizaListaTecnicasMedicao();
        });

        $(function() {
            var codigo_cliente = $("#TecnicaMedicaoPpraCodigoCliente");
                        
            if(codigo_cliente.val() > 0) {
                criaBotaoIncluir(incluir_incluir_tecnicas_medicao_botao);
            }

            codigo_cliente.blur(function(){
                if(codigo_cliente.val() > 0) {
                    criaBotaoIncluir(incluir_incluir_tecnicas_medicao_botao);
                } else {
                    incluir_incluir_tecnicas_medicao_botao.html(" ");
                }
            });

            function criaBotaoIncluir(incluir_incluir_tecnicas_medicao_botao) {
                var btn_incluir  = "' . $btn_incluir . '";
                incluir_incluir_tecnicas_medicao_botao.html(btn_incluir);
                $("#link_incluir_tecnicas").attr("href", "/portal/tecnicas_medicao/incluir_terceiros/" + codigo_cliente.val())
            }
        });       
    });', false);
?>