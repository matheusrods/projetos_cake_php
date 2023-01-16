<div class="row-fluid inline">
    <?php //echo $this->BForm->input('unidades', array('type' => 'select',  'empty' => 'selecione..', 'label' => 'Unidades', 'class' => 'input-large')); ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente($this); ?>
    </div>
    <?php echo $this->BForm->input('tipo_arquivo', array('type' => 'select', 'options' => $tipos_arquivos, 'empty' => 'Todos', 'label' => 'Tipo de Arquivo', 'class' => 'input-large')); ?>
    <?php echo $this->BForm->input('status', array('type' => 'select', 'options' => $status, 'empty' => 'Todos', 'label' => 'Status', 'class' => 'input-large')); ?>
    <?php echo $this->BForm->input('data_de', array('class' => 'input-small data', 'label' => 'De', 'type' => 'text')); ?>
    <?php echo $this->BForm->input('data_ate', array('class' => 'input-small data', 'label' => 'Até', 'type' => 'text')); ?>
    <?php //echo $this->BForm->input('tipo_exame', array('options' => $tipos_exames, 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Tipo do exame')); ?>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        setup_datepicker();
    });
</script>