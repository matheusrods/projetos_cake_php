<div class='well'>
    <?php
    if(!empty($msg)){
        echo "<h1>{$msg}</h1>";
    }
    ?>

    <?php echo $this->BForm->create('Sistema', array('url' => array('controller' => 'sistemas', 'action' => 'relatorio_financeiro'))) ?>

        <div class="row-fluid inline control-group input text">            
            <?php echo $this->Buonny->input_periodo($this,'Sistema','data_inicial','data_final',false); ?>

            <?php echo $this->Form->input('tipos', array('required' => true, 'legend' => false, 'type' => 'select', 'multiple' => 'option','label' => false, 'options' => $tipos)); ?>
        </div>
        
        <div class="row-fluid inline"> 
            <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn ')) ?>
        </div>  
    <?php echo $this->BForm->end() ?>
</div