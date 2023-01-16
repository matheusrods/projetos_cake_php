<div class='well'>
	<?php echo $bajax->form('ClienteCobrador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteCobrador', 'element_name' => 'cliente_cobrador'), 'divupdate' => '.form-procurar')) ?>
	<div class="form-horizontal">
	    <div class="row-fluid inline">
	        <?php echo $this->BForm->input('codigo_cliente', array('class' => 'input-small', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')); ?>
	        <?php echo $this->BForm->input('razao_social_readonly', array('readonly' => 'readonly', 'class' => 'input-xlarge', 'placeholder' => 'Razão social', 'label' => false, 'type' => 'text')); ?>
	        <?php echo $this->BForm->input('tipo_cliente', array('label' => false, 'class' => 'input-small', 'options' => array('pagador', 'cliente'), 'empty' => 'Tipo')); ?>
	    </div>
	    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn btn-filtro')); ?>
	    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
	</div>
	<?php echo $this->BForm->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock("
    (function($){
        $(document).ready(function($){
            atualizaListaClienteCobrador();
            
            var inputCodigoCliente = $('#ClienteCobradorCodigoCliente');
            var inputRazaoSocial = $('#ClienteCobradorRazaoSocialReadonly');
    
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
                bloquearDiv(jQuery('.form-horizontal'));
                $('.form-procurar').load(baseUrl + 'filtros/limpar/model:ClienteCobrador/element_name:cliente_cobrador/' + Math.random())
            }); 
        });
    })(jQuery);");

?>