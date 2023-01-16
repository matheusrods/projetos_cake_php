<div class='well'>
	<?php if($edit_mode): ?>
		<?php echo $this->BForm->hidden('codigo'); ?>
	<?php endif; ?>

    <div class="row-fluid inline">
        <?php
        
        if ($is_admin) {
            if ($this->Buonny->seUsuarioForMulticliente()) {
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'PerigosAspectos', $this->data['PerigosAspectos']['codigo_cliente']);
            } else {
                echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'PerigosAspectos');
            }
        } else {

            if (isset($_SESSION['Auth']['Usuario']['multicliente']) && !empty($_SESSION['Auth']['Usuario']['multicliente'])) {

                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'PerigosAspectos', $this->data['PerigosAspectos']['codigo_cliente']);

            } else {
                echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                echo $this->BForm->input('nome_fantasia', array('type' => 'text',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));

            }
        }

        ?>
    </div>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => 'Descrição (*)')) ?>

        <?php echo $this->BForm->input('codigo_risco_tipo', array('label' => 'Risco tipo (*)','class' => 'input-medium riscos_tipo', 'options'=> $combo_riscos_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

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
    jQuery('#PerigosAspectosCodigoCliente').change(function() {
        var codigo_cliente = this.value;

        comboRiscosTipo(codigo_cliente);
    });

    var comboRiscosTipo = function(codigo_cliente) {
        jQuery('.riscos_tipo').html('<option value="">Carregando...</option>');

        jQuery.ajax({
            url: baseUrl + 'perigos_aspectos/obtem_riscos_tipo_por_ajax',
            type: 'POST',
            dataType: 'html',
            data: {
                'codigo_cliente': codigo_cliente
            }
        })
            .done(function(response) {
                if (response) {
                    jQuery('.riscos_tipo').html(response);
                }
            });
    }
</script>
