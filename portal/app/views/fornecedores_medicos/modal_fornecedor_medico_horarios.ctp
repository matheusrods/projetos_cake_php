<?php echo $this->Buonny->link_css('jqueryui/hot-sneaks/jquery-ui-1.9.2.custom.css'); ?>
<?php echo $this->Buonny->link_js('jqueryui/jquery-ui-1.9.2.custom'); ?>

<div class="modal-dialog modal-sm" style="position: static;">
    <div class="modal-content" id="modal_horario">
        <div class="modal-header" style="text-align: center;">
            <h3>Médico calendário - <?= $profissional['Medico']['nome']; ?></h3>
        </div>

        <div class="modal-body" style="min-height: 295px;max-height: 360px;">
            <div class='row horarios'>
                <div class='span1'>
                    <b>Segunda</b>
                </div>
                <div class='span4'>
                    <div class='row'>
                        <div class='span1'>
                            <label>Início</label>
                            <input name="hora_inicio_manha" class='span1 hora' type='text' value='<?php
                                                                                                    foreach ($horarios as $row) {
                                                                                                        if ($row[0]['dia_semana'] == 1) echo $row[0]['hora_inicio_manha'];
                                                                                                    };
                                                                                                    ?>'>
                        </div>
                        <div class='span1'>
                            <label>Almoço</label>
                            <input name="hora_fim_manha" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 1) echo $row[0]['hora_fim_manha'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <div class='span1'>
                            <label>Retorno</label>
                            <input name="hora_inicio_tarde" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 1) echo $row[0]['hora_inicio_tarde'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Fim</label>
                            <input name="hora_fim_tarde" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 1) echo $row[0]['hora_fim_tarde'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <input name="dia_semana" type="hidden" value="1">
                    </div>
                </div>
            </div>
            <hr>
            <div class='row horarios'>
                <div class='span1'>
                    <b>Terça</b>
                </div>
                <div class='span4'>
                    <div class='row'>
                        <div class='span1'>
                            <label>Início</label>
                            <input name="hora_inicio_manha" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 2) echo $row[0]['hora_inicio_manha'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Almoço</label>
                            <input name="hora_fim_manha" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 2) echo $row[0]['hora_fim_manha'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <div class='span1'>
                            <label>Retorno</label>
                            <input name="hora_inicio_tarde" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 2) echo $row[0]['hora_inicio_tarde'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Fim</label>
                            <input name="hora_fim_tarde" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 2) echo $row[0]['hora_fim_tarde'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <input name="dia_semana" type="hidden" value="2">
                    </div>
                </div>
            </div>
            <hr>
            <div class='row horarios'>
                <div class='span1'>
                    <b>Quarta</b>
                </div>
                <div class='span4'>
                    <div class='row'>
                        <div class='span1'>
                            <label>Início</label>
                            <input name="hora_inicio_manha" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 3) echo $row[0]['hora_inicio_manha'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Almoço</label>
                            <input name="hora_fim_manha" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 3) echo $row[0]['hora_fim_manha'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <div class='span1'>
                            <label>Retorno</label>
                            <input name="hora_inicio_tarde" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 3) echo $row[0]['hora_inicio_tarde'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Fim</label>
                            <input name="hora_fim_tarde" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 3) echo $row[0]['hora_fim_tarde'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <input name="dia_semana" type="hidden" value="3">
                    </div>
                </div>
            </div>
            <hr>
            <div class='row horarios'>
                <div class='span1'>
                    <b>Quinta</b>
                </div>
                <div class='span4'>
                    <div class='row'>
                        <div class='span1'>
                            <label>Início</label>
                            <input name="hora_inicio_manha" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 4) echo $row[0]['hora_inicio_manha'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Almoço</label>
                            <input name="hora_fim_manha" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 4) echo $row[0]['hora_fim_manha'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <div class='span1'>
                            <label>Retorno</label>
                            <input name="hora_inicio_tarde" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 4) echo $row[0]['hora_inicio_tarde'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Fim</label>
                            <input name="hora_fim_tarde" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 4) echo $row[0]['hora_fim_tarde'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <input name="dia_semana" type="hidden" value="4">
                    </div>
                </div>
            </div>
            <hr>
            <div class='row horarios'>
                <div class='span1'>
                    <b>Sexta</b>
                </div>
                <div class='span4'>
                    <div class='row'>
                        <div class='span1'>
                            <label>Início</label>
                            <input name="hora_inicio_manha" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 5) echo $row[0]['hora_inicio_manha'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Almoço</label>
                            <input name="hora_fim_manha" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 5) echo $row[0]['hora_fim_manha'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <div class='span1'>
                            <label>Retorno</label>
                            <input name="hora_inicio_tarde" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 5) echo $row[0]['hora_inicio_tarde'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Fim</label>
                            <input name="hora_fim_tarde" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 5) echo $row[0]['hora_fim_tarde'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <input name="dia_semana" type="hidden" value="5">
                    </div>
                </div>
            </div>
            <hr>
            <div class='row horarios'>
                <div class='span1'>
                    <b>Sábado</b>
                </div>
                <div class='span4'>
                    <div class='row'>
                        <div class='span1'>
                            <label>Início</label>
                            <input name="hora_inicio_manha" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 6) echo $row[0]['hora_inicio_manha'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Almoço</label>
                            <input name="hora_fim_manha" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 6) echo $row[0]['hora_fim_manha'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <div class='span1'>
                            <label>Retorno</label>
                            <input name="hora_inicio_tarde" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 6) echo $row[0]['hora_inicio_tarde'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Fim</label>
                            <input name="hora_fim_tarde" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 6) echo $row[0]['hora_fim_tarde'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <input name="dia_semana" type="hidden" value="6">
                    </div>
                </div>
            </div>
            <hr>
            <div class='row horarios'>
                <div class='span1'>
                    <b>Domingo</b>
                </div>
                <div class='span4'>
                    <div class='row'>
                        <div class='span1'>
                            <label>Início</label>
                            <input name="hora_inicio_manha" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 7) echo $row[0]['hora_inicio_manha'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Almoço</label>
                            <input name="hora_fim_manha" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 7) echo $row[0]['hora_fim_manha'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <div class='span1'>
                            <label>Retorno</label>
                            <input name="hora_inicio_tarde" class='span1' type='text' value='<?php
                                                                                                foreach ($horarios as $row) {
                                                                                                    if ($row[0]['dia_semana'] == 7) echo $row[0]['hora_inicio_tarde'];
                                                                                                };
                                                                                                ?>'>
                        </div>
                        <div class='span1'>
                            <label>Fim</label>
                            <input name="hora_fim_tarde" class='span1' type='text' value='<?php
                                                                                            foreach ($horarios as $row) {
                                                                                                if ($row[0]['dia_semana'] == 7) echo $row[0]['hora_fim_tarde'];
                                                                                            };
                                                                                            ?>'>
                        </div>
                        <input name="dia_semana" type="hidden" value="7">
                    </div>
                </div>
            </div>
            <hr>
        </div>

        <div class="modal-footer">
            <div class="right">
                <a href="javascript:void(0);" onclick="horariosMedico(<?php echo $codigo_fornecedor; ?>,<?php echo $codigo_medico; ?>, 0);" class="btn btn-danger">FECHAR</a>
                <a id="horarioLimparSalvar" href="javascript:void(0);" onclick="salvar_horario(<?php echo $codigo_fornecedor; ?>,<?php echo $codigo_medico; ?>, 1);" class="btn btn-warning">LIMPAR E SALVAR</a>
                <a id="horarioSalvar" href="javascript:void(0);" onclick="salvar_horario(<?php echo $codigo_fornecedor; ?>,<?php echo $codigo_medico; ?>, 0);" class="btn btn-success">SALVAR</a>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {

        $(document).on('keyup', 'input', function(e) {
            e.preventDefault();
            var text = $(this).val();
            $(this).attr('value', text);
        })

        //Add mascara para os inputs de inserção de hora
        jQuery(".horarios input[name^='hora_']").mask("99:99");
    });

    function horariosMedico(codigo_fornecedor, codigo_medico, mostra) {

        if (mostra) {

            var div = jQuery('div#modal_horario');
            bloquearDiv(div);
            div.load(baseUrl + 'fornecedores_medicos/modal_fornecedor_medico_horarios/' + codigo_fornecedor + '/' + codigo_medico + '/' + Math.random());

            $('#modal_horario').css('z-index', '1050');
            $('#modal_horario').modal('show');
        } else {
            $('#modal_horario').modal('hide');
        }
    }

    function salvar_horario(codigo_fornecedor, codigo_medico, limpar) {

        if (limpar) {
            $('.horarios input').val('');
            console.log('aqui para limpar')
        }
        var div = jQuery('#modal_horario');
        bloquearDiv(div);

        var form_array = [];
        var form = {
            "codigo_fornecedor": codigo_fornecedor,
            "codigo_medico": codigo_medico,
            "calendario": ""
        }

        $('.horarios').each(function(index, element) {

            var obj = {
                hora_inicio_manha: $(this).find("input[name^='hora_inicio_manha']").attr('value'),
                hora_fim_manha: $(this).find("input[name^='hora_fim_manha']").attr('value'),
                hora_inicio_tarde: $(this).find("input[name^='hora_inicio_tarde']").attr('value'),
                hora_fim_tarde: $(this).find("input[name^='hora_fim_tarde']").attr('value'),
                dia_semana: $(this).find("input[name^='dia_semana']").attr('value'),
            }

            form_array.push(obj);

        });

        form["calendario"] = form_array;

        var total_form = {
            form: form
        }

        //envia via ajax a data de realizacao
        $.ajax({
            url: baseUrl + 'fornecedores_medicos/salvar_horarios',
            type: 'POST',
            dataType: 'json',
            data: total_form,
            success: function(data) {
                console.log('sucesso');
                console.log(data);
                swal({
                    type: 'success',
                    title: 'Sucesso',
                    text: 'Dados atualizados com sucesso.'
                });
                desbloquearDiv(div);
            },
            error: function(error) {
                console.log('erro');
                console.log(error);
                swal({
                    type: 'warning',
                    title: 'Atenção',
                    text: error,
                });
                desbloquearDiv(div);
            }

        });
        return;
    }
</script>