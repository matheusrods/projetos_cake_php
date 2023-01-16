<?php //debug($this->data); ?>

<div class='well'>
	<?php if($edit_mode): ?>
		<?php echo $this->BForm->hidden('codigo'); ?>
	<?php endif; ?>

    <div class="row-fluid inline">
        <?php
        
        if ($is_admin) {
            if ($this->Buonny->seUsuarioForMulticliente()) {
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'RiscosImpactos', $this->data['RiscosImpactos']['codigo_cliente']);
            } else {
                echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código (*)', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'RiscosImpactos');
            }
        } else {

            if (isset($_SESSION['Auth']['Usuario']['multicliente']) && !empty($_SESSION['Auth']['Usuario']['multicliente'])) {

                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'RiscosImpactos', $this->data['RiscosImpactos']['codigo_cliente']);

            } else {
                echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => "{$codigo_cliente}"));
                echo $this->BForm->input('nome_fantasia', array('type' => 'text',  'label' => 'Cliente', 'readonly' => 'readonly', 'value' => "{$nome_fantasia['Cliente']['nome_fantasia']}"));

            }
        }

        ?>
    </div>

	<div class="row-fluid inline">
		<?php echo $this->BForm->input('descricao', array('class' => 'input-large', 'placeholder' => 'Descrição', 'label' => 'Descrição (*)')) ?>

        <?php echo $this->BForm->input('codigo_perigo_aspecto', array('label' => 'Perigo/aspecto(*)','class' => 'input-medium perigos_aspectos', 'options'=> $combo_perigos_aspectos, 'empty' => 'Todos', 'default' => ' ')); ?>

        <?php echo $this->BForm->input('codigo_risco_impacto_tipo', array('label' => 'Risco/impacto tipo(*)','class' => 'input-medium', 'options'=> $combo_riscos_impactos_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

        <?php echo $this->BForm->input('codigo_metodo_tipo', array('label' => 'Tipo de metodo(*)','class' => 'input-medium', 'options'=> $combo_metodos_tipo, 'empty' => 'Todos', 'default' => ' ')); ?>

    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->input('unidade_medida', array('class' => 'input-large', 'placeholder' => 'Unidade de medida', 'label' => 'Unidade de medida')) ?>

        <?php echo $this->BForm->input('meio_propagacao', array('class' => 'input-large', 'placeholder' => 'Meio de propagação', 'label' => 'Meio de propagação')) ?>

        <?php echo $this->BForm->input('nivel_acao', array('class' => 'input-large', 'placeholder' => 'Nível de ação', 'label' => 'Nível de ação')) ?>

        <?php echo $this->BForm->input('limite_tolerancia', array('class' => 'input-large', 'placeholder' => 'Limite de tolerância', 'label' => 'Limite de tolerância')) ?>
    </div>

    <div class="row-fluid inline">
        <div class="control-group input clear" style="display: inline-flex">

            <label for="RiscoCaracterizadoPorAltura" class="switch">
                <?php
                echo $this->BForm->checkbox('risco_caracterizado_por_altura',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'RiscoCaracterizadoPorAltura',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Risco Caracterizado por Altura</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="RiscoCaracterizadoPorTrabalhoConfinado" class="switch">
                <?php
                echo $this->BForm->checkbox('risco_caracterizado_por_trabalho_confinado',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'RiscoCaracterizadoPorTrabalhoConfinado',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Risco Caracterizado por Trabalho Confinado</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="RiscoCaracterizadoPorRuido" class="switch">
                <?php
                echo $this->BForm->checkbox('risco_caracterizado_por_ruido',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'RiscoCaracterizadoPorRuido',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Risco Caracterizado por Ruído</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="RiscoCaracterizadoPorCalor" class="switch">
                <?php
                echo $this->BForm->checkbox('risco_caracterizado_por_calor',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'RiscoCaracterizadoPorCalor',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Risco Caracterizado por Calor</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="AusenciaDeRisco" class="switch">
                <?php
                echo $this->BForm->checkbox('ausencia_de_risco',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'AusenciaDeRisco',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Ausência de Risco</label>
        </div>

    </div>

    <hr/>
    <h4>RELATÓRIOS</h4>
    <div class="row-fluid inline">
        <div class="control-group input clear" style="display: inline-flex">

            <label for="Aso" class="switch">
                <?php
                echo $this->BForm->checkbox('aso',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'Aso',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Aso</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="Convocacao" class="switch">
                <?php
                echo $this->BForm->checkbox('convocacao',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'Convocacao',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Convocação/Pedido de Exame</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="NocivoPpp" class="switch">
                <?php
                echo $this->BForm->checkbox('nocivo_ppp',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'NocivoPpp',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Nocivo - PPP</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="OrdemServiço" class="switch">
                <?php
                echo $this->BForm->checkbox('ordem_servico',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'OrdemServiço',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Ordem de serviço</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="Pcmso" class="switch">
                <?php
                echo $this->BForm->checkbox('pcmso',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'Pcmso',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">PCMSO</label>
        </div>

        <div class="control-group input clear" style="display: inline-flex">

            <label for="Ppra" class="switch">
                <?php
                echo $this->BForm->checkbox('ppra',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'Ppra',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">PGR</label>
        </div>
    </div>

    <hr/>
    <h4>CLASSIFICAÇÃO</h4>
    <div class="row-fluid ">
        <div class="span3 control-group input" style="display: inline-flex">

            <label for="Periculoso" class="switch">
                <?php
                echo $this->BForm->checkbox('periculoso',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'Periculoso',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Periculoso</label>
        </div>

        <div class="span3 control-group input" style="display: inline-flex">

            <label for="Insalubridade" class="switch">
                <?php
                echo $this->BForm->checkbox('insalubridade',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'Insalubridade',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Insalubridade</label>
        </div>

        <div class="span3 control-group input" style="display: inline-flex">

            <label for="AposentadoriaEspecial" class="switch">
                <?php
                echo $this->BForm->checkbox('aposentadoria_especial',
                    array(
                        'type'=>'checkbox',
                        'class'=>'input-large',
                        'id'=>'AposentadoriaEspecial',
                    ));
                ?>
                <span class="slider round"></span>
            </label>
            <label style="margin-left: 10px">Aposentadoria especial</label>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span3">
        </div>

        <div class="span3">
            <div class="grauInsalubridade" style="display: none">
                <?php echo $this->BForm->input('grau_insalubridade', array('class' => 'input-large', 'placeholder' => 'Grau de insalubridade', 'label' => 'Grau de insalubridade')) ?>
            </div>
        </div>

        <div class="span3">
            <div class="tempoAtividade" style="display: none">
                <?php echo $this->BForm->input('tempo_atividade', array('class' => 'input-large', 'placeholder' => 'Tempo de atividade', 'label' => 'Tempo de atividade')) ?>
            </div>
        </div>
    </div>

    <hr/>

    <?php if (!isset($adicionar_novo)):?>
        <div class="row">
        <div class="span3" style="display:inline-flex">
            <h4>Vincular risco ao E-SOCIAL:</h4>
            <a href="#myModal" title="Buscar e-Social" role="button" data-toggle="modal"><span class="icon-search" style="margin-top: 23px;margin-left: 10px"></span></a>

        </div>

        <div id="tbodyRiscoAssociado" class="span10">
            <?php if (!empty($riscosesocial)) : ?>
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="input-medium">Códigos</th>
                        <th class="input-xxlarge">Nome agente</th>
                        <th class="input-xlarge">Código agente nocivo e-Social</th>
                        <th class="input-xlarge">Grupo risco</th>
                    </tr>
                    </thead>

                    <tbody >
                    <?php foreach ($riscosesocial as $dados): ?>
                        <tr>
                            <td class="input-mini"><?php echo $dados['codigo'] ?></td>

                            <td class="input-xxlarge"><?php echo $dados['nome_agente'] ?></td>

                            <td class="input-xlarge"><?php echo $dados['codigo_agente_nocivo_esocial']; ?></td>

                            <td class="input-mini"><?php echo $dados['descricao']; ?></td>

                        </tr>
                    <?php endforeach ?>

                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
            <?php else: ?>
                <h5>Nenhum vinculo de e-Social associado a este risco</h5>
            <?php endif; ?>
        </div>
    </div>

        <hr/>
    <?php endif; ?>

	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<!-- Se for modo adicionar, não exibe modal para vincular risco -->
<?php if (!isset($adicionar_novo)):?>
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="min-width: 60% !important;">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Lista de Riscos do e-Social</h3>
    </div>
    <div class="modal-body">

        <div class="form-procurar">
            <div class="well">
                <div id="filtros">
                    <form id="riscoEsocialForm" method="post" accept-charset="utf-8" onkeydown="return event.key != 'Enter';">
                        <div class="row-fluid inline">
                            <div class="control-group input text">
                                <label for="RiscosEsocialCodigo">Código</label>
                                <input name="codigo" type="text" class="input-small just-number" placeholder="Código" id="RiscosEsocialCodigo">
                            </div>

                            <div class="control-group input text required">
                                <label for="RiscosEsocialRisco">Nome agente</label>
                                <input name="risco" type="text" class="input-xlarge" placeholder="Risco" maxlength="255" id="RiscosEsocialRisco">
                            </div>

                            <div class="control-group input text required">
                                <label for="RiscosEsocialCodigoEsocial">Código agente nocivo e-Social</label>
                                <input name="codigo_esocial" type="text" class="input-xlarge " placeholder="Código e-Social" id="RiscosEsocialCodigoEsocial">
                            </div>

                            <div class="control-group input select required">
                                <label for="RiscosEsocialCodigoGrupoRisco">Grupo risco</label>
                                <select name="codigo_grupo_risco" class="input-xlarge" id="RiscosEsocialCodigoGrupoRisco">
                                    <option value="">Todos</option>
                                    <option value="1">FÍSICOS</option>
                                    <option value="2">QUÍMICOS</option>
                                    <option value="3">BIOLÓGICOS</option>
                                    <option value="5">ERGONÔMICOS</option>
                                    <option value="6">ACIDENTES</option>
                                    <option value="8">OUTROS</option>
                                    <option value="9">MECÂNICOS/ACIDENTES</option>
                                    <option value="10">PERICULOSOS</option>
                                    <option value="11">PENOSOS</option>
                                    <option value="13">AUSÊNCIA DE FATORES DE RISCO</option>
                                    <option value="14">ERGONÔMICOS - AMBIENTAIS</option>
                                    <option value="15">ERGONÔMICOS - BIOMECÂNICOS</option>
                                    <option value="16">ERGONÔMICOS - MOBILIÁRIO E EQUIPAMENTOS</option>
                                    <option value="17">ERGONÔMICOS - ORGANIZACIONAIS</option>
                                    <option value="18">ERGONÔMICOS - PSICOSSOCIAIS / COGNITIVOS</option>
                                    <option value="19">PERIGOSOS</option>
                                </select>
                            </div>
                        </div>

                        <input id="buscarRiscoEsocial" type="button" class="btn" value="Buscar">
                        <button id="limparRiscoEsocial" class="btn">Limpar busca</button>
                    </form>
                </div>
            </div>
            <?php echo $paginator->options(array('update' => 'div.modal-body')); ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th ></th>
                <th class="input-medium">Códigos</th>
                <th class="input-xxlarge">Nome agente</th>
                <th class="input-xlarge">Código agente nocivo e-Social</th>
                <th class="input-xlarge">Grupo risco</th>
            </tr>
            </thead>

            <tbody id="tbodyRisco">
<!--            --><?php //foreach ($riscos_esocial as $dados): ?>
<!--                <tr>-->
<!--                    <td class="input-mini">-->
<!--                        <input type="checkbox" class="checkbox" id="--><?php //echo $dados['Risco']['codigo'] ?><!--" value="--><?php //echo $dados['Risco']['codigo'] ?><!--">-->
<!--                    </td>-->
<!--                    <td class="input-mini">--><?php //echo $dados['Risco']['codigo'] ?><!--</td>-->
<!---->
<!--                    <td class="input-xxlarge">--><?php //echo $dados['Risco']['nome_agente'] ?><!--</td>-->
<!---->
<!--                    <td class="input-xlarge">--><?php //echo $dados['Risco']['codigo_agente_nocivo_esocial']; ?><!--</td>-->
<!---->
<!--                    <td class="input-mini">--><?php //echo $dados['GruposRiscos']['descricao']; ?><!--</td>-->
<!---->
<!--                </tr>-->
<!--            --><?php //endforeach ?>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Risco']['count']; ?>
                </td>
            </tr>

            </tfoot>
        </table>

            <div class='counter'>
                <div class='row-fluid' style="display: inline-flex">
                    <div class='numbers span5'>
                        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
                        <?php echo $this->Paginator->numbers(); ?>
                        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
                    </div>
                    <div class='counter span6'>
                        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
                    </div>
                </div>
            </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <button id="salvarRiscoEsocial" class="btn btn-primary">Enviar</button>
    </div>
</div>
</div>
<?php  endif; ?>

<!-- Recebe value do checkbox selecionado para ser vinculado ao risco -->
<input name="valorDoCheckbox" id="valorDoCheckbox" type="hidden" value="">

<?php

echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
	});
'); ?>

<script type="text/javascript">

    //Impede de de submeter formulário ao clicar 'Enter' em qualquer input da tela
    $(document).on("keydown", "form", function(event) {
        return event.key != "Enter";
    });


    $(".counter a").unbind('click').bind('click', function(e) {
        e.preventDefault();


        var url = $(this).attr('href');
        var urlsplit = url.split("/");
        var lastpart = urlsplit[urlsplit.length-1];
        if(lastpart==='')
        {
            lastpart = urlsplit[urlsplit.length-2];
        }

        // alert(lastpart)
        carregarRiscos(lastpart)

        return false;
    })

    jQuery('#RiscosImpactosCodigoCliente').change(function() {
        var codigo_cliente = this.value;

        comboPerigosAspectos(codigo_cliente);
    });

    var comboPerigosAspectos = function(codigo_cliente) {
        jQuery('.perigos_aspectos').html('<option value="">Carregando...</option>');

        jQuery.ajax({
            url: baseUrl + 'riscos_impactos/obtem_perigos_aspectos_por_ajax',
            type: 'POST',
            dataType: 'html',
            data: {
                'codigo_cliente': codigo_cliente
            }
        })
            .done(function(response) {
                if (response) {
                    jQuery('.perigos_aspectos').html(response);
                }
            });
    }

    if ($("#Insalubridade").is(":checked") == true) {
        $(".grauInsalubridade").show()
    } else {
        $(".grauInsalubridade").hide();
        $("#RiscosImpactosGrauInsalubridade").val("")
    }

    $("#Insalubridade").click(function(){

        if ($(this).is(":checked") == true) {
            $(".grauInsalubridade").show()
        } else {
            $(".grauInsalubridade").hide();
            $("#RiscosImpactosGrauInsalubridade").val("")

        }
    });

    $("#AposentadoriaEspecial").click(function(){

        if ($(this).is(":checked") == true) {
            $(".tempoAtividade").show();
        } else {
            $(".tempoAtividade").hide();
            $("#RiscosImpactosTempoAtividade").val("")
        }
    });

    if ($("#AposentadoriaEspecial").is(":checked") == true) {
        $(".tempoAtividade").show();
    } else {
        $(".tempoAtividade").hide();
        $("#RiscosImpactosTempoAtividade").val("")
    }

    // Função para permitir que apenas 1 checkbox seja selecionado
    $('.checkbox').on("click", function() {
        $('.checkbox').not(this).prop('checked', false);

        $("#valorDoCheckbox").val($(this).val());
    });

    //Listagem do modal de riscos do e-social
    function atualizaListaRiscosEsocial() {

        var div = jQuery('#myModal table');
        bloquearDiv(div);
        div.load(baseUrl + 'riscos_esocial/listagem/' + Math.random());
    }

    function atualizaStatusRiscosEsocial(codigo)
    {

        $.ajax({
            type: 'POST',
            url: baseUrl + 'riscos_esocial/editar_status/' + codigo,
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));
            },
            success: function(data){
                if(data == 1){
                    atualizaListaRiscosEsocial();
                    $('div.lista').unblock();
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaListaRiscosEsocial();
                    $('div.lista').unblock();
                    viewMensagem(0,'Não foi possível mudar o status!');
                }
            },
            error: function(erro){
                $('div.lista').unblock();
                viewMensagem(0,'Não foi possível mudar o status!');
            }
        });
    }

    carregarRiscos();

    function carregarRiscos(page)
    {

        var div = jQuery('#myModal table');
        bloquearDiv(div);
        $.ajax({
            type: "GET",
            url: baseUrl + 'riscos/listar_riscos/' + page + '/',

            dataType: "json",
            success: function(data) {
                console.log(data)

                var qtdRows = data.length;

                let tr = "";

                for (var i=0;i < qtdRows;i++) {

                    tr += "<tr>" +
                        "<td class='input-mini'>" +
                        "<input id='"+ data[i]['Risco'].codigo +"' type='checkbox' class='checkbox' value='"+ data[i]['Risco'].codigo +"'>" +
                        "</td>" +
                        "<td class='input-mini'>" + data[i]['Risco'].codigo + "</td>" +
                        "<td class='input-xxlarge'>" + data[i]['Risco'].nome_agente + "</td>" +
                        "<td class='input-xlarge'>" + data[i]['Risco'].codigo_agente_nocivo_esocial + "</td>" +
                        "<td class='input-mini'>" + data[i]['GruposRiscos'].descricao + "</td>" +
                        "</tr>"
                }


                $("#tbodyRisco tr").remove();
                $("#tbodyRisco").append(tr);
                $('#tbodyRisco').append("<script> $(function(){$('.checkbox').on('click',function(){$('.checkbox').not(this).prop('checked',false); $('#valorDoCheckbox').val($(this).val())})}) <\/script>");


                div.unblock();
            },
            error: function() {
                alert('error handling here');
            }
        });
    }


    $("#buscarRiscoEsocial").click(function(){

        var div = jQuery('#myModal table');
        bloquearDiv(div);
        $.ajax({
            type: "POST",
            url: baseUrl + 'riscos/filtrar/',
            data: {
                "codigo": $("#RiscosEsocialCodigo").val(),
                "nome_agente": $("#RiscosEsocialRisco").val(),
                "codigo_agente_nocivo_esocial": $("#RiscosEsocialCodigoEsocial").val(),
                "codigo_grupo": $("#RiscosEsocialCodigoGrupoRisco").val()

            },
            dataType: "json",
            success: function(data) {
                console.log(data)

                var qtdRows = data.length;

                let tr = "";

                for (var i=0;i < qtdRows;i++) {

                    tr += "<tr>" +
                            "<td class='input-mini'>" +
                            "<input id='"+ data[i]['Risco'].codigo +"' type='checkbox' class='checkbox' value='"+ data[i]['Risco'].codigo +"'>" +
                            "</td>" +
                            "<td class='input-mini'>" + data[i]['Risco'].codigo + "</td>" +
                            "<td class='input-xxlarge'>" + data[i]['Risco'].nome_agente + "</td>" +
                            "<td class='input-xlarge'>" + data[i]['Risco'].codigo_agente_nocivo_esocial + "</td>" +
                            "<td class='input-mini'>" + data[i]['GruposRiscos'].descricao + "</td>" +
                        "</tr>";
                }

                $("#tbodyRisco tr").remove();
                $("#tbodyRisco").append(tr);
                $('#tbodyRisco').append("<script> $(function(){$('.checkbox').on('click',function(){$('.checkbox').not(this).prop('checked',false); $('#valorDoCheckbox').val($(this).val())})}) <\/script>");

                div.unblock();
            },
            error: function() {
                alert('error handling here');
            }
        });
    });

    $("#limparRiscoEsocial").click(function(e){

        e.preventDefault();
        this.form.reset(); //Limpa formulário de busca
        $("#valorDoCheckbox").val(""); //Limpa value do input hidden que armazena o codigo do e-social que será vinculado

        var div = jQuery('#myModal table');
        bloquearDiv(div);
        $.ajax({
            type: "POST",
            url: baseUrl + 'riscos/filtrar/',
            data: {
                "codigo": "",
                "nome_agente": "",
                "codigo_agente_nocivo_esocial": "",
                "codigo_grupo": ""
            },
            dataType: "json",
            success: function(data) {
                console.log(data)

                var qtdRows = data.length;

                let tr = "";

                for (var i=0;i < qtdRows;i++) {

                    tr += "<tr>" +
                        "<td class='input-mini'>" +
                        "<input id='"+ data[i]['Risco'].codigo +"' type='checkbox' class='checkbox' value='"+ data[i]['Risco'].codigo +"'>" +
                        "</td>" +
                        "<td class='input-mini'>" + data[i]['Risco'].codigo + "</td>" +
                        "<td class='input-xxlarge'>" + data[i]['Risco'].nome_agente + "</td>" +
                        "<td class='input-xlarge'>" + data[i]['Risco'].codigo_agente_nocivo_esocial + "</td>" +
                        "<td class='input-mini'>" + data[i]['GruposRiscos'].descricao + "</td>" +
                        "</tr>"
                }


                $("#tbodyRisco tr").remove();
                $("#tbodyRisco").append(tr);
                $('#tbodyRisco').append("<script> $(function(){$('.checkbox').on('click',function(){$('.checkbox').not(this).prop('checked',false); $('#valorDoCheckbox').val($(this).val())})}) <\/script>");


                div.unblock();
            },
            error: function() {
                alert('error handling here');
            }
        });
    })

    $("#salvarRiscoEsocial").click(function(e){

        $(this).attr("disabled", true);
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: baseUrl + 'riscos_impactos/relacionar_risco_impacto_esocial/',
            data: {
                "data[RiscosImpactos][codigo_risco_impacto]": <?php echo isset($codigo) ?  $codigo : 0 ; ?>,
                "data[RiscosEsocial][codigo_risco]": $("#valorDoCheckbox").val(),
            },
            dataType: "json",
            success: function(data) {

                console.log(data['riscos'])
                if(data['result'] === 1){
                    $("#myModal .close").click();

                    $("#salvarRiscoEsocial").attr("disabled", false);

                    let tr = "";

                    tr += "<table class='table table-striped'>" +
                    "<thead>" +
                    "<tr>" +
                    "<th class='input-medium'>Códigos</th>" +
                    "<th class='input-xxlarge'>Nome agente</th>" +
                    "<th class='input-xlarge'>Código agente nocivo e-Social</th>" +
                    "<th class='input-xlarge'>Grupo risco</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody >";

                    tr += "<tr>" +
                        "<td class='input-mini'>" + data['riscos'][0].codigo + "</td>" +
                        "<td class='input-xxlarge'>" + data['riscos'][0].nome_agente + "</td>" +
                        "<td class='input-xlarge'>" + data['riscos'][0].codigo_agente_nocivo_esocial + "</td>" +
                        "<td class='input-mini'>" + data['riscos'][0].descricao + "</td>" +
                        "</tr>";

                    tr += "</tbody>" +
                        "<tfoot>" +
                        "</tfoot>" +
                        "</table>";

                    $("#tbodyRiscoAssociado table, #tbodyRiscoAssociado h5").remove();
                    $("#tbodyRiscoAssociado").append(tr);

                    viewMensagem(2,'E-Social vinculado com sucesso!');
                } else {
                    viewMensagem(0,'Não foi possível salvar!');
                }

            },
            error: function() {
                // $('div.lista').unblock();
                viewMensagem(0,'Não foi possível salvar!');
            }
        })
    })

    function fecharMsg()
    {
        setInterval(
            function(){
                $('div.message.container').css({ 'opacity': '0', 'display': 'none' });
            },
            4000
        );
    }

    function gerarMensagem(css, mens)
    {
        $('div.message.container').css({ 'opacity': '1', 'display': 'block' });
        $('div.message.container').html('<div class=\"alert alert-'+css+'\"><p>'+mens+'</p></div>');
        fecharMsg();
    }

    function viewMensagem(tipo, mensagem){
        switch(tipo){
            case 1:
                gerarMensagem('success',mensagem);
                break;
            case 2:
                gerarMensagem('success',mensagem);
                break;
            default:
                gerarMensagem('error',mensagem);
                break;
        }
    }

</script>


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

    .modal {
        left: 35% !important;
    }
</style>
