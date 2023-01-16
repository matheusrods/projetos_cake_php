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
        <h5>Quantidade de dias corridos para encaminhar uma ação:</h5>
        <?php echo $this->BForm->input('dias_encaminhar', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>

    <div class="row-fluid inline">
        <h5>Quantidade de dias corridos para uma ação ficar sem prazo:</h5>
        <?php echo $this->BForm->input('dias_prazo', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>

        <?php echo $this->BForm->input('status_acao_sem_prazo', array('options' => array('1' => 'Em andamento',	'2' => 'Atrasado'), 'default' => '1', 'class' => 'input-medium', 'label' => 'Status')); ?>
    </div>

    <div class="row-fluid inline">
        <h5>Quantidade de dias corridos para análise de implementação da ação:</h5>
        <?php echo $this->BForm->input('dias_analise_implementacao', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>

    <div class="row-fluid inline">
        <h5>Quantidade de dias corridos para análise da eficácia da ação:</h5>
        <?php echo $this->BForm->input('dias_analise_eficacia', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>

    <div class="row-fluid inline">
        <h5>Quantidade de dias corridos para análise de abrangência da ação:</h5>
        <?php echo $this->BForm->input('dias_analise_abrangencia', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>

    <div class="row-fluid inline">
        <h5>Quantidade de dias corridos para análise de cancelamento da ação:</h5>
        <?php echo $this->BForm->input('dias_analise_cancelamento', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>

    <div class="row-fluid inline">
        <h5>Quantidade de dias de ações a vencer:</h5>
        <?php echo $this->BForm->input('dias_a_vencer', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>

    <div class="row-fluid inline">
        <h5>Quantidade de dias corridos para aceitar uma ação:</h5>
        <?php echo $this->BForm->input('dias_a_aceitar', array('class' => 'input-medium numeric', 'label' => 'Quantidade de dias')) ?>
    </div>
</div>

<hr/>

<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
<?php echo $html->link('Voltar', array('action' => 'regras_acao'), array('class' => 'btn')); ?>
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
