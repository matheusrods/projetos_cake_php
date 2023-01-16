<?php //debug($this->validationErrors);?>
<?php //debug($this->data);?>

<div class='well'>
	<?php if ($edit_mode): ?>
	<?php echo $this->BForm->hidden('codigo'); ?>
	<?php endif; ?>

	<div class="row-fluid inline">
		<?php

            if ($is_admin) {
                if ($this->Buonny->seUsuarioForMulticliente()) {
                    echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'Chamado');
                } else {
                    echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'Chamado');
                }
            } else {
                echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));
            }
        ?>
	</div>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Título', 'label' => 'Título (*)')) ?>

		<?php echo $this->BForm->input('codigo_chamado_tipo', array('label' => 'Tipo (*)','class' => 'input-medium', 'options'=> $combo_chamado_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

		<?php echo $this->BForm->input('responsavel', array(
            'label' => 'Responsavel (*)',
            'options' => $combo_usuarios,
            'empty' => 'Selecione',
            'default' => '',
            'class' => 'input-medium responsavel'));?>

		<?php if ($edit_mode): ?>
		<?php echo $this->BForm->input('codigo_chamado_status', array('label' => 'Status (*)','class' => 'input-medium', 'options'=> $combo_chamado_status, 'empty' => 'Todos', 'default' => ' ')); ?>
		<?php endif;  ?>
	</div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('descricao_levantamento', array('type' => 'textarea', 'class' => 'input-xxlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição')) ?>
    </div>

	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('[data-toggle=\"tooltip\"]').tooltip();
	});

	jQuery('#ChamadoCodigoCliente').change(function() {
		var codigo_cliente = this.value;

		comboResponsavel(codigo_cliente);
	});

	var comboResponsavel = function(codigo_cliente) {
		jQuery('.responsavel').html('<option value="">Carregando...</option>');

		jQuery.ajax({
			url: baseUrl + 'chamados/combo_usuarios_ajax',
			type: 'POST',
			dataType: 'html',
			data: {
				'codigo_cliente': codigo_cliente
			}
		})
		.done(function(response) {
			if (response) {
				jQuery('.responsavel').html(response);
			}
		});
	}
</script>
