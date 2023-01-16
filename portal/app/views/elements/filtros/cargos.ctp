<div class='well'>
<?php echo $bajax->form('Cargo', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cargo', 'element_name' => 'cargos', 'codigo_cliente' => $this->data['Cliente']['codigo'], 'referencia' => $referencia, 'terceiros_implantacao' => $terceiros_implantacao), 'divupdate' => '.form-procurar')) ;
     if($this->Buonny->seUsuarioForMulticliente()) { 
        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Cargo');     
    } else {
?>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $this->data['Cliente']['razao_social']); ?>
    <?php echo $this->BForm->hidden('Cargo.codigo_cliente', array('value' => $this->data['Cliente']['codigo'])); ?>
<?php echo '</div><div class="well">'; } ?>
   <div id='filtros'>
      <?php echo $this->element('cargos/fields_filtros') ?>
      <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
  </div>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro").click(function(){
          var codigo_cliente = $("#CargoCodigoCliente").val();
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Cargo/element_name:cargos/codigo_cliente:" + codigo_cliente + "/referencia:'.$referencia.'/" + "/terceiros_implantacao:" + "'.$terceiros_implantacao.'")
        });
        
        function atualizaLista() {
          var codigo_cliente = $("#CargoCodigoCliente").val();
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "cargos/listagem/" + codigo_cliente + "/'.$referencia.'/" + "'.$terceiros_implantacao.'");
        }
        
    });', false);
?>