<?php //debug($this->data); ?>

<div class='well'>
    <div class="row-fluid inline">
        <?php
        echo $this->BForm->input('codigo_cliente', array('type' => 'text', 'label' => 'Código', 'readonly' => 'readonly', 'value' => "{$codigo_cliente}"));
        echo $this->BForm->input('nome_fantasia', array('type' => 'text',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));
        ?>
    </div>
</div>

<h4>Configurações</h4>

<div class="well">
    <div class="row-fluid inline">
        <h5>Limite de dias corridos para registro retroativo:</h5>
        <?php echo $this->BForm->input('dias_registro_retroativo', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>
</div>

<hr/>

<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
<?php echo $html->link('Voltar', array('action' => 'configuracao_swt'), array('class' => 'btn', "style" => "margin-left: 10px;")); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>

<script>
    $(function(){

        $(document).on("input", ".numeric", function() {
            this.value = this.value.replace(/\D/g,'');
        });
    })
</script>
