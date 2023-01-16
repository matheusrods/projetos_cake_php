
<?php if(isset($dados_questoes) && count($dados_questoes)) : ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Título</th>
                <th>Questão</th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($dados_questoes as $dados): 
                //para edicao
                $codigo_titulo = $dados['PosSwtFormTitulo']['codigo'];
                echo $this->BForm->hidden('PosSwtFormTitulo.ordem'.$codigo_titulo, array('value' => $dados['PosSwtFormTitulo']['ordem']) );
                echo $this->BForm->hidden('PosSwtFormTitulo.titulo'.$codigo_titulo, array('value' => $dados['PosSwtFormTitulo']['titulo']) );

            ?>
                <tr>
                    <td><?php echo $dados['PosSwtFormTitulo']['ordem'] .' - '. $dados['PosSwtFormTitulo']['titulo'] ?></td>
                    <td>&nbsp;</td>
                    <td>
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusTitulo('{$codigo_titulo}','{$dados['PosSwtFormTitulo']['ativo']}')"));?>

                        <?php if($dados['PosSwtFormTitulo']['ativo']== 0): ?>
                            <span class="badge-empty badge badge-important" title="Desativado"></span>
                        <?php elseif($dados['PosSwtFormTitulo']['ativo']== 1): ?>
                            <span class="badge-empty badge badge-success" title="Ativo"></span>
                        <?php endif; ?>
                        &nbsp;
                        <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-edit', 'escape' => false, 'title'=>'Editar','onclick' => "editar_lista_titulo('{$codigo_titulo}')"));?>
                    </td>
                </tr>

                <?php
                foreach ($dados['PosSwtFormQuestao'] AS $dado_questao): 

                    //para edicao
                    $codigo_questao = $dado_questao['codigo'];
                    echo $this->BForm->hidden('PosSwtFormQuestao.codigo_form_titulo'.$codigo_questao, array('value' => $codigo_titulo) );
                    echo $this->BForm->hidden('PosSwtFormQuestao.ordem'.$codigo_questao, array('value' => $dado_questao['ordem']) );
                    echo $this->BForm->hidden('PosSwtFormQuestao.questao'.$codigo_questao, array('value' => $dado_questao['questao']) );
                    echo $this->BForm->hidden('PosSwtFormQuestao.saiba_mais'.$codigo_questao, array('value' => $dado_questao['saiba_mais']) );

                    ?>
                    <tr>
                        <td>-</td>
                        <td><?php echo $dado_questao['ordem'] .' - '. $dado_questao['questao'] ?></td>
                        <td>
                            <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-random troca-status', 'escape' => false, 'title'=>'Troca Status','onclick' => "atualizaStatusQuestao('{$codigo_questao}','{$dado_questao['ativo']}')"));?>

                            <?php if($dado_questao['ativo']== 0): ?>
                                <span class="badge-empty badge badge-important" title="Desativado"></span>
                            <?php elseif($dado_questao['ativo']== 1): ?>
                                <span class="badge-empty badge badge-success" title="Ativo"></span>
                            <?php endif; ?>
                            &nbsp;
                            <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => 'icon-edit', 'escape' => false, 'title'=>'Editar','onclick' => "editar_lista_questao('{$codigo_questao}')"));?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php endforeach ?>
        </tbody>
    </table>
<?php else:?>
    <div class="alert">Nenhuma questão encontrada!</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock('        
    function atualizaStatusTitulo(codigo, status){
        $.ajax({
            type: "POST",
            url: baseUrl + "swt/atualiza_status_titulo/" + codigo + "/" + status + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div.lista_questoes"));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaQuestoes();
                    $("div.lista_questoes").unblock();
                } else {
                    atualizaListaQuestoes();
                    $("div.lista_questoes").unblock();
                }
            },
            error: function(erro){
                $("div.lista_questoes").unblock();
            }
        });
    }

    function atualizaStatusQuestao(codigo, status){
        $.ajax({
            type: "POST",
            url: baseUrl + "swt/atualiza_status_questao/" + codigo + "/" + status + "/" + Math.random(),
            beforeSend: function(){
                bloquearDivSemImg($("div.lista_questoes"));  
            },
            success: function(data){
                if(data == 1){
                    atualizaListaQuestoes();
                    $("div.lista_questoes").unblock();
                } else {
                    atualizaListaQuestoes();
                    $("div.lista_questoes").unblock();
                }
            },
            error: function(erro){
                $("div.lista_questoes").unblock();
            }
        });
    }

    function atualizaListaQuestoes(){
        var div = jQuery("div.lista_questoes");
        bloquearDiv(div);
        div.load(baseUrl + "swt/listagem_form_questao/'.$codigo_form.'/"+ Math.random());
    }
    '); 
?>
