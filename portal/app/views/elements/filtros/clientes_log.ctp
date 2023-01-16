
<div class='well'>
    <?php echo $bajax->form('ClienteLog', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteLog', 'element_name' => 'clientes_log'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_cliente', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')); ?>
            <?php echo $this->BForm->input('razao_social', array('readonly' => true, 'class' => 'input-xxlarge', 'placeholder' => 'Razão Social', 'label' => false, 'type' => 'text')); ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('usuario', array('placeholder' => 'Usuário', 'label' => false, 'type' => 'text')); ?>
            <?php echo $this->BForm->hidden('codigo_usuario'); ?>
            <?php echo $form->input('data_inicio', array('class' => 'data input-small', 'placeholder' => 'Log a partir de', 'label' => false, 'type' => 'text')); ?>
            <?php echo $form->input('data_fim', array('class' => 'data input-small', 'placeholder' => 'Log até', 'label' => false, 'type' => 'text')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-filtro')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
    <?php echo $this->BForm->end(); ?>
</div>   

<?php echo $this->Javascript->codeBlock("
    (function($){
        $(document).ready(function($){
            atualizaListaClientesLog(); 
            atualizaListaEnderecoClientesLog();
            atualizaListaContatoClientesLog();
            atualizaListaProdutoClientesLog();
            atualizaListaProdutoServicosClientesLog();
            setup_datepicker();
            
            var inputCodigoCliente = $('#ClienteLogCodigoCliente');
            var inputRazaoSocial = $('#ClienteLogRazaoSocial');
    
            inputRazaoSocial.val('');
    
            var carregaRazaoSocial = function() {
                var self = this;
                var codigo = $(this).val();
                if (codigo == '')  {
                    inputRazaoSocial.val('');
                    return false;
                }
                $.ajax(baseUrl + '/clientes/carrega_cliente/' + codigo, {
                    success: function(r, status, e) {
                        if (r) {
                            inputRazaoSocial.val(r.Cliente.razao_social);
                        } else {
                            inputRazaoSocial.val('');
                            self.select();
                        }
                    }
                });
            }
    
            inputCodigoCliente.change(carregaRazaoSocial).keyup(function(e) {
                if (e.keyCode == 13) {
                    $(this).blur();
                    return false;
                }
            }).trigger('change');
            
            $('#limpar-filtro').click(function(){
                bloquearDiv(jQuery('.form-procurar'));
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:ClienteLog/element_name:clientes_log/' + Math.random());
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:ClienteEnderecoLog/element_name:clientes_log/' + Math.random());
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:ClienteContatoLog/element_name:clientes_log/' + Math.random());
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:ClienteProdutoLog/element_name:clientes_log/' + Math.random());
                $('.form-procurar').load(baseUrl + '/filtros/limpar/model:ClienteProdutoServicoLog/element_name:clientes_log/' + Math.random());
            }); 
        });
    })(jQuery);", false);

?>





