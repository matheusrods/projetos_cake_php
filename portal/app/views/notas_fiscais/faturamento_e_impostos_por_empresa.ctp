<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Notafis', array('autocomplete' => 'off', 'url' => array('controller' => 'notas_fiscais', 'action' => 'faturamento_e_impostos_por_empresa'))) ?>
        <?php echo $this->Buonny->input_periodo($this) ?>
        <?php echo $this->Buonny->input_grupo_empresas($this,$grupos_empresas,$empresas); ?>
    </div>
    <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>