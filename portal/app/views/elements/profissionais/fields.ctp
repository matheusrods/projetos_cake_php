<div class="row-fluid inline">
    <?php echo $this->BForm->hidden('Profissional.codigo'); ?>
    <?php echo $this->BForm->input('Profissional.codigo_documento', array('label' => 'CPF', 'class' => 'input-medium', 'readonly' => $edit_mode)); ?>
    <?php echo $this->BForm->input('Profissional.nome', array('label' => 'Nome', 'class' => 'input-xxlarge', 'readonly' => TRUE)); ?>
</div>
<?php if (!$edit_mode): ?>
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
                	}
                }
            });
		}	
	    jQuery(document).ready(function() {
	        $(document).on('blur', '#ProfissionalCodigoDocumento', function(e) {
	            buscar_cpf(this, '#ProfissionalNome', '#ProfissionalCodigo');
	        });
	    })"
    ) ?>
<?php endif ?>