
<?php if(isset($dados_condicoes) && count($dados_condicoes)) : ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Status</th>
                <th>Condição</th>
                <th>Quantidade Dias</th>
                <th>Criticidade</th>
                <th>Origem</th>
                <th>Unidade</th>
                <th>Opco</th>
                <th>B.U</th>
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
                $codigo_status = (!empty($dados["PdaConfigRegra"]["codigo_acoes_melhorias_status"])) ? $dados["PdaConfigRegra"]["codigo_acoes_melhorias_status"] : 'null';

                $ativo = $dados['PdaConfigRegraCondicao']['ativo'];
            ?>
                <tr>
                    <td><?php echo $dados['AcoesMelhoriasStatus']['descricao'] ?></td>
                    <td><?php echo $dados['PdaConfigRegraCondicao']['condicao'] ?></td>
                    <td><?php echo $dados['PdaConfigRegraCondicao']['qtd_dias'] ?></td>
                    <td><?php echo $dados['PosCriticidade']['descricao'] ?></td>
                    <td><?php echo $dados['OrigemFerramenta']['descricao'] ?></td>
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
                            if($codigo_tema == 1 && $codigo_status == 3) {
                                echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-edit', 'escape' => false, 'title'=>'Editar','onclick' => "edit_condicoes(1,".$codigo_pda_config_regra.",".$codigo_cliente.",".$codigo_tema.",".$codigo_status.",".$codigo.")"));
                            }
                            else if($codigo_tema <> 1){
                                echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-edit', 'escape' => false, 'title'=>'Editar','onclick' => "edit_condicoes(1,".$codigo_pda_config_regra.",".$codigo_cliente.",".$codigo_tema.",".$codigo_status.",".$codigo.")"));
                            }
                         ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
<?php else:?>
    <div class="alert">Nenhuma questão encontrada!</div>
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
                    atualizaListaCondicoes();
                    $("div.lista_condicoes").unblock();
                } else {
                    atualizaListaCondicoes();
                    $("div.lista_condicoes").unblock();
                }
            },
            error: function(erro){
                $("div.lista_condicoes").unblock();
            }
        });
    }

    function edit_condicoes(mostra,codigo,codigo_cliente,codigo_tema,codigo_status,codigo_condicao) {
        if(mostra) {
            
            var div = jQuery("div#modal_condicoes");
            bloquearDiv(div);
            div.load(baseUrl + "pda_config_regra/modal_condicoes/" + codigo + "/"  + codigo_cliente + "/" + codigo_tema + "/" + codigo_status + "/" + codigo_condicao + "/" + Math.random());
    
            $("#modal_condicoes").css("z-index", "1050");
            $("#modal_condicoes").modal("show");

        } else {
            $(".modal").css("z-index", "-1");
            $("#modal_condicoes").modal("hide");
        }

    }


    '); 
?>