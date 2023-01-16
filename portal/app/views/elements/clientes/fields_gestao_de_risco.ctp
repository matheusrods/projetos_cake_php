<?php //debug($this->data); ?>

<div class='well'>
    <?php if($edit_mode): ?>
        <?php echo $this->BForm->hidden('codigo'); ?>
    <?php endif; ?>

    <div class="row-fluid inline">
        <?php

            echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
            echo $this->BForm->input('codigo_documento', array('type' => 'hidden', 'value' => "{$this->data['Cliente']['codigo_documento']}"));
            echo $this->BForm->input('nome_fantasia', array('type' => 'text',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));

        ?>
    </div>

    <div class="row-fluid inline">
        <div class="control-group input clear">

            <label>Método de definição probabilidade por meio de perguntas</label>
            <label for="FlagMetodoHazop" class="switch">
                <?php
                    echo $this->BForm->checkbox('flag_metodo_hazop',
                        array(
                            'type'=>'checkbox',
                            'class'=>'input-large',
                            'id'=>'FlagMetodoHazop',
                        ));
                    ?>
                <span class="slider round"></span>
            </label>

        </div>
    </div>

    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', array('action' => 'visualizar_clientes_gestao_de_risco'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 45px;
        height: 24px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(20px);
        -ms-transform: translateX(20px);
        transform: translateX(20px);
    }

    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
