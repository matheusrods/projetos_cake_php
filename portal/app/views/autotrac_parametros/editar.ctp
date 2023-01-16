<div class="formulario">
    <?php echo $this->BForm->create('AutotracParametro', array('url' => array('controller' => 'autotrac_parametros', 'action' => 'editar'))); ?>
        <div class="row-fluid inline">
            <?php //echo $this->BForm->hidden('codigo'); ?>
            <?php 
            echo $this->BForm->input(
                'taxa_administrativa', 
                array(
                    'class'   => 'input-medium moeda numeric', 
                    'label'   => 'Taxa Administrativa',                    
                    'div'     => array('class' => 'control-group input text input-prepend'),
                    'between' => '<span class="add-on">R$</span>',     
                    'type'    => 'text'
                )
            ); ?>
            
            <?php 
            echo $this->BForm->input(
                'percentual_imposto', 
                array(
                    'class' => 'input-medium moeda numeric', 
                    'label' => 'Percentual Imposto', 
                    'div'   => array('class' => 'control-group input text input-append'),
                    'after' => '<span class="add-on">%</span>',                
                    'type'  => 'text'
                )
            ); ?>           
        </div>
        
        <div class="form-actions">
          <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
        </div>    
    <?php echo $this->BForm->end() ?>
</div>
<?php echo $this->addScript($this->Javascript->codeBlock('jQuery(document).ready(function(){setup_mascaras();});')); ?>