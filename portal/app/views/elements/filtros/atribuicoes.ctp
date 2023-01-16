<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('Atribuicao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Atribuicao', 'element_name' => 'atribuicoes', 'codigo_cliente' => $this->data['Atribuicao']['codigo_cliente']), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('atribuicoes/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            var codigo_cliente = $("#AtribuicaoCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Atribuicao/element_name:atribuicoes/codigo_cliente:" + codigo_cliente + "/" + Math.random())
        });
        
        function atualizaLista() {
            var codigo_cliente = $("#AtribuicaoCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "atribuicoes/listagem/" +codigo_cliente + "/" + Math.random());
        }
        
    });', false);
?>