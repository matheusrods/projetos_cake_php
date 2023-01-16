<?php echo $this->BForm->create('ProfNegativacaoCliente', array('type' => 'post' ,'url' => array('controller' => 'profissionais_negativados_clientes','action' => 'incluir')));?>
<?if( empty($authUsuario['Usuario']['codigo_cliente']) ): ?>
<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', TRUE, 'ProfNegativacaoCliente' ); ?>
	<?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'readonly'=>true)) ?>
</div>
<?endif;?>
<div class="row-fluid inline parent">
	<?php echo $this->BForm->input('Profissional.codigo_documento', array('label' => 'CPF', 'class' => 'input-medium formata-cpf', 'readonly' => FALSE )); ?>
    <?php echo $this->BForm->input('Profissional.nome', array('label' => 'Nome', 'class' => 'input-xxlarge', 'readonly' => TRUE)); ?>        
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->hidden('Profissional.codigo') ?>
    <?php echo $this->BForm->input('codigo_negativacao', array('label'=>'Tipo Negativaçao', "empty"=>"Tipo de Negativaçao","options" => $tipo_negativacao)); ?>
</div>
<div class="row-fluid inline">
    <?php echo $this->BForm->input('observacao',array('class' => 'input-xxlarge','type'=>'textarea input-large','label' => 'Observação')); ?>   
</div>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock("
	var x = null;    	
	function buscar_cpf(input_cpf, input_receiver_nome, input_receiver_codigo) {
		input_cpf = $(input_cpf);
		input_receiver_codigo = $(input_receiver_codigo);
		input_receiver_nome = $(input_receiver_nome);
		$.ajax({
            url: baseUrl + 'profissionais/carregarPorCpf/' + input_cpf.val() + '/' + Math.random(),
            dataType: 'json',
            success: function(data){
            	if (data != null && data != false) {
                    input_receiver_codigo.val(data.Profissional.codigo);
					input_receiver_nome.val(data.Profissional.nome);
            	} else {
            		input_receiver_codigo.val('');
					input_receiver_nome.val('');
					input_cpf.val('');
            	}
            }
        });
	}	
    jQuery(document).ready(function() {
		pesquisa_cliente('ProfNegativacaoClienteCodigoCliente');
        $(document).on('blur', '#ProfissionalCodigoDocumento', function(e) {
            buscar_cpf(this, '#ProfissionalNome', '#ProfissionalCodigo');
        });
    })"
) ?>