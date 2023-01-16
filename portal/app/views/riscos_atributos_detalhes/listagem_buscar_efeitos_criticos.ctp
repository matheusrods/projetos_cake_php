<?php if(!empty($dados_efeito_critico)):?>
<?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>

<span id="resultado_erro" class="control-group input text error"></span>
<?php echo $bajax->form('EfeitosCriticos') ?>

<table class="table table-striped" id="efeito_critico">
	<thead>
		<tr>
            <th class="input-small">Código</th>
            <th style="width: 700px">Efeito Crítico</th>
            <th class="input-mini">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($dados_efeito_critico as $key => $dados): ?>
            <tr class="linhas_fonte_geradora">
                <td class="input-small"><?php echo $dados['RiscoAtributoDetalhe']['codigo']; ?></td>
				<td class="input-medium descricao_efeito_critico"><?php echo $dados['RiscoAtributoDetalhe']['descricao'];?></td>
				<td class="action-icon">
                    <?php echo $this->BForm->input('EfeitosCriticos.'.($key).'.codigo',array('type'=>'checkbox','value' => $dados['RiscoAtributoDetalhe']['codigo'], 'class' => 'input-mini', 'label' => false)) ?>
                </td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
        <tr>
            <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['RiscoAtributoDetalhe']['count']; ?></td>
        </tr>
	</tfoot>    
</table>
<?php echo $this->BForm->end(); ?>
<div class='form-actions'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-primary', 'title' =>'Enviar Informações', 'style' => 'color:#FFF', 'onclick' => 'insereGrupoExposicaoEfeitosCriticos()'));?>
    <?= $html->link('Voltar',  'javascript:void(0)', array('class' => 'btn', 'onclick' => 'close_dialog();')); ?>
</div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?> 

<?php echo $this->Javascript->codeBlock('

    function insereGrupoExposicaoEfeitosCriticos() {

        var form = $("form#EfeitosCriticosListagemBuscarEfeitosCriticosForm");

        $.each($(form).find("input[type=\'checkbox\']"), function(id_check, dados) { 
            if ($(this).is(":checked")) {
                var value = $(this).val();
                var descricao = $(this).parent().parent().parent().find("td.descricao_efeito_critico").html();

                var id = $("#listagem_efeitos_criticos_'.$linha.' tr.linhas").length;
                
                $("#modelo_efeitos_criticos tr.linhas").clone().appendTo("#listagem_efeitos_criticos_'.$linha.'").show().find("input").each(function(index, element){
                    $("#listagem_efeitos_criticos_'.$linha.'").parent().parent().css("width", "200px");

                    $(element).attr("name", $(element).attr("name").replace("[k]", "['.$linha.']"));
                    $(element).attr("id", $(element).attr("id").replace("K", '.$linha.'));

                    $(element).attr("name", $(element).attr("name").replace("[x]", "["+ id +"]"));
                    $(element).attr("id", $(element).attr("id").replace("X", id));

                    codigo = $("#GrupoExposicaoRisco'.$linha.'GrupoExpEfeitoCritico"+ id +"CodigoEfeitoCritico");
                    desc = $("#GrupoExposicaoRisco'.$linha.'GrupoExpEfeitoCritico"+ id +"Descricao");
                    
                    $(codigo).val(value);
                    $(desc).val(descricao);

                    $(element).parent().next().find("a").attr("onclick", "excluirEfeitoCritico(this)")                         
                 });
            }
        });
        close_dialog();
    }
    
');
?>
<?php echo $this->Js->writeBuffer(); ?>
