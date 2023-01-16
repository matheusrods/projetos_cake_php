<?php if(!empty($dados_epi)):?>
<?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<div class='actionbar-right'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-success', 'style' => 'color: #FFF', 'title' =>'Enviar Informações', 'onclick' => 'insereGrupoExposicaoRiscoEpi()'));?>
</div>
<span id="resultado_erro" class="control-group input text error"></span>
<?php echo $bajax->form('GrupoExposicaoRisco') ?>
<table class="table table-striped" id="epi">
	<thead>
		<tr>
			<th>EPI</th>
			<th class="input-mini">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dados_epi as $key => $dados): ?>
			<tr class="linhas_epi">
				<td class="input-medium nome_epi"><?php echo $dados['Epi']['nome'] ?></td>
				<td class="action-icon">
                    <?php echo $this->BForm->input('GrupoExposicaoRisco.'.($key).'.codigo',array('type'=>'checkbox','value' => $dados['Epi']['codigo'], 'class' => 'input-mini', 'label' => false)) ?>
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
    <div class='row-fluid'>
        <div class='numbers span7'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span4'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>

<?php echo $this->BForm->end(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 
<?php echo $this->Javascript->codeBlock('

    function insereGrupoExposicaoRiscoEpi(){
        var form = $("form#GrupoExposicaoRiscoListagemBuscarEpiForm");

        $.each($(form).find("input[type=\'checkbox\']"), function(id_check, dados) { 
            if ($(this).is(":checked")) {
        
                var value = $(this).val();
                var descricao = $(this).parent().parent().parent().find("td.nome_epi").html();

               
                var id = $("#listagem_epi tr.linhas").length;
alert(id);
                if(id == 0){
                    alert("entrou1");
                    $(".alert-epi").hide();
                    
                    $("#modelo_epi").clone().appendTo("#listagem_epi_'.$linha.'").show().find("select").each(function(index, element){
                        $(element).attr("name", $(element).attr("name").replace("[k]", "['.$linha.']"));
                        $(element).attr("id", $(element).attr("id").replace("K", '.$linha.'));

                        $(element).attr("name", $(element).attr("name").replace("[x]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("x", id));
                        
                        if($(element).attr("name") == "data[GrupoExposicaoRiscoEpi]["+ id +"][codigo_epi]"){
                            $(element).children().remove();
                            $(element).append("<option value=\'"+value+"\'>" +descricao+ "</option>");
                        }
                    });

                }
                else{
                    alert("entrou2");
                    $("#listagem_epi_'.$linha.' tr.linhas:first").clone().appendTo("#listagem_epi").show().find("select.codigo_epi").each(function(index, element){
                        $(element).attr("name", $(element).attr("name").replace("[0]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("0", id));
                        $(element).children().remove();
                        $(element).append("<option value=\'"+value+"\'>" +descricao+ "</option>");
                    });
                }
            }
        });
        //close_dialog();
    }
    
');
?>
<?php echo $this->Js->writeBuffer(); ?>
