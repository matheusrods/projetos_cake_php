<?php if(!empty($dados_epc)):?>
<?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<div class='actionbar-right'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-success', 'style' => 'color: #FFF', 'title' =>'Enviar Informações', 'onclick' => 'insereGrupoExposicaoRiscoEpc()'));?>
</div>
<span id="resultado_erro" class="control-group input text error"></span>
<?php echo $bajax->form('GrupoExposicaoRisco') ?>
<table class="table table-striped" id="epc">
	<thead>
		<tr>
			<th>EPC</th>
			<th class="input-mini">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($dados_epc as $key => $dados): ?>
			<tr class="linhas_epc">
				<td class="input-medium nome_epc"><?php echo $dados['Epc']['nome'];?></td>
				<td class="action-icon">
                    <?php echo $this->BForm->input('GrupoExposicaoRisco.'.($key).'.codigo',array('type'=>'checkbox','value' => $dados['Epc']['codigo'], 'class' => 'input-mini', 'label' => false)) ?>
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

    function insereGrupoExposicaoRiscoEpc(){
        var form = $("form#GrupoExposicaoRiscoListagemBuscarEpcForm");

        $.each($(form).find("input[type=\'checkbox\']"), function(id_check, dados) { 
            if ($(this).is(":checked")) {
        
                var value = $(this).val();
                var descricao = $(this).parent().parent().parent().find("td.nome_epc").html();

                var id = $("#listagem_epc tr.linhas").length;

              
                if(id == 0){
                    $(".alert-epc").hide();
                    $("#modelo_epc").clone().appendTo("#listagem_epc").show().find("select").each(function(index, element){
                        $(element).attr("name", $(element).attr("name").replace("[0]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("0", id));

                        if($(element).attr("name") == "data[GrupoExposicaoRiscoEpc]["+ id +"][codigo_epc]"){
                            $(element).children().remove();
                            $(element).append("<option value=\'"+value+"\'>" +descricao+ "</option>");
                        }
                    });
                }
                else{
                    $("#listagem_epc tr.linhas:first").clone().appendTo("#listagem_epc").show().find("select.codigo_epc").each(function(index, element){
                        $(element).attr("name", $(element).attr("name").replace("[0]", "["+ id +"]"));
                        $(element).attr("id", $(element).attr("id").replace("0", id));
                        $(element).children().remove();
                        $(element).append("<option value=\'"+value+"\'>" +descricao+ "</option>");
                    });
                }
            }
        });
        close_dialog();
    }
    
');
?>
<?php echo $this->Js->writeBuffer(); ?>
