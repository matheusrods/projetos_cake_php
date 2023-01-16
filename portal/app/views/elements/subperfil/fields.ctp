<?php //debug($this->data); ?>

<div class='well'>
    <?php if($edit_mode): ?>
        <?php echo $this->BForm->hidden('codigo'); ?>
    <?php endif; ?>

    <div class="row-fluid inline">
        <?php
        echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
        ?>
    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => 'Descrição (*)')) ?>
    </div>

    <h5>Tipo Usuário</h5>
    <div class="row-fluid">

        <div class="span12 control-group flex">
            <label for="interno" class="switch">
                <?php
                echo $this->BForm->checkbox("tipo_interno",
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=> "interno"
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 10px"><b>Interno</b></label>
        </div>

        <div class="span12 control-group flex">
            <label for="externo" class="switch">
                <?php
                echo $this->BForm->checkbox("tipo_externo",
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=> "externo"
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="padding-left: 10px"><b>Externo</b></label>
        </div>
    </div>

    <hr style="margin: 0 !important;"/>

    <h3>Permissões</h3>

    <div class="row-fluid">

        <?php

        $codigo_subperfil = isset($this->data['Subperfil']['codigo']) ? $this->data['Subperfil']['codigo'] : 'null';
        //Verifica em qual contagem do array esta para adicionar o titulo do tipo da acao
        $count_acao_tipo1 = 0;
        $count_acao_tipo2 = 0;
        $count_acao_tipo3 = 0;

            foreach ($this->data['acao_tipo'] as $key => $acao_tipo) :

                $descricao_acao_tipo = utf8_encode($acao_tipo['descricao_acao_tipo']);

                if ($acao_tipo['codigo_acao_tipo'] == 1) :

                    $count_acao_tipo1++;
                    if ($count_acao_tipo1 == 1) :

                        echo "<div class='span7 control-group r1'>";
                        echo "<h5>{$descricao_acao_tipo}</h5>";

                        ?>
                        <div class="span12 control-group flex">
                            <label for="todos1" class="switch">
                                <input type="checkbox" class="input-large" id="todos1" >
                                <span class="slider round green"></span>
                            </label>
                            <label style="padding-left: 10px"><b>Selecionar Todos</b></label>
                        </div>
                    <?php
                    endif;
                    ?>

                <div class="span12 control-group flex">
                    <label for="<?= "acao{$acao_tipo['codigo']}" ?>" class="switch">
                        <?php
                        echo $this->BForm->checkbox("acao_tipo.{$acao_tipo['codigo']}",
                            array(
                                'multiple' => 'checkbox',
                                'type'=>'checkbox',
                                'class'=>'input-large',
                                'id'=> "acao{$acao_tipo['codigo']}",
                                'value' => (isset($acao_tipo['codigo_subperfil_acao']) ? $acao_tipo['codigo_subperfil_acao'] : 'null') . '.' . $acao_tipo['codigo'] . '.' . $codigo_subperfil,
                                'checked' => !empty($acao_tipo['codigo_subperfil_acao']) ? 'checked' : '',
                            ));
                        ?>
                        <span class="slider round"></span>
                    </label>
                    <label style="padding-left: 10px"><?= utf8_encode($acao_tipo['descricao']) ?></label>
                </div>

        <?php
                endif;//Fim de acao_tipo 1

                if ($acao_tipo['codigo_acao_tipo'] == 2) :
                    $count_acao_tipo2++;

                    if ($count_acao_tipo2 == 1) :
                        echo "</div>";
                        echo "<div class='span7 control-group r2' style='margin: 0'>";
                        echo "<h5>{$descricao_acao_tipo}</h5>";
                        ?>
                        <div class="span12 control-group flex">
                            <label for="todos2" class="switch">
                                <input type="checkbox" class="input-large" id="todos2" >
                                <span class="slider round green"></span>
                            </label>
                            <label style="padding-left: 10px"><b>Selecionar Todos</b></label>
                        </div>
                        <?php
                    endif;
                    ?>

                    <div class="span12 control-group flex">
                        <label for="<?= "acao{$acao_tipo['codigo']}" ?>" class="switch">
                            <?php
                            echo $this->BForm->checkbox("acao_tipo.{$acao_tipo['codigo']}",
                                array(
                                    'multiple' => 'checkbox',
                                    'type'=>'checkbox',
                                    'class'=>'input-large',
                                    'id'=> "acao{$acao_tipo['codigo']}",
                                    'value' => (isset($acao_tipo['codigo_subperfil_acao']) ? $acao_tipo['codigo_subperfil_acao'] : 'null') . '.' . $acao_tipo['codigo'] . '.' . $codigo_subperfil,
                                    'checked' => !empty($acao_tipo['codigo_subperfil_acao']) ? 'checked' : '',
                                ));
                            ?>
                            <span class="slider round"></span>
                        </label>
                        <label style="padding-left: 10px"><?= utf8_encode($acao_tipo['descricao']) ?></label>
                    </div>

                <?php
                endif;//Fim de acao_tipo 2

                if ($acao_tipo['codigo_acao_tipo'] == 3) :
                $count_acao_tipo3++;

                if ($count_acao_tipo3 == 1) :
                    echo "</div>";
                    echo "<div class='span7 control-group r3' style='margin: 0'>";
                    echo "<h5>{$descricao_acao_tipo}</h5>";
                ?>
                    <div class="span12 control-group flex">
                        <label for="todos3" class="switch">
                            <input type="checkbox" class="input-large" id="todos3">
                            <span class="slider round green"></span>
                        </label>
                        <label style="padding-left: 10px"><b>Selecionar Todos</b></label>
                    </div>
                <?php
                endif;
                ?>

                <div class="span12 control-group flex">
                    <label for="<?= "acao{$acao_tipo['codigo']}" ?>" class="switch">
                        <?php
                        echo $this->BForm->checkbox("acao_tipo.{$acao_tipo['codigo']}",
                            array(
                                'multiple' => 'checkbox',
                                'type'=>'checkbox',
                                'class'=>'input-large',
                                'id'=> "acao{$acao_tipo['codigo']}",
                                'value' => (isset($acao_tipo['codigo_subperfil_acao']) ? $acao_tipo['codigo_subperfil_acao'] : 'null') . '.' . $acao_tipo['codigo'] . '.' . $codigo_subperfil,
                                'checked' => !empty($acao_tipo['codigo_subperfil_acao']) ? 'checked' : '',
                            ));
                        ?>
                        <span class="slider round"></span>
                    </label>
                    <label style="padding-left: 10px"><?= utf8_encode($acao_tipo['descricao']) ?></label>
                </div>

                <?php
                 endif;//Fim de acao_tipo 3
                 endforeach;//Fim do foreach acao_tipo
                ?>

        </div>
    </div>

    <hr/>

    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>

<style>
    .flex {
        display: inline-flex !important;
        margin-left: 0 !important;
    }
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

    input:checked + .slider.green {
        background-color: #448a43;
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

<script>
    $(function(){

        <?php
        if (!isset($this->data['Subperfil']['codigo'])) {
            echo '$("input:checkbox").attr("checked", "checked");';
            echo '$("#externo").removeAttr("checked");';
        } else {

            if ($this->data['Subperfil']['interno'] == 1) {
                echo '$("#interno").attr("checked", "checked");';
                echo '$("#externo").removeAttr("checked");';
            } else {
                echo '$("#externo").attr("checked", "checked");';
                echo '$("#interno").removeAttr("checked");';
            }

        }
        ?>

        $("#interno").on("change", function() {
            if ($(this).is(":checked")) {
                $("#externo").removeAttr("checked")
            }
        });

        $("#externo").on("change", function() {
            if ($(this).is(":checked")) {
                $("#interno").removeAttr("checked")
            }
        });


        $("#todos1").on("change", function() {
            var aplicaCheck = $(this).parents('.r1').find('input:checkbox');
            if (this.checked) {
                aplicaCheck.prop('checked','checked');
            } else {
                aplicaCheck.removeAttr('checked');
            }
        })

        $("#todos2").on("change", function() {
            var aplicaCheck = $(this).parents('.r2').find('input:checkbox');
            if (this.checked) {
                aplicaCheck.prop('checked','checked');
            } else {
                aplicaCheck.removeAttr('checked');
            }
        })

        $("#todos3").on("change", function() {
            var aplicaCheck = $(this).parents('.r3').find('input:checkbox');
            if (this.checked) {
                aplicaCheck.prop('checked','checked');
            } else {
                aplicaCheck.removeAttr('checked');
            }
        })
    })
</script>
