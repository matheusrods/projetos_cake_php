<?php if(!empty($dados_epi)):?>
    
    <?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>

    <span id="resultado_erro" class="control-group input text error"></span>
    <?php echo $bajax->form('Epi') ?>

    <table class="table table-striped" id="epi">
       <thead>
          <tr>
            <th class="input-small">Código</th>
            <th style="width: 700px">EPI</th>           
            <th class="input-mini">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dados_epi as $key => $dados): ?>
            <?php 
            $data_validade = '';
            if(!empty($dados['Epi']['data_validade_ca'])) {
                $data_validade = reset((explode(' ', $dados['Epi']['data_validade_ca'])));
            }
            ?>
            <tr class="linhas_epi">
                <td class="hide">
                    <?php echo $this->BForm->hidden('numero_ca', array('value' => $dados['Epi']['numero_ca'])) ?>
                    <?php echo $this->BForm->hidden('data_validade_ca', array('type' => 'text', 'value' => $data_validade))?>
                    <?php echo $this->BForm->hidden('atenuacao_qtd', array('value' => $dados['Epi']['atenuacao_qtd'])) ?>
                </td>

                <td class="input-small"><?php echo $dados['Epi']['codigo']; ?></td>
                <td class="input-medium nome_epi"><?php echo $dados['Epi']['nome'];?></td>
                <td class="action-icon">
                    <?php echo $this->BForm->input('Epi.'.($key).'.codigo',array('type'=>'checkbox','value' => $dados['Epi']['codigo'], 'class' => 'input-mini', 'label' => false)) ?>
                </td>
            </tr>
        <?php endforeach ?>  
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Epi']['count']; ?></td>
        </tr>
    </tfoot>    
</table>
<?php echo $this->BForm->end(); ?>
<div class='form-actions'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-primary', 'title' =>'Enviar Informações', 'style' => 'color:#FFF', 'onclick' => 'insereGrupoExposicaoRiscoEpi()'));?>
    <?= $html->link('Voltar', 'javascript:void(0)', array('class' => 'btn', 'onclick' => 'close_dialog();')); ?>
</div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Javascript->codeBlock(' 

    function insereGrupoExposicaoRiscoEpi(){

        var form = $("form#EpiListagemBuscarEpiForm");

        $.each($(form).find("input[type=\'checkbox\']"), function(id_check, dados) { 
            if ($(this).is(":checked")) {
                var value = $(this).val();
                var descricao = $(this).parent().parent().parent().find("td.nome_epi").html();
                var numero_ca = $(this).parent().parent().parent().find("[name=\'data[Epi][numero_ca]\']").val();
                var data_validade_ca = $(this).parent().parent().parent().find("[name=\'data[Epi][data_validade_ca]\']").val();
                var atenuacao_qtd = $(this).parent().parent().parent().find("[name=\'data[Epi][atenuacao_qtd]\']").val();

                var id = $("#listagem_epi_'.$linha.' tr.linhas").length;

                $("#modelo_epi tr.linhas").clone().appendTo("#listagem_epi_'.$linha.'").show().find("input").each(function(index, element){
                    
                    $("#listagem_epi_'.$linha.'").parent().parent().css("width", "720px");

                    if($(element).attr("type") == "hidden") {
                        $(element).attr("name", $(element).attr("name").replace("[k]", "['.$linha.']"));
                        $(element).attr("id", $(element).attr("id").replace("K", '.$linha.'));

                        $(element).attr("name", $(element).attr("name").replace("[x]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("X", id));

                        codigo = $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpi"+ id +"CodigoEpi");
                        $(codigo).val(value);

                        //traz selecionado o existente como default
                        $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpi"+id+"Controle1").prop("checked",true);

                    }
                    else{

                        $(element).attr("name", $(element).attr("name").replace("[k]", "['.$linha.']"));
                        $(element).attr("id", $(element).attr("id").replace("K", '.$linha.'));

                        $(element).attr("name", $(element).attr("name").replace("[x]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("X", id));

                        in_nome                 = $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpi"+ id +"Nome");
                        in_numero_ca            = $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpi"+ id +"NumeroCa");
                        in_data_validade_ca     = $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpi"+ id +"DataValidadeCa");
                        in_atenuacao_qnt        = $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpi"+ id +"AtenuacaoQnt");

                        $(in_nome).val(descricao);
                        $(in_numero_ca).val(numero_ca);
                        $(in_data_validade_ca).val(data_validade_ca);
                        $(in_atenuacao_qnt).val(atenuacao_qtd);
                        
                        $(element).parent().next().find("a").attr("onclick", "excluirEpi(this)");

                        $(in_data_validade_ca).addClass("data");
                    }

                });

                //rele a tabela para trocar as diretivas k e x para esconder ou apresentar o itens do epi
                $("#listagem_epi_'.$linha.' tr.linhas").find("div").each(
                    function(index, element){
                        //console.log($(element).attr("class"));
                        $(element).attr("class", $(element).attr("class").replace(".k.", '.$linha.'));
                        $(element).attr("class", $(element).attr("class").replace(".x", id));
                    }
                );
                
            }

        });

        close_dialog();

        setup_mascaras();
        setup_datepicker(); 


    }

    ');
    ?>
    <?php echo $this->Js->writeBuffer(); ?>
