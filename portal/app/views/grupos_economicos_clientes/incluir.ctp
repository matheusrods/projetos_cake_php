<?php echo $this->BForm->create('GrupoEconomicoCliente', array('url'=>array('controller' => 'grupos_economicos_clientes', 'action' => 'incluir', $this->passedArgs[0]))); ?>
<div id="cliente" class='well'>
	<strong>Grupo Econômico: </strong><?= $grupo_economico['GrupoEconomico']['descricao'] ?>
</div>
<div class="well">
    <div class='row-fluid inline'>
    	<?php echo $this->BForm->hidden('codigo_grupo_economico') ?>
     	<?php echo $this->Buonny->input_codigo_cliente($this); ?>
     	<?php echo $this->BForm->input('Cliente.razao_social', array('label' => false, 'placeholder' => 'Razão Social', 'readonly' => true, 'class' => 'input-xxlarge')) ?>
    </div>  
</div>
<div class='form-actions'>
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', array('controller' => 'grupos_economicos_clientes', 'action' => 'index', $this->passedArgs[0]), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function($){
        
        var inputCodigoCliente = $('#GrupoEconomicoClienteCodigoCliente');
        var inputRazaoSocial = $('#ClienteRazaoSocial');
        var carregaRazaoSocial = function() {
        	inputRazaoSocial.val('');
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
    });");
?>