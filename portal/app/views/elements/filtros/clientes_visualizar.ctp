<?php if(isset($authUsuario['Usuario']['codigo_seguradora']) && $authUsuario['Usuario']['codigo_seguradora']): ?>
    <div class='well'>
        <strong>Seguradora: </strong> <?php echo $this->data['Cliente']['descricao_seguradora']; ?>
    </div>
<?php endif; ?>
<?php if(isset($authUsuario['Usuario']['codigo_corretora']) && $authUsuario['Usuario']['codigo_corretora']): ?>
    <div class='well'>
        <strong>Corretora: </strong> <?php echo $this->data['Cliente']['descricao_corretora']; ?>
    </div>
<?php endif; ?>
<?php if(isset($authUsuario['Usuario']['codigo_filial']) && $authUsuario['Usuario']['codigo_filial']): ?>
    <div class='well'>
        <strong>Filial: </strong> <?php echo $this->data['Cliente']['descricao_endereco_regiao']; ?>
    </div>
<?php endif; ?>
<div class='well'>
  <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'clientes_visualizar'), 'divupdate' => '.form-procurar')) ?>
    <?php echo $this->element('clientes/fields_filtros') ?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaClientesVisualizar("clientes");
        setup_datepicker();
        jQuery("#limpar-filtro-clientes").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:clientes_visualizar/" + Math.random())
        });
        
        jQuery(".codigoClienteTipo").bind("change",
            function() {
                jQuery.ajax({
                    "url": baseUrl + "/clientes_sub_tipos/combo/" + jQuery(this).val() + "/" + Math.random(),
                    "success": function(data) {
                        jQuery(".codigoClienteSubTipo").html(data).val();
                    }
                });
            }
        );
    });', false);
?>
