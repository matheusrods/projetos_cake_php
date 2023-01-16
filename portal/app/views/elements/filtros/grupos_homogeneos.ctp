
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
    <?php echo $bajax->form('GrupoHomogeneo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'GrupoHomogeneo', 'element_name' => 'grupos_homogeneos', 'codigo_cliente' => $codigo_cliente, 'referencia' => $referencia), 'divupdate' => '.form-procurar')) ?>
      <?php echo $this->element('grupos_homogeneos/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:GrupoHomogeneo/element_name:grupos_homogeneos/codigo_cliente:'.$codigo_cliente.'/referencia:'.$referencia.'/" + Math.random())
        });
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "grupos_homogeneos/listagem/'.$codigo_cliente.'/'.$referencia.'/" + Math.random());
        }
        
    });', false);
?>