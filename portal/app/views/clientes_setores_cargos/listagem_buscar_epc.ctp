<?php if(!empty($dados_epc)):?>
<?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>

<span id="resultado_erro" class="control-group input text error"></span>
<?php echo $bajax->form('Epc') ?>

<table class="table table-striped" id="epc">
	<thead>
		<tr>
            <th class="input-small">Código</th>
            <th style="width: 700px">EPc</th>
            <th class="input-mini">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dados_epc as $key => $dados): ?>
            <tr class="linhas_epc">
                <td class="input-small"><?php echo $dados['Epc']['codigo']; ?></td>
				<td class="input-medium nome_epc"><?php echo $dados['Epc']['nome'];?></td>
				<td class="action-icon">
                    <?php echo $this->BForm->input('Epc.'.($key).'.codigo',array('type'=>'checkbox','value' => $dados['Epc']['codigo'], 'class' => 'input-mini', 'label' => false)) ?>
                </td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Epc']['count']; ?></td>
            </tr>
	</tfoot>    
</table>
<?php echo $this->BForm->end(); ?>
<div class='form-actions'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-primary', 'title' =>'Enviar Informações', 'style' => 'color:#FFF', 'onclick' => 'insereGrupoExposicaoRiscoEpc()'));?>
    <?= $html->link('Voltar',  'javascript:void(0)', array('class' => 'btn', 'onclick' => 'close_dialog();')); ?>
</div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Javascript->codeBlock('

    function insereGrupoExposicaoRiscoEpc(){

        var form = $("form#EpcListagemBuscarEpcForm");

        $.each($(form).find("input[type=\'checkbox\']"), function(id_check, dados) { 
            if ($(this).is(":checked")) {
                var value = $(this).val();
                var descricao = $(this).parent().parent().parent().find("td.nome_epc").html();

                var id = $("#listagem_epc_'.$linha.' tr.linhas").length;
        
                $("#modelo_epc tr.linhas").clone().appendTo("#listagem_epc_'.$linha.'").show().find("input").each(function(index, element){
                    
                    $("#listagem_epc_'.$linha.'").parent().parent().css("width", "270px");

                    if($(element).attr("type") == "hidden") { 

                        $(element).attr("name", $(element).attr("name").replace("[k]", "['.$linha.']"));
                        $(element).attr("id", $(element).attr("id").replace("K", '.$linha.'));

                        $(element).attr("name", $(element).attr("name").replace("[x]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("X", id));
                        
                        codigo = $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpc"+ id +"CodigoEpc");
                        $(codigo).val(value);
                    }
                    else{
                        $(element).attr("name", $(element).attr("name").replace("[k]", "['.$linha.']"));
                        $(element).attr("id", $(element).attr("id").replace("K", '.$linha.'));

                        $(element).attr("name", $(element).attr("name").replace("[x]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("X", id));

                        nome = $("#GrupoExposicaoRisco'.$linha.'GrupoExposicaoRiscoEpc"+ id +"Nome");
                        $(nome).val(descricao);
                        
                        $(element).parent().next().find("a").attr("onclick", "excluirEpc(this)")                         
                    }
                 });
            }
        });

        close_dialog();
    }
    
');
?>
<?php echo $this->Js->writeBuffer(); ?>
