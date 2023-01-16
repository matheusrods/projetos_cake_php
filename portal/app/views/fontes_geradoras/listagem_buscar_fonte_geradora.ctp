<?php if(!empty($dados_fonte_geradora)):?>
<?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>

<span id="resultado_erro" class="control-group input text error"></span>
<?php echo $bajax->form('FonteGeradora') ?>

<table class="table table-striped" id="fonte_geradora">
	<thead>
		<tr>
            <th class="input-small">Código</th>
            <th style="width: 700px">Fonte Geradora</th>
            <th class="input-mini">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dados_fonte_geradora as $key => $dados): ?>
            <tr class="linhas_fonte_geradora">
                <td class="input-small"><?php echo $dados['FonteGeradora']['codigo']; ?></td>
				<td class="input-medium nome_fonte_geradora"><?php echo $dados['FonteGeradora']['nome'];?></td>
				<td class="action-icon">
                    <?php echo $this->BForm->input('FonteGeradora.'.($key).'.codigo',array('type'=>'checkbox','value' => $dados['FonteGeradora']['codigo'], 'class' => 'input-mini', 'label' => false)) ?>
                </td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['FonteGeradora']['count']; ?></td>
            </tr>
	</tfoot>    
</table>
<?php echo $this->BForm->end(); ?>
<div class='form-actions'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-primary', 'title' =>'Enviar Informações', 'style' => 'color:#FFF', 'onclick' => 'insereGrupoExposicaoRiscoFontegeradora()'));?>
    <?= $html->link('Voltar',  'javascript:void(0)', array('class' => 'btn', 'onclick' => 'close_dialog();')); ?>
</div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

<?php echo $this->Javascript->codeBlock('

    function insereGrupoExposicaoRiscoFontegeradora(){

        var form = $("form#FonteGeradoraListagemBuscarFonteGeradoraForm");

        $.each($(form).find("input[type=\'checkbox\']"), function(id_check, dados) { 
            if ($(this).is(":checked")) {
                var value = $(this).val();
                var descricao = $(this).parent().parent().parent().find("td.nome_fonte_geradora").html();

                var id = $("#listagem_fonte_geradora_'.$linha.' tr.linhas").length;
                
                $("#modelo_fonte_geradora tr.linhas").clone().appendTo("#listagem_fonte_geradora_'.$linha.'").show().find("input").each(function(index, element){
                    $("#listagem_fonte_geradora_'.$linha.'").parent().parent().css("width", "200px");

                    $(element).attr("name", $(element).attr("name").replace("[k]", "['.$linha.']"));
                    $(element).attr("id", $(element).attr("id").replace("K", '.$linha.'));

                    $(element).attr("name", $(element).attr("name").replace("[x]", "["+ id +"]"));
                    $(element).attr("id", $(element).attr("id").replace("X", id));

                    codigo = $("#GrupoExposicaoRisco'.$linha.'GrupoExpRiscoFonteGera"+ id +"CodigoFontesGeradoras");
                    nome = $("#GrupoExposicaoRisco'.$linha.'GrupoExpRiscoFonteGera"+ id +"Nome");
                    
                    $(codigo).val(value);
                    $(nome).val(descricao);

                    $(element).parent().next().find("a").attr("onclick", "excluirFonteGeradora(this)")                         
                 });
            }
        });
        close_dialog();
    }
    
');
?>
<?php echo $this->Js->writeBuffer(); ?>
