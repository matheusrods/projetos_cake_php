<?php if(!empty($riscos)):?>
<?php echo $paginator->options(array('update' => 'div#busca-lista')); ?>
<div class='actionbar-right'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-success', 'style' => 'color: #FFF', 'title' =>'Enviar Informações', 'onclick' => 'insereEpcRisco()'));?>
</div>
<span id="resultado_erro" class="control-group input text error"></span>
<?php echo $bajax->form('EpcRisco',array('url' => array('controller' => 'epc_riscos', 'action' => 'incluir', $codigo_epc),'type' => 'post')) ?>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Risco</th>
			<th>Grupo de Risco</th>
			<th class="input-mini">Ações</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($riscos as $key => $dados): ?>
			<tr>
				<td class="input-medium"><?php echo $dados['GrupoRisco']['descricao'] ?></td>
				<td><?php echo $dados['Risco']['codigo']."-".$dados['Risco']['nome_agente'] ?></td>
				<td class="action-icon">
                    <?php echo $this->BForm->input('risco'.$dados['Risco']['codigo'] ,array('type'=>'checkbox','value'=>$dados['Risco']['codigo'], 'class' => 'input-mini', 'label' => false)) ?>
                </td>
			</tr>
		<?php endforeach ?>	 
	</tbody>
	<tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Risco']['count']; ?></td>
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

    function insereEpcRisco(){
        var form = $("form#EpcRiscoListagemEpcRiscosForm").serialize();

    	$.ajax({
            type: "POST",        
            url: baseUrl + "/epc_riscos/incluir/'.$codigo_epc.'/" + Math.random(),        
            dataType : "json",
            data: form,
            success: function(data){
            

                if(data == 1){
                   close_dialog();
                   atualizaListaRiscos();       
                }
                else{
                    $("#resultado_erro").removeClass("form-error").parent().removeClass("error").find("#lbl-error").remove();
                    $("#resultado_erro").addClass("form-error").addClass("error").append("<div id=\'lbl-error\' class=\'alert alert-error\'>Risco já cadastrado!</div>");
                }
          	},
          	error : function(error){
                console.log(error);
          	}
        }); 
    }


    function atualizaListaRiscos(){
        var div = jQuery("#epc_riscos-lista");
        bloquearDiv(div);
        div.load(baseUrl + "epc_riscos/listagem/'.$codigo_epc.'/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
