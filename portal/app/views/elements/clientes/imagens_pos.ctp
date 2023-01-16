<div class="well">
    <?php echo $this->BForm->input('codigo_documento', array('type' => 'hidden')); ?>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('codigo', array('type' => 'text', 'class' => 'input-mini', 'label' => 'Código', 'readonly' => 'readonly')); ?>

        <?php echo $this->BForm->input('nome_fantasia', array('type' => 'text', 'class' => 'input-xlarge', 'label' => 'Nome fantasia', 'readonly' => 'readonly'));?>
        <?php echo $this->BForm->input('razao_social', array('type' => 'text', 'class' => 'input-xlarge', 'label' => 'Razão social', 'readonly' => 'readonly'));?>
    </div>
</div>

<div class="well">

    <?php

    echo '<div class="control-group input clear checkbox">
            '.$this->BForm->checkbox('flag_logo_lyn',
            array(
                'type'=>'checkbox',
                'class'=>'input-large',
                'id'=>'ClienteFlagLogoLyn',
            )).'
            <label for="ClienteFlagLogoLyn">Utilizar Logo no Lyn</label>
        </div>';

    echo '<div class="control-group input clear checkbox">
            '.$this->BForm->checkbox('flag_logo_gestao_risco',
            array(
                'type'=>'checkbox',
                'class'=>'input-large',
                'id'=>'ClienteFlagLogoGestaoRisco',
            )).'
            <label for="ClienteFlagLogoGestaoRisco">Utilizar Logo no Gestão de Risco</label>
        </div>';

    if (!empty($assinaturas)) {

        foreach ($assinaturas as $ass) {

            if ($ass == 'PLANO_DE_ACAO') {
                echo '<div class="control-group input clear checkbox">
                            '.$this->BForm->checkbox('flag_pda',
                                        array(
                                            'type'=>'checkbox',
                                            'class'=>'input-large',
                                            'id'=>'ClienteFlagPda',
                                        )).'
                            <label for="ClienteFlagPda">Utilizar logo no Plano de Ação</label>
                        </div>';
            }

            if ($ass == 'SAFETY_WALK_TALK') {
                echo '<div class="control-group input clear checkbox">
                        '.$this->BForm->checkbox('flag_swt',
                                    array(
                                        'type'=>'checkbox',
                                        'class'=>'input-large',
                                        'id'=>'ClienteFlagSwt',
                                    )).'
                        <label for="ClienteFlagSwt">Utilizar logo no Safety Walk & Talk</label>
                    </div>';
            }

            if ($ass == 'SAFETY_WALK_TALK') {
                echo '<div class="control-group input clear checkbox">
                            '.$this->BForm->checkbox('flag_obs',
                                        array(
                                            'type'=>'checkbox',
                                            'class'=>'input-large',
                                            'id'=>'ClienteFlagObs',
                                        )).'
                            <label for="ClienteFlagObs">Utilizar logo no Observador EHS</label>
                        </div>';
            }
        }
    }

    echo $this->BForm->input('cor_primaria', array('label' => 'Cor Primária:', 'class' => 'jscolor'));
    echo $this->BForm->input('cor_secundaria', array('label' => 'Cor Secundária:', 'class' => 'jscolor')); 
    echo $this->BForm->input('cor_auxiliar', array('label' => 'Cor Auxiliar:', 'class' => 'jscolor')); 
    ?>
</div>
<div class="well" id="logomarca" style="display: none;"></div>

<?php echo $this->Buonny->link_js('jscolor'); ?>

<?php
    echo $this->element('upload_dinamico/simples', array('options' => array(
        "campo_upload" => array(
            "nome" => "file_1", 
            "id" => "file_1",
            "texto" => "Escolher Arquivo", 
            "model_field"=> 'Cliente.caminho_arquivo_logo'
        ),
        "pagina" => array(
            "titulo" => "Logotipo"
        ),
        "js" => array(
            "id_container" => 'upload-field-imagem', // onde sera mostrado os uploads
            "service" => "ClientesService", // servico 
            "url" => $upload['url'], // url pre carregada na controller
            "codigo" => $upload['codigo_cliente'] // identificacao pre carregada na controller
        )
    ))); 

?>

<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>

    <?php echo $html->link('Voltar', array('action' => 'logos_cores_cliente'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>

