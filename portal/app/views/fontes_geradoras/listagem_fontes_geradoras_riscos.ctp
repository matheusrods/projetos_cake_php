<?php if(!empty($riscos)):?>
    <?php echo $paginator->options(array('update' => 'div#buscar_fontes_geradoras_riscos-lista')); ?>
    <span id="resultado_erro" class="control-group input text error"></span>

    <?php echo $bajax->form('FonteGeradoraRisco',array('url' => array('controller' => 'fontes_geradoras_riscos', 'action' => 'incluir', $codigo_fonte_geradora),'type' => 'post')) ?>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Código</th>
                <th>Risco</th>
                <th>Grupo de Risco</th>
                <th class="input-mini">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($riscos as $key => $dados): ?>
            <tr>
                <td class="input-small"><?php echo $dados['Risco']['codigo'] ?></td>
                <td><?php echo $dados['Risco']['nome_agente'] ?></td>
                <td class="input-medium"><?php echo $dados['GrupoRisco']['descricao'] ?></td>
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
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
<?php echo $this->BForm->end(); ?>

<div class='form-actions'>
    <?php echo $this->Html->link('Salvar', 'javascript:void(0)',array('escape' => false, 'class' => 'btn btn-primary', 'style' => 'color: #FFF', 'title' =>'Enviar Informações', 'onclick' => 'insereFonteGeradoraRisco()'));?>
    <?= $html->link('Voltar', 'javascript:void(0)', array('class' => 'btn', 'escape' => false,'title' =>'Enviar Informações', 'onclick' => 'close_dialog();')); ?>
</div>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock('

    function insereFonteGeradoraRisco(){
        var form = $("form#FonteGeradoraRiscoListagemFontesGeradorasRiscosForm").serialize();

    	$.ajax({
            type: "POST",        
            url: baseUrl + "/fontes_geradoras_riscos/incluir/'.$codigo_fonte_geradora.'/" + Math.random(),        
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
        var div = jQuery("#fontes_geradoras_riscos-lista");
        bloquearDiv(div);
        div.load(baseUrl + "fontes_geradoras_riscos/listagem/'.$codigo_fonte_geradora.'/" + Math.random());
    }
');
?>
<?php echo $this->Js->writeBuffer(); ?>
