<div class='well'>
    <?php echo $bajax->form('ClienteProduto', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteProduto', 'element_name' => 'clientes_produtos'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_cliente', array('type'=>'text', 'label' => false, 'placeholder' => 'Código', 'class' => 'input-small')) ?>
            <?php echo $this->BForm->input('razao_social', array('label' => false, 'placeholder' => 'Razão Social', 'readonly' => true, 'class' => 'input-xlarge')) ?>
            <?php echo $this->BForm->input('codigo_produto', array('label' => false, 'options' => $produtos, 'empty'=>'Selecione o produto', 'class' => 'input-xlarge')); ?>
            <?php echo $this->BForm->input('codigo_contrato', array('label' => false, 'placeholder' => 'N° Contrato', 'class' => 'input-small')); ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('data_envio', array('label' => false, 'placeholder' => 'Data Envio', 'class' => 'data input-small')); ?>
            <?php echo $this->BForm->input('data_contrato', array('label' => false, 'placeholder' => 'Data Contrato', 'class' => 'data input-small')); ?>
            <?php echo $this->BForm->input('codigo_motivo_bloqueio', array('label' => false, 'empty' => 'Selecione o motivo', 'options' => $status_produto, 'class' => 'input-medium')); ?>
            <?php echo $this->BForm->input('codigo_status_contrato', array('label' => false, 'empty' => 'Selecione o Status', 'options' => $status_contrato, 'class' => 'input-medium')); ?>
            <?php echo $buonny->combo_booleano('possui_contrato', array('label' => false, 'class' => 'input-medium', 'empty' => 'Possui Contrato?')) ?>
        </div>

        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    <?php echo $this->BForm->end() ?>
</div>
<?php $script = '
   jQuery(document).ready(function(){
        atualizaListaClientesProdutosParaContrato();
        setup_mascaras();
        setup_datepicker();
        var inputCodigoCliente = $("#ClienteProdutoCodigoCliente");
        var inputRazaoSocial = $("#ClienteProdutoRazaoSocial");

        inputRazaoSocial.val("");

        var carregaRazaoSocial = function() {
            var self = this;
            var codigo = $(this).val();
            if (codigo == "")  {
                inputRazaoSocial.val("");
                return false;
            }
            $.ajax(baseUrl + "/clientes/carrega_razaosocial/" + codigo, {
                success: function(r, status, e) {
                    if (r) {
                        inputRazaoSocial.val(r.Cliente.razao_social);
                    } else {
                        inputRazaoSocial.val("");
                        self.select();
                    }
                }
            }).trigger("change");
        }

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteProduto/element_name:clientes_produtos/" + Math.random())
        });


    });';
 echo $this->Javascript->codeBlock($script);
?>