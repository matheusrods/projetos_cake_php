<?php
App::import('Controller', 'Clientes');
//debug($produtos);
?>

<div class='well'>
    <?php if ($edit_mode) : ?>
        <?php echo $this->BForm->hidden('codigo'); ?>
    <?php endif; ?>

    <div class="row-fluid inline">
        <?php
        echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));

        // if ($is_admin) {
        //     if ($this->Buonny->seUsuarioForMulticliente()) {
        //         echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'OrigemFerramenta');
        //     } else {
        //         echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'OrigemFerramenta');
        //     }
        // } else {

        //     if (isset($_SESSION['Auth']['Usuario']['multicliente']) && !empty($_SESSION['Auth']['Usuario']['multicliente'])) {

        //         if ($this->Buonny->seUsuarioForMulticliente()) {
        //             echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'OrigemFerramenta');
        //         } else {
        //             echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'OrigemFerramenta');
        //         }
        //     } else {
        //         echo $this->BForm->input('nome_fantasia', array('type' => 'text',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia}"));

        //     }
        // }

        ?>
    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => 'Descrição (*)')) ?>
    </div>

    <hr>

    <div class="row">
        <div class="span6">
            <h4>Adicionar campos personalizados</h4>
            <div class="control-group input text">
                <div id="accordion">
                    <?php
                    if ($authUsuario['Usuario']['codigo_uperfil'] == 1) {
                        if (!empty($produtos)) {
                            foreach ($produtos as $key => $p) {
                    ?>
                                <?php if ($edit_mode && isset($p['Produto']['selecionado']) || !$edit_mode) { ?>
                                    <div class="card">
                                        <div class="card-header" id="heading<?= $key ?>">
                                            <h5 class="mb-0">
                                                <div class="btn btn-link text" data-toggle="collapse" data-target="#collapse<?= $key ?>" aria-expanded="true" aria-controls="collapse<?= $key ?>">

                                                    <div class="span6 flex">
                                                        <?php
                                                        if (!$p['Produto']['cadastrado']): 
                                                        ?>
                                                        <label class="switch">
                                                            <input 
                                                                type="checkbox" 
                                                                class="select_produto" 
                                                                name="data[Produto][]" 
                                                                class="input-large" 
                                                                id="produto_<?= $key ?>" 
                                                                value="<?= $p['Produto']['codigo'] ?>" 
                                                                <?php if (isset($p['Produto']['selecionado']))  echo " checked='checked' " ?>
                                                            >
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <?php 
                                                        endif; 
                                                        ?>
                                                        <label style="padding-left: 10px; color: #33333f;"><b><?= $p['Produto']['descricao'] ?></b></label>
                                                    </div>
                                                </div>
                                            </h5>
                                        </div>

                                        <?php 
                                        if (!$p['Produto']['cadastrado']): 
                                        ?>
                                        <div id="collapse<?= $key ?>" class="collapse <?php if (isset($p['Produto']['selecionado'])) echo " show in " ?>" aria-labelledby="heading<?= $key ?>" data-parent="#accordion">
                                            <div class="card-body">
                                                <div class="row-fluid inline">

                                                    <?php foreach ($p['OrigemFerramentaFormulario'] as $origem_ferramenta_formulario) { ?>
                                                        <div class="span12 flex select_ferramenta">
                                                            <label class="switch">
                                                                <input type="checkbox" name="data[OrigemFerramentaFormulario][]" class="input-large" data-codigo="<?= $origem_ferramenta_formulario['codigo'] ?>" id="ferramenta_<?= $origem_ferramenta_formulario['codigo'] ?>" value="<?= $origem_ferramenta_formulario['codigo'] ?>" <?php if (isset($origem_ferramenta_formulario['selecionado'])) { echo "checked='checked'"; } ?>>
                                                                <span class="slider round"></span>
                                                            </label>
                                                            <label class="ferramenta_nome" style="padding-left: 10px"><?php echo utf8_encode($origem_ferramenta_formulario['descricao']); ?></label>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                                <?php }
                            } //fim foreach
                        } //fim produto
                    } else {

                        if (!empty($produtos)) {

                            foreach ($produtos as $key => $p) {

                                if ($edit_mode && isset($p['Produto']['selecionado']) || !$edit_mode) { ?>
                                    <div class="card">
                                        <div class="card-header" id="heading<?= $key ?>">
                                            <h5 class="mb-0">
                                                <div class="btn btn-link text" data-toggle="collapse" data-target="#collapse<?= $key ?>" aria-expanded="true" aria-controls="collapse<?= $key ?>">

                                                    <div class="span6 flex">
                                                        <?php
                                                        if (!$p['Produto']['cadastrado']): 
                                                        ?>
                                                        <label class="switch">
                                                            <input type="checkbox" class="select_produto" name="data[Produto][]" class="input-large" id="produto_<?= $key ?>" value="<?= $p['Produto']['codigo'] ?>" <?php if (isset($p['Produto']['selecionado'])) echo " checked='checked' " ?>>
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <?php
                                                        endif;
                                                        ?>
                                                        <label style="padding-left: 10px; color: #33333f;"><b><?= $p['Produto']['descricao'] ?></b></label>
                                                    </div>
                                                </div>
                                            </h5>
                                        </div>
                                        <?php
                                        if (!$p['Produto']['cadastrado']): 
                                        ?>
                                        <div id="collapse<?= $key ?>" class="collapse <?php if (isset($p['Produto']['selecionado'])) echo " show in " ?>" aria-labelledby="heading<?= $key ?>" data-parent="#accordion">
                                            <div class="card-body">
                                                <div class="row-fluid inline">

                                                    <?php foreach ($p['OrigemFerramentaFormulario'] as $origem_ferramenta_formulario) { ?>
                                                        <div class="span12 flex select_ferramenta">
                                                            <label class="switch">
                                                                <input type="checkbox" name="data[OrigemFerramentaFormulario][]" class="input-large" data-codigo="<?= $origem_ferramenta_formulario['codigo'] ?>" id="ferramenta_<?= $origem_ferramenta_formulario['codigo'] ?>" value="<?= $origem_ferramenta_formulario['codigo'] ?>" <?php if (isset($origem_ferramenta_formulario['selecionado'])) {echo "checked='checked' ";} ?>>
                                                                <span class="slider round"></span>
                                                            </label>
                                                            <label class="ferramenta_nome" style="padding-left: 10px"><?php echo utf8_encode($origem_ferramenta_formulario['descricao']); ?></label>
                                                        </div>
                                                    <? } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        endif;
                                        ?>
                                    </div>
                    <?php }
                            } //fim foreach
                        } //fim produtos
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="span6">
            <h4>Campos Padrões</h4>

            <div class="row inline campos_fixos">
                <div class="span6 mb5">
                    <b>Tipo da Ação</b>
                </div>
                <div class="span6 mb5">
                    <b>Criticidade</b>
                </div>
                <div class="span6 mb5">
                    <b>Descreva o desvio</b>
                </div>
                <div class="span6 mb5">
                    <b>Descreva a ação</b>
                </div>
                <div class="span6 mb5">
                    <b>Local da ação</b>
                </div>
                <div class="span6 mb5">
                    <b>Responsável da ação</b>
                </div>
                <div class="span6 mb5">
                    <b>Prazo para conclusão</b>
                </div>
                <div class="span6 mb5">
                    <b>Status da ação</b>
                </div>

            </div>
        </div>
    </div>

    <hr />

    <?php
    if (!$of_sendo_usado) {
        echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary'));
    } else {
        echo "<div class='alert'>Esta origem de ferramenta está sendo utilizada pelo APP, não podendo ser editada!</div>";
    }
    ?>
    <?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>

<style>
    .mb5 {
        margin: 5px 30px;
    }

    .text {
        text-decoration: none !important;
    }

    #accordion {
        background-color: #FFFFFF;
        border-radius: 5px;
    }

    #accordion h5 {
        margin: 0 !important;
    }

    .card-body {
        margin: 0 55px 0;
    }

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
        -webkit-transition: .2s;
        transition: .2s;
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

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:checked+.slider.green {
        background-color: #448a43;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
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
    $(function() {

        $(".select_produto").on("change", function() {

            if ($(this).is(":checked")) {                
                $('#accordion .in').collapse('hide');
                $(this).closest("#accordion").find("input:checkbox").not(this).removeAttr("checked");
                $(".campos_fixos [id^=ferramenta_]").remove();
            } else {
                $(this).closest("#accordion").find("input:checkbox").removeAttr("checked");
                $('#accordion .in').collapse('hide');
                $(".campos_fixos [id^=ferramenta_]").remove();
            }
        });

        cardBody = function() {
            console.log('aquiiiiiiiii');
            
            // if ($(this).is(":checked")) {
            if ($(".card-body input:checkbox").is(":checked")) {

                var novo_campo = "<div id='ferramenta_" + codigo_ferramenta + "' class='span6 mb5'><b>" + ferramenta_nome + "</b></div>"
                $(".campos_fixos").append(novo_campo);
            } else {

                $(".campos_fixos #ferramenta_" + codigo_ferramenta + "").remove()
            }
        }

        $(".card-body input:checkbox").on("change", function() {

            var codigo_ferramenta = $(this).attr("data-codigo");
            var ferramenta_nome = $(this).closest(".select_ferramenta").find(".ferramenta_nome").text();

            if ($(this).is(":checked")) {
                var novo_campo = "<div id='ferramenta_" + codigo_ferramenta + "' class='span6 mb5'><b>" + ferramenta_nome + "</b></div>"
                $(".campos_fixos").append(novo_campo);
            } else {

                $(".campos_fixos #ferramenta_" + codigo_ferramenta + "").remove()
            }

            // cardBody(codigo_ferramenta,ferramenta_nome);

        });
    })
</script>