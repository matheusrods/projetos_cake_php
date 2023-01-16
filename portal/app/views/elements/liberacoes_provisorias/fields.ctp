<div class="row-fluid inline">
    <?php echo $this->BForm->input('Profissional.codigo_documento', array('label' => 'CPF do profissional', 'maxlength' => 14, 'class' => 'input-medium cpf', 'div' => 'control-group input text span2')); ?>
    <?php echo $this->BForm->input('codigo_profissional', array('type' => 'hidden')); ?>
    <?php echo $this->BForm->input('Profissional.nome', array('label' => 'Nome do profissional', 'class' => 'input-xlarge nome', 'disabled' => 'disabled')); ?>
    <?php echo $this->BForm->input('data_liberacao', array('label' => 'Validade', 'type' => 'text', 'maxlength' => 10, 'class' => 'input-small data validade')); ?>
    <?php echo $this->BForm->input('codigo_profissional_tipo', array('type' => 'select', 'label' => 'Tipo do profissional', 'options' => array('Outros', 'Carreteiro'))); ?>
    <?php echo $this->BForm->input('codigo_produto', array('class' => 'checkbox inline', 'options' => $produtos, 'multiple' => 'checkbox', 'label' => 'Produto'));?>
</div>    
<div class="escolher-cliente row-fluid inline">
    <?php echo $this->BForm->input('codigo_cliente', array('label' => 'Código do cliente', 'maxlength' => 10, 'class' => 'input-medium codigo_cliente', 'div' => 'control-group input text span2')); ?>
    <?php echo $this->BForm->input('Cliente.nome', array('label' => 'Razão social', 'class' => 'input-xxlarge nome_cliente', 'disabled' => 'disabled')); ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
    setup_datepicker();
    setup_mascaras();
    $("select[name='data[LiberacaoProvisoria][codigo_profissional_tipo]']")
    .unbind('change')
    .bind('change', function() {
        var selecionado = $(':selected', this).val();
        if (selecionado == 1) {
        	$(".escolher-cliente").hide();
        } else {
        	$(".escolher-cliente").show();
        }
    });

    $("#ProfissionalCodigoDocumento")
    .unbind('keydown')
    .bind('keydown', function(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if(code == 13) {
            e.preventDefault();
            $(this).change();
        }
    });

    $("#ProfissionalCodigoDocumento")
    .unbind('change')
    .change('change', function() {
        var codigo_documento = $(this).val().replace(/[\-|\.]/g, '');
        if (codigo_documento) {
        	$.ajax({
                url: "/portal/profissionais/carrega_profissionalnome/" + codigo_documento,
                dataType: "json",
                beforeSend: function (xhr) {
                    bloquearDiv(jQuery('.conteudo'));
                },
                success: function(data) {
                   if (data.Profissional) {
                	   $("#ProfissionalNome").val(data.Profissional.nome);
                       $("#LiberacaoProvisoriaCodigoProfissional").val(data.Profissional.codigo);
                   }
                   jQuery('.conteudo').unblock();
                }
            }).done(function() {
                jQuery('.conteudo').unblock();
            });
        }
    });

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
                      jQuery("#ClienteNome").val(data.Cliente.razao_social);
                   }
                   jQuery('.conteudo').unblock();
                },
                dataType: 'json'
            }).done(function() {
                jQuery('.conteudo').unblock();
            });
        }
    });    
    $(":submit")
    .unbind('click')
    .bind('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).focus();
    	var confirm = window.confirm("Confirmar Perfil Adequado por Prazo?");
    	if (confirm) {
    		$("#LiberacaoProvisoriaIncluirForm").submit();
    	}
    });
    $("#ProfissionalCodigoDocumento").trigger('change');
    $("#LiberacaoProvisoriaCodigoCliente").trigger('change');    
    $("select[name='data[LiberacaoProvisoria][codigo_profissional_tipo]']").trigger('change');    
});
</script>