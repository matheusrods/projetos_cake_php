<div class='well'>
  <div class='row-fluid inline'>
    <h5>Empresa</h5>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Matriz']['codigo']); ?>
    <strong>Razão Social: </strong><?php echo $this->Html->tag('span', $this->data['Matriz']['razao_social']); ?>
  </div>
  <hr style="border:1px solid #ccc; margin:10px 0 0;"/>
  <div class='row-fluid inline'>
    <h5>Unidade</h5>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Unidade']['codigo']); ?>
    <strong>Razão Social: </strong><?php echo $this->Html->tag('span', $this->data['Unidade']['razao_social']); ?>
  </div>
</div>
<div class='well'>
  <div id='filtros'>
    <?php echo $bajax->form('GrupoExposicao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'GrupoExposicao', 'element_name' => 'grupos_exposicao', 'codigo_cliente' => $this->data['Unidade']['codigo']), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('grupos_exposicao/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
          var codigo_cliente = $("#GrupoExposicaoCodigoClienteAlocacao").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:GrupoExposicao/element_name:grupos_exposicao/codigo_cliente:" + codigo_cliente + "/" + Math.random())
        });
        
        function atualizaLista() {
          var codigo_cliente = $("#GrupoExposicaoCodigoClienteAlocacao").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "grupos_exposicao/listagem/" + codigo_cliente + "/" + Math.random());
        }
        jQuery(".bselect2").select2();
    });', false);
?>