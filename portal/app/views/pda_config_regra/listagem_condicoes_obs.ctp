
<?php if(isset($dados_condicoes) && count($dados_condicoes)) : ?>

    <?php if($codigo_tema == 12): //critificadade ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Criticidade</th>
                    <th class="acoes" style="width:75px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($dados_condicoes as $dados): 
                    //para edicao
                    $codigo = $dados['PdaConfigRegraCondicao']['codigo'];
                    $codigo_pda_config_regra = $dados['PdaConfigRegraCondicao']['codigo_pda_config_regra'];
                    $codigo_cliente = $dados['PdaConfigRegraCondicao']['codigo_cliente'];
                    $codigo_tema = $dados["PdaConfigRegra"]["codigo_pda_tema"];
                    
                    $ativo = $dados['PdaConfigRegraCondicao']['ativo'];
                ?>
                    <tr>
                        <td><?php echo $dados['PosCriticidade']['descricao'] ?></td>
                        <td>
                            <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$codigo}','{$ativo}')"));?>

                            <?php if($ativo== 0): ?>
                                <span class="badge-empty badge badge-important" title="Desativado"></span>
                            <?php elseif($ativo== 1): ?>
                                <span class="badge-empty badge badge-success" title="Ativo"></span>
                            <?php endif; ?>
                            &nbsp;
                            <?php
                                echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-edit', 'escape' => false, 'title'=>'Editar','onclick' => "edit_condicoes(1,".$codigo_pda_config_regra.",".$codigo_cliente.",".$codigo_tema.",".$codigo.")"));
                             ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

    <?php elseif($codigo_tema == 13): // obs atraso ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tipo Sla</th>
                    <th>Quantidade de dias corridos após o atraso na tratativa de uma observação</th>
                    <th>Unidade</th>
                    <th>Opco</th>
                    <th>Bussiness Unit</th>
                    <th class="acoes" style="width:75px">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($dados_condicoes as $dados): 
                    //para edicao
                    $codigo = $dados['PdaConfigRegraCondicao']['codigo'];
                    $codigo_pda_config_regra = $dados['PdaConfigRegraCondicao']['codigo_pda_config_regra'];
                    $codigo_cliente = $dados['PdaConfigRegraCondicao']['codigo_cliente'];
                    $codigo_tema = $dados["PdaConfigRegra"]["codigo_pda_tema"];
                    
                    $ativo = $dados['PdaConfigRegraCondicao']['ativo'];
                ?>
                    <tr>
                        <td><?php echo $tipo_sla[$dados['PdaConfigRegraCondicao']['tipo_sla']] ?></td>
                        <td><?php echo $dados['PdaConfigRegraCondicao']['qtd_dias'] ?></td>
                        <td><?php echo $dados['ClienteUnidade']['nome_fantasia'] ?></td>
                        <td><?php echo $dados['ClienteOpco']['descricao'] ?></td>
                        <td><?php echo $dados['ClienteBu']['descricao'] ?></td>
                        <td>
                            <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatus('{$codigo}','{$ativo}')"));?>

                            <?php if($ativo== 0): ?>
                                <span class="badge-empty badge badge-important" title="Desativado"></span>
                            <?php elseif($ativo== 1): ?>
                                <span class="badge-empty badge badge-success" title="Ativo"></span>
                            <?php endif; ?>
                            &nbsp;
                            <?php
                                echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-edit', 'escape' => false, 'title'=>'Editar','onclick' => "edit_condicoes(1,".$codigo_pda_config_regra.",".$codigo_cliente.",".$codigo_tema.",".$codigo.")"));
                             ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>

    <?php endif;?>


<?php else:?>
    <div class="alert">Nenhuma condição encontrada!</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock('
    function atualizaStatus(codigo, status){
        $.ajax({
            type: "POST",
            url: baseUrl + "pda_config_regra/atualiza_status/" + codigo + "/" + status + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div.lista_condicoes"));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaCondicoesObs();
                    $("div.lista_condicoes").unblock();
                } else {
                    atualizaListaCondicoesObs();
                    $("div.lista_condicoes").unblock();
                }
            },
            error: function(erro){
                $("div.lista_condicoes").unblock();
            }
        });
    }

    function edit_condicoes(mostra,codigo,codigo_cliente,codigo_tema,codigo_condicao) {
        if(mostra) {
            
            var div = jQuery("div#modal_condicoes_obs");
            bloquearDiv(div);
            div.load(baseUrl + "pda_config_regra/modal_condicoes_obs/" + codigo + "/"  + codigo_cliente + "/" + codigo_tema +  "/" + codigo_condicao + "/" + Math.random());
    
            $("#modal_condicoes_obs").css("z-index", "1050");
            $("#modal_condicoes_obs").modal("show");

        } else {
            $(".modal").css("z-index", "-1");
            $("#modal_condicoes_obs").modal("hide");
        }

    }


    '); 
?>