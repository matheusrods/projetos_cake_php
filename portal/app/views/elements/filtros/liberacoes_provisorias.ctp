<div class="well">
    <?php echo $bajax->form('LiberacaoProvisoria', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LiberacaoProvisoria', 'element_name' => 'liberacoes_provisorias'), 'divupdate' => '.form-procurar')); ?>
    	<div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'placeholder' => 'Código', 'class' => 'input-mini', 'maxlength' => 6)); ?>
            <?php echo $this->BForm->input('razao_social', array('label' => false, 'placeholder' => 'Razão Social', 'readonly' => true, 'class' => 'input-xlarge')); ?>
            <?php echo $this->BForm->input('codigo_documento', array('label' => false, 'class' => 'cpf', 'placeholder' => 'CPF Profissional')); ?>
            <?php echo $this->BForm->input('codigo_produto', array('label' => false, 'placeholder' => 'Produto', 'type' => 'select', 'empty' => 'Selecione o produto', 'options' => $produtos )); ?>
            <?php echo $this->BForm->input('data_inicio', array('label' => false, 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
            <?php echo $this->BForm->input('data_fim', array('label' => false, 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
        </div>
            
        <div class="control-group">
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        </div>
    <?php echo $this->BForm->end(); ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function() {
    atualizaListaLiberacoesProvisorias("liberacoes_provisorias");
    setup_mascaras();
    setup_datepicker();

    jQuery("#LiberacaoProvisoriaCodigoCliente")
    .unbind("keydown")
    .bind("keydown", function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) {
            e.preventDefault();
            jQuery(this).change();
        }
    });

    jQuery("#LiberacaoProvisoriaCodigoCliente")
    .unbind("change")
    .bind("change", function() {
        var codigo_cliente = jQuery("#LiberacaoProvisoriaCodigoCliente").val();
        if (codigo_cliente) {
            jQuery.ajax({
                url: "/portal/clientes/carrega_cliente/" + codigo_cliente,
                dataType: "json",
                beforeSend: function (xhr) {
                    bloquearDiv(jQuery('.conteudo'));
                },
                success: function(data) {
                   if (data.Cliente) {
                      jQuery("#LiberacaoProvisoriaRazaoSocial").val(data.Cliente.razao_social);
                   }
                   jQuery('.conteudo').unblock();
                },
                dataType: 'json'
            });
        }
    });

    jQuery('#limpar-filtro')
    .unbind('click')
    .bind('click', function(){
        jQuery(".form-procurar").load(baseUrl + 'filtros/limpar/model:LiberacaoProvisoria/element_name:liberacoes_provisorias/' + Math.random());
    });
});
</script>