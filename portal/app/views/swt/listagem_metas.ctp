
<?php if(isset($dados_clientes) && is_array($dados_clientes) && count($dados_clientes) > 0) : ?>
        <?php
            echo $paginator->options(array('update' => 'div.lista'));
            $total_paginas = $this->Paginator->numbers();
        ?>

        <div id="tabela_select_meta_container">
            <table class="table table-striped tabela_select_meta">
                <thead>
                <tr>
                    <th><input type="checkbox" class="metas_select_all"></th>
                    <th><?php echo 'Cód. Cliente' ?></th>
                    <th><?php echo 'Razão Social' ?></th>
                    <th><?php echo 'Nome Fantasia' ?></th>
                    <th><?php echo 'Setor' ?></th>
                    <th><?php echo 'Opco' ?></th>
                    <th><?php echo 'Business Unit' ?></th>
                    <th><?php echo 'Meta' ?></th>
                    <th><?php echo 'Periodicidade da meta' ?></th>
                    <th class="acoes" style="width:75px">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($dados_clientes as $dados): 
                    $dados["PosMetas"] = empty($dados["MetasCustom"]["codigo"]) ? $dados["MetasPadrao"] : $dados["MetasCustom"];

                    $inputId = 
                        $dados["Setor"]["codigo"] 
                        . ((!empty($dados["ClienteBu"]["codigo"])) ? "-" . $dados["ClienteBu"]["codigo"] : "")
                        . ((!empty($dados["ClienteOpco"]["codigo"])) ? "-" . $dados["ClienteOpco"]["codigo"] : "")
                ?>
                    <tr>
                        <th>
                            <input 
                                id="<?= $inputId ?>" 
                                type="checkbox" 
                                class="checkbox" 
                                data-setor="<?= $dados["Setor"]["codigo"] ?>"
                                data-unidade="<?= $dados["Cliente"]["codigo"] ?>"
                                data-opco="<?= $dados["ClienteOpco"]["codigo"] ?>"
                                data-bu="<?= $dados["ClienteBu"]["codigo"] ?>"
                            >
                        </th>
                        <td><?php echo $dados['Cliente']['codigo'] ?></td>
                        <td><?php echo $dados['Cliente']['razao_social'] ?></td>
                        <td><?php echo $dados['Cliente']['nome_fantasia'] ?></td>
                        <td><?php echo $dados['Setor']['descricao'] ?></td>
                        <td><?php echo $dados['ClienteOpco']['descricao'] ?></td>
                        <td><?php echo $dados['ClienteBu']['descricao'] ?></td>
                        <td><?php echo $dados['PosMetas']['valor'] ?></td>
                        <td><?php echo $dados['PosMetas']['dia_follow_up'] ?></td>
                        <td>
                            <?php                               
                                echo $this->Html->link(
                                    '', 
                                    'javascript:void(0)',
                                    array(
                                        'class' => 'icon-random troca-status', 
                                        'escape' => false, 
                                        'title'=>'Troca Status',
                                        'onclick' => "atualizaStatusPosMetas('{$dados["PosMetas"]["codigo"]}')"
                                    )
                                );
                            ?>
                            <?php if($dados["PosMetas"]["ativo"] == 0): ?>
                                <span class="badge-empty badge badge-important" title="Desativado" style="margin-right: 5px"></span>
                            <?php elseif($dados["PosMetas"]["ativo"] == 1): ?>
                                <span class="badge-empty badge badge-success" title="Ativo" style="margin-right: 5px"></span>
                            <?php endif; ?>

                            <?php
                                echo $this->Html->link(
                                    '', 
                                    array(
                                        'action' => 'incluir_metas', 
                                        $dados['Cliente']['codigo'], 
                                        $dados['Setor']['codigo'],
                                        $dados['ClienteBu']['codigo'],
                                        $dados['ClienteOpco']['codigo']
                                    ), 
                                    array(
                                        'class' => 'icon-edit ', 
                                        'title' => 'Editar'
                                    )
                                ); 
                            ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan = "11"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['ClienteFuncionario']['count']; ?></td>
                </tr>
                </tfoot>
            </table>
        </div>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
        <?php echo $this->Js->writeBuffer(); ?>
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif;?>

    <?php echo $this->Javascript->codeBlock('
    function atualizaLista(){
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "swt/listagem_metas/" + Math.random());
    }   
    ');

    echo $this->Javascript->codeBlock("

    function atualizaStatusPosMetas(codigo)
    {
        if (codigo.length <= 0) {
            alert('Insira uma meta neste setor para alterar o status!');
            return false;
        }

        $.ajax({
            type: 'POST',
            url: baseUrl + 'swt/editar_status/' + codigo,
            beforeSend: function(){
                bloquearDivSemImg($('div.lista'));  
            },
            success: function(data){           
                if(data == 1){
                    atualizaLista();                   
                    viewMensagem(2,'Os dados informados foram armazenados com sucesso!');
                } else {
                    atualizaLista();                    
                    viewMensagem(0,'Não foi possível mudar o status!');
                }
            },
            error: function(erro){
                $('div.lista').unblock();
                viewMensagem(0,'Não foi possível mudar o status!');
            }
        });
    }
    
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
");
    ?>

    <?php
    echo $this->Javascript->codeBlock(" ");
    ?>

</div>

<script>

    $(function() {

        if ($("#tabela_select_meta_container").length > 0) {
            $("#cadastrar_metas").show()
            console.log('oii1')
        } else {
            console.log('oii2')
            $("#cadastrar_metas").hide()
        }
        
        $(".lista").css("position", "unset");
        $("#tabela_select_meta_container").css("position", "unset");

        $(".metas_select_all").on("change", function(){
            if ($(this).is(":checked")) {
                $('.tabela_select_meta tbody tr input:checkbox').prop('checked','checked');
            } else {
                $('.tabela_select_meta tbody tr input:checkbox').removeProp('checked');
            }
        });
    })

</script>
