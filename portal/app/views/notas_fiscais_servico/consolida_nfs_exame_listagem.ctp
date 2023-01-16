<style type="text/css">
.badge {
    display: inline-block;
    min-width: 0px;
    padding: 3px 7px;
    font-size: 12px;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    background-color: #777;
    border-radius: 10px;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-info {
    color: #fff;
    background-color: #17a2b8;
}

.modal { 
    width: 40%;
} 




</style>

<?php if(!empty($dados)):?>
    <?php if($dados == 'erro'):?>
        <div class="alert alert-danger">Preencha pelo menos um dos campos com *</div>
    <?php elseif($dados == 'erro2'):?>
    <?php else:?>    
        <?php foreach ($dados as $key => $dado): ?>
            <table class="table table-striped" style="width: 1500px;" >
                <thead>
                    <tr>
                        <th class="input-mini">Detalhe</th>
                        <th class="input-medium">Número Nota Fiscal</th>
                        <th class="input-medium">Código Prestador</th>
                        <th class="input-medium">Razão Social Prestador</th>
                        <th class="input-medium">CNPJ Prestador</th>
                        <th class="input-medium">Nome Fantasia Prestador</th>
                        <th class="input-medium">Valor</th>
                        <th class="input-medium">Data vencimento</th>
                        
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="javascript:void(0);" id="expandir_<?= $key ?>" onclick="mostrar_exames_consolidados(<?= $dado[0]['codigo_nf'] ?>,<?= $key ?>, 'consolidados');" title="Exibir Exames Consolidados"><i id="icone_<?= $key ?>" class="icon-plus"></i></a>
                        </td>
                        <td><?= $dado[0]['numero_nf'] ?></td>
                        <td><?= $dado[0]['codigo_credenciado'] ?></td>
                        <td><?= $dado[0]['razao_credenciado'] ?></td>
                        <td><?= !empty($dado[0]['cnpj_credenciado']) ? Comum::formatarDocumento($dado[0]['cnpj_credenciado']) : '-' ?></td>
                        <td><?= $dado[0]['nome_credenciado'] ?></td>              
                        <td><?= $this->Buonny->moeda($dado[0]['valor_nota'])?></td>              
                        <td><?= Comum::formataData($dado[0]['data_vencimento'],'ymd','dmy'); ?></td>              
                    </tr>
                    <tr id="detalhe_<?= $key ?>" class="hidden">
                        <td colspan="9" class="td-detalhe">

                            <div class='row-fluid'>
                                <div class='span12'>
                                    <h4>Exames Consolidados da Nota Fiscal <?= $dado[0]['numero_nf']  ?> - Detalhes</h4>
                                    <?php if($dado[0]['status_nota'] == 5):?>
                                        <button  onclick="imprimir_ordem_de_pagamento(<?= $dado[0]['codigo_nf']  ?>);"class="btn btn-success imprimir_nf">IMPRIMIR ORDEM DE PAGAMENTO</button>
                                        <button  onclick="reabrir_nota_fiscal(<?= $dado[0]['codigo_nf']?>,<?=$key?>);"class="btn btn-success reabrir_nota">REABRIR NOTA FISCAL</button>
                                        <button  onclick="corrigir_valor_exame(<?= $dado[0]['codigo_nf']  ?>);"class="btn btn-success corrigir_valor hidden">AUDITAR VALOR DOS EXAMES</button>
                                        <button  onclick="finalizar_nf(<?= $dado[0]['codigo_nf']  ?>);"class="btn btn-success finalizar_nf hidden">FINALIZAR NF</button>
                                    <?php else:?>   
                                        <button  onclick="corrigir_valor_exame(<?= $dado[0]['codigo_nf']  ?>);"class="btn btn-success corrigir_valor">AUDITAR VALOR DOS EXAMES</button>
                                        <button  onclick="finalizar_nf(<?= $dado[0]['codigo_nf']  ?>);"class="btn btn-success finalizar_nf">FINALIZAR NF</button>
                                    <?php endif;?> 
                                    
                                    
                                    <br>
                                    <div id="icone_carregar_2<?= $key ?>" class="inline carregarConsolidados" style=""></div>
                                    <div id="icone_carregar_<?= $key ?>" class="inline carregarConsolidados" style=""></div>
                                </div>        
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif;?> 
<?php else:?>    
    <div class="alert">Nenhuma Nota Fiscal  encontrada.</div>
<?php endif;?> 
<?php if(!empty($nao_consolidadas)):?>
    <?php foreach ($array_fornecedores as $codigo_fornecedor => $forn): ?>
        <table class="table table-striped item_pedido_exame" >

            <thead>
            <h4>Exames não consolidados: <?= $forn['razao_social_forn'] ?></h4>                        
                <tr>
                    <th ><input id="<?php echo $codigo_fornecedor ?>" type="checkbox" class="all" title="Marcar/Desmarcar Todos" /></th>
                    <th>Número NFS</th>
                    <th>Pedido de Exame</th>
                    <th>Exame</th>
                    <th>Data Realização</th>
                    <th>Valor Custo</th>
                    <th>Funcionário</th>
                    <th>CPF Funcionário</th>
                    <th>Código Credenciado</th>
                    <th>Credenciado</th>
                    <th>CNPJ Credenciado</th>
                    <th>Nome Fantasia Credenciado</th>
                    <th>Código Cliente</th>
                    <th>Cliente</th>
                    <th>Data Vencimento</th>
                    <th>Data Pagamento</th>
                    <th>Data Baixa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nao_consolidadas[$codigo_fornecedor] as $key => $dado): ?>
                    <tr>
                        <td id="codigo_exame">
                            <?php echo $this->BForm->input('exame'.$key.'codigo', array('type'=>'checkbox', 'id'=>'exame'.$key , 'label' => false,'value'=> $dado['codigo_item_pedido_exame'], 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge pedido_exame_codigo_'.$codigo_fornecedor)); ?>
                        </td>
                        <td><?php  echo  $dado['numero_nota_fiscal']; ?></td>
                        <td><?php  echo  $dado['codigo_pedido_exame']; ?></td>
                        <td><?php  echo  $dado['exame']; ?></td>
                        <td><?php  echo  Comum::formataData($dado['data_realizacao'],'ymd','dmy'); ?></td>
                        <td><?php  echo  $this->Buonny->moeda($dado['valor_custo']); ?></td>
                        <td><?php  echo  $dado['funcionario_nome']; ?></td>
                        <td><?php  echo  !empty($dado['funcionario_cpf']) ? Comum::formatarDocumento($dado['funcionario_cpf']) : '-' ?></td>
                        <td><?php  echo  $dado['codigo_credenciado']; ?></td>
                        <td><?php  echo  $dado['credenciado_razao_social']; ?></td>
                        <td><?php  echo  !empty($dado['credenciado_cnpj']) ? Comum::formatarDocumento($dado['credenciado_cnpj']) : '-' ?></td>
                        <td><?php  echo  $dado['credenciado_nome_fantasia']; ?></td>
                        <td><?php  echo  $dado['codigo_cliente']; ?></td>
                        <td><?php  echo  $dado['nome_cliente']; ?></td>
                        <td><?php  echo  Comum::formataData($dado['data_vencimento_nfs'],'ymd','dmy'); ?></td>
                        <td><?php  echo  Comum::formataData($dado['data_pagamento_nfs'],'ymd','dmy'); ?></td>
                        <td><?php  echo  Comum::formataData($dado['data_baixa'],'timestamp','dmyhms'); ?></td>
                    </tr>
                <?php endforeach; ?>
                
            </tbody>
        </table>
        <div class='row-fluid'>
            <a id="ConsolidarNfsExame" onclick="consolidar_exames(<?= $codigo_fornecedor?>);" href="javascript:void(0);" class="btn btn-success" style="float: left;">Consolidar NFS x Exames</a>
            <div id="loading" style='display: none;float: left;' >
                <img src="/portal/img/default.gif" style="padding: 0px;">Consolidando...
            </div>
        </div>
        <br>
    <?php endforeach; ?>
<?php endif;?> 

<div id="modal_consolidar" class="modal hide fade modal_consolidar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Consolidar exames</h3>
  </div>
  <div class="modal-body">
    <div style="height: 360px; overflow-y: auto;">
        <table class="table table-borded" id="icone_exames">
            <tr id="exame">
                <td colspan="9" class="td-exame">
                <div id="icone_modal_carregar"></div>
                </td>
            </tr>
        </table>
    </div>
        <div>  
            <div class='row-fluid'>
                <div class='span12'>
                <span>Notas Fiscais </span>
                <select name="notas_fiscais" id="notas_fiscais" >
                    <option>Selecione</option>
                </select>
                </div>                               
            </div>            
        </div>
  </div>
  <div class="modal-footer">
    <div class="pull-right">
        <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">CANCELAR</button>
        <button class="btn btn-success modal_data_salvar">APLICAR PARA OS EXAMES SELECIONADOS</button>
    </div>
  </div>
</div>

<div id="modal_concluir_nf" class="modal hide fade modal_concluir_nf" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header ">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Concluir Nota Fiscal</h3>
            <table class="table table-borded" id="parte_header"></table>
        </div>

        <div class="modal-body ">
            <div>
                <table class="table table-borded" id="parte_body_cima"></table>
                <table class="table table-borded" id="parte_body_baixo"></table>
            </div>     
        </div>
        <div class="modal-footer ">
            <div class="pull-right">
                <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">CANCELAR</button>
                <button class="btn btn-success modal_data_concluir">CONCLUIR NF</button>
            </div>
        </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>
<script>

    jQuery(document).ready(function(){
    });

    function mostrar_exames_consolidados(codigo_nf,x,tabela){
        //troca o icone
        if($('#icone_'+x).hasClass('icon-plus')) {

            $('#icone_'+x).removeClass('icon-plus');
            $('#icone_'+x).addClass('icon-minus');

            $('#detalhe_'+x).removeClass('hidden');

            $('#icone_carregar_'+x).show();

            carregamento = $('#carregado_'+x).val();

            if(carregamento != 1) {

                $('#icone_carregar_'+x).show();
            
                $.ajax({
                    type: 'GET',
                    url: '/portal/notas_fiscais_servico/exibir_exames_consolidados/' + codigo_nf,
                    dataType: 'json',  
                    beforeSend: function() {
                                    $('#icone_carregar_'+x).html('<img src=\"/portal/img/loading.gif\">');
                    },       
                    success: function(dados) {


                        
                                
                                if(dados) {

                                    $('icone_carregar_'+x).html('');
                                    $('#carregado_'+x).val('1');

                                    var detalhes = '';
                                    if(dados == 'erro'){
                                        detalhes += '<div class=\"alert\">Nenhum exame foi encontrado para essa nota.</div>';
                                    } else {
                                        detalhes += '<tr>';

                                        detalhes += '<th>Pedido de Exame</th>';
                                        detalhes += '<th>Exame</th>';
                                        detalhes += '<th>Data Realização</th>';
                                        detalhes += '<th>Valor Custo</th>';
                                        detalhes += '<th>Funcionário</th>';
                                        detalhes += '<th>CPF Funcionário</th>';
                                        detalhes += '<th>Código Credenciado</th>';
                                        detalhes += '<th>Credenciado</th>';
                                        detalhes += '<th>CNPJ Credenciado</th>';
                                        detalhes += '<th>Nome Fantasia Credenciado</th>';
                                        detalhes += '<th>Código Cliente</th>';
                                        detalhes += '<th>Cliente</th>';
                                        detalhes += '<th>Data Vencimento</th>';
                                        detalhes += '<th>Data Pagamento</th>';
                                        detalhes += '<th>Data Baixa</th>';
                                        detalhes += '</tr>';

                                        detalhes += '<div class=\"row-fluid \">';

                                        $.each(dados['consolidadas'], function(key, val){
                                            $.each(val, function(){
                                                detalhes += '<tr>';
                                                detalhes += '<th>'+this.codigo_pedido_exame+'</th>';
                                                detalhes += '<th>'+this.exame+'</th>';
                                                detalhes += '<th>'+this.data_realizacao+'</th>';
                                                detalhes += '<th>'+this.valor_custo+'</th>';
                                                detalhes += '<th>'+this.funcionario_nome+'</th>';
                                                detalhes += '<th>'+this.funcionario_cpf+'</th>';
                                                detalhes += '<th>'+this.codigo_credenciado+'</th>';
                                                detalhes += '<th>'+this.credenciado_razao_social+'</th>';
                                                detalhes += '<th>'+this.credenciado_cnpj+'</th>';
                                                detalhes += '<th>'+this.credenciado_nome_fantasia+'</th>';
                                                detalhes += '<th>'+this.codigo_cliente+'</th>';
                                                detalhes += '<th>'+this.nome_cliente+'</th>';
                                                detalhes += '<th>'+this.data_vencimento_nfs+'</th>';
                                                detalhes += '<th>'+this.data_pagamento_nfs+'</th>';
                                                detalhes += '<th>'+this.data_baixa+'</th>';
                                                detalhes += '</tr>';
                                            });
                                        });

                                        detalhes += '</div>';
                                    }
                                    $('#icone_carregar_'+x).html(detalhes); 
                                }else {
                                    swal({type: 'error', title: 'Houve um erro.', text: 'Houve um erro ao tentar carregar os dados da nota fiscal'});
                                }
                                
                    },
                    complete: function(dados) {

                    }
                
                }); 
            }
        }else {

            $('#icone_'+x).removeClass('icon-minus');
            $('#icone_'+x).addClass('icon-plus');

            $('#icone_carregar_'+x).hide();

            $('#detalhe_'+x).addClass('hidden');
        }
    }

    exames_selecionados = '';

    function consolidar_exames(codigo_fornecedor) {
        retorno = false; 

        var codigo = [];
        $('.pedido_exame_codigo_'+codigo_fornecedor+ ':checkbox').each(function(key){
            if ($(this).is(":checked")) {

                codigo[key] = $(this).val();
                if(codigo){					     			
                    retorno = true;         
                }																
            }
		});

        if(retorno == true){
            $.ajax({
            type: "POST",
            url: '/portal/notas_fiscais_servico/modal_consolidar',
            dataType: 'json',      
            data: {codigo: codigo, codigo_fornecedor: codigo_fornecedor},
            success: function(dados) {
                exames_selecionados = dados['nao_consolidadas'];
                $('#icone_exames').html('');                 
                var exames = '';

                exames += '<thead></tr>';
                exames += '<th>Pedido de Exame</th>';
                exames += '<th>Exame</th>';
                exames += '<th>Recebimento Físico</th>';
                exames += '<th>Valor Custo</th>';
                exames += '</tr></thead>';
                
                exames += '<div class=\"row-fluid \">';

                $('#icone_exames').append(exames); 

                $.each(dados['nao_consolidadas'], function(key, val){
                    var body = '';

                    $.each(val, function(){
                            body += '<tr>';
                            body += '<th>'+this.codigo_item_pedido_exame+'</th>';
                            body += '<th>'+this.exame+'</th>';
                            body += '<th>';
                            body += '<input type="radio" name="rec_fisico_'+this.codigo_item_pedido_exame+'" id="recebimento_fisico_nao_'+this.codigo_item_pedido_exame+'" value=0>Não ';
                            body += '<input type="radio" name="rec_fisico_'+this.codigo_item_pedido_exame+'" id="recebimento_fisico_sim_'+this.codigo_item_pedido_exame+'" value=1>Sim ';
                            body +='</th>';
                            body += '<th>'+this.valor_custo+'</th>';
                            body += '</tr>';
                            $('#icone_exames').append(body); 

                            if(this.recebimento_fisico === 1){
                                recebimento_fisico_sim = $('#recebimento_fisico_sim_'+this.codigo_item_pedido_exame);
                                if(recebimento_fisico_sim.prop) {
                                    recebimento_fisico_sim.prop("checked",true);

                                }else {
                                    recebimento_fisico_sim.attr('checked','checked');
                                }
                            }else if (this.recebimento_fisico === 0){
                                recebimento_fisico_nao = $('#recebimento_fisico_nao_'+this.codigo_item_pedido_exame);
                                if(recebimento_fisico_nao.prop) {
                                    recebimento_fisico_nao.prop("checked",true);
                                }
                                else {
                                    recebimento_fisico_nao.attr("checked","checked");
                                }
                            }

                    });
                });
               
                    var input = $('#notas_fiscais');
                    
                    if(input.prop) {
                        var options = input.prop("options");
                    }
                    else {
                        var options = input.attr("options");
                    }
                    $("option", input).remove();

                    if (dados['notas_fornecedor'] != null) {
                        $.each(dados['notas_fornecedor'], function(i, r) {
                            input.append($('<option />').val(r['NotaFiscalServico']['codigo']).text(r['NotaFiscalServico']['numero_nota_fiscal']));
                        });
                    }
                



                    $("#modal_consolidar").modal("show");
                
            }
            });
        }else{
            alert("Selecione um exame para consolidar");
        }
        
        
    }
    

    $('body').on('change', '.all', function() {
        var id = $(this).attr("id");
  		$('.pedido_exame_codigo_'+id).prop('checked', this.checked);
	});

    $(".modal_data_salvar").on("click",function(e){
        e.preventDefault();
        nota_fiscal = $('#notas_fiscais').val();
        if(nota_fiscal && exames_selecionados){
            var avancar = true;
            $.each(exames_selecionados, function(key, val){
                $.each(val, function(){

                    var rec_fisico = $('input[name="rec_fisico_'+this.codigo_item_pedido_exame+'"]:checked').val(); 
                    
                    if(typeof rec_fisico === 'undefined'){
                        swal("Atenção", "O campo recebimento físico é obrigatório!", "warning");
                        avancar = false;
                    }else{
                        this.recebimento_fisico = rec_fisico;
                    }

                });
            });

            if(avancar == true){
                 salvar_consolidacao(nota_fiscal, exames_selecionados);
            }


        }else   {
            alert("Erro ao selecionar os dados");
        }
    });

    function salvar_consolidacao(nota_fiscal,exames_selecionados) {

        $.ajax({
            type: "POST",
            url: '/portal/notas_fiscais_servico/salvar_consolidacao',
            dataType: 'json',      
            data: {nota_fiscal: nota_fiscal, exames_selecionados: exames_selecionados, renderiza: 0},
            beforeSend: function() {
                        // $('#icone_carregar_'+x).show();
                        // $('#icone_modal_carregar').html('<img src=\"/portal/img/loading.gif\">');
                    },
            success: function(retorno) {
                var sucesso = false;
                if(retorno == true){
                    alert("Dados salvos com sucesso!");
                }else{
                    alert("Erro ao salvar os dados"); 
                }
            },
            complete: function(retorno){
                $("#modal_consolidar").modal("hide");
                atualizaListaNotaFiscalServico();
            }
            
        });      
    }

    function corrigir_valor_exame(codigo_nf) {

        var janela = window_sizes();
        window.open(baseUrl + "notas_fiscais_servico/corrigir_valor_exame/" + codigo_nf + "/" + Math.random(), janela, "scrollbars=yes,menubar=no,height="+(janela.height-400)+",width="+(janela.width-80)+",resizable=yes,toolbar=no,status=no");
    }

    function finalizar_nf(codigo_nf) {
        $.ajax({
            type: 'GET',
            url: '/portal/notas_fiscais_servico/finalizar_nf/' + codigo_nf,
            dataType: 'json',  
            success: function(dados) {
                //montando o header do modal
                var header = '';
                header += '<thead></tr>';
                header += '<th>Nota fiscal</th>';
                header += '<th>Data Vencimento</th>';
                header += '<th>Credenciado</th>';
                header += '<th>Valor da nota</th>';
                header += '</tr></thead>';
                header += '<th id="mdl_concluir_numero_nota_fiscal">'+dados['nota_fiscal']['NotaFiscalServico']['numero_nota_fiscal']+'</th>';
                header += '<th class="hidden" id="mdl_codigo_nota_fiscal">'+dados['nota_fiscal']['NotaFiscalServico']['codigo']+'</th>';
                header += '<th id="mdl_concluir_data_vencimento">'+ (dados['nota_fiscal']['NotaFiscalServico']['data_vencimento'] ? dados['nota_fiscal']['NotaFiscalServico']['data_vencimento'] : "-") +'</th>';
                header += '<th id="mdl_concluir_prestador">'+dados['nota_fiscal']['Fornecedor']['razao_social']+'</th>';
                header += '<th id="mdl_concluir_valor_bruto">'+dados['nota_fiscal']['NotaFiscalServico']['valor']+'</th>';

                $('#parte_header').html(header); 
                
                //montando o body do modal

                var body_cima = '';
                body_cima += '<tbody>';
                body_cima += '<tr>';
                body_cima += '<th>Total de exames consolidados:</th>';
                body_cima += '<th id="mdl_valor_exames_consolidados">'+dados['valor_exames_consolidados']+'</th>';
                body_cima += '</tr>';
                body_cima += '<tr>';
                body_cima += '<th>Glosas - Valores não aprovados:</th>';
                body_cima += '<th id="mdl_valor_glosas_valor">'+dados['valor_glosas_valor']+'</th>';
                body_cima += '</tr>';
                body_cima += '<tr>';
                body_cima += '<th>Glosas - Imagens não aprovadas:</th>';
                body_cima += '<th id="mdl_valor_glosas_imagens">'+dados['valor_glosas_imagens']+'</th>';
                body_cima += '</tr>';
                body_cima += '<tr>';
                body_cima += '<th>Glosas - Manuais:</th>';
                body_cima += '<th id="mdl_valor_glosas_manuais">'+dados['valor_glosas_manuais']+'</th>';
                body_cima += '</tr>';
                body_cima += '<tr>';
                body_cima += '<th>Total de glosas:</th>';
                body_cima += '<th id="mdl_total_glosas">'+dados['total_glosas']+'</th>';
                body_cima += '</tr>';
                body_cima += '<tr>';
                body_cima += '<th>Valor consolidado:</th>';
                body_cima += '<th id="mdl_total_final"></th>';
                body_cima += '</tr>';


                $('#parte_body_cima').html(body_cima); 
                 


                var body_baixo ='';
                body_baixo += '<div>';
                body_baixo += '<th><input id="mdl_btn_adicionar_valor" class="btn btn_adicionar_valor hidden" type="submit" value="Acrescimo/Desconto"></th>';
                body_baixo += '</div>';
                body_baixo += '<br>';

                body_baixo += '<tr id="mdl_tr_acrescimo", class="hidden">';
                body_baixo += '<th>Acrescimo: </th><th><input id="mdl_acrescimo" class="input-small valor_corrigido" placeholder="Valor"></input></th>';
                body_baixo += '<th><select id="mdl_motivo_acrescimo"></select></th>';
                body_baixo += '</tr>';

                body_baixo += '<tr id="mdl_tr_desconto", class="hidden">';
                body_baixo += '<th>Desconto: </th><th><input id="mdl_desconto" class="input-small valor_corrigido" placeholder="Valor"></input></th>';
                body_baixo += '<th><select id="mdl_motivo_desconto"></select></th>';
                body_baixo += '</tr>';

                var valor_a_pagar = null;

                body_baixo += '<tr>';
                body_baixo += '<th>Valor a pagar:</th>';
                body_baixo += '<th id="mdl_valor_a_pagar">'+valor_a_pagar+'</th>';
                body_baixo += '</tr>';
                body_baixo += '</tbody>';



                $(".valor_corrigido").mask("999.999,99",{reverse: true});

                $('#parte_body_baixo').html(body_baixo); 

                valor_acrescimo =  ((dados['nota_fiscal']['NotaFiscalServico']['valor_acrescimo'] != "0,00") ? dados['nota_fiscal']['NotaFiscalServico']['valor_acrescimo'] : null);
                valor_desconto  =  ((dados['nota_fiscal']['NotaFiscalServico']['valor_desconto'] != "0,00") ? dados['nota_fiscal']['NotaFiscalServico']['valor_desconto'] : null);
                
                $('#mdl_acrescimo').val(valor_acrescimo);
                $('#mdl_desconto').val(valor_desconto);

                var acrescimo = $('#mdl_acrescimo').val();
                var desconto = $('#mdl_desconto').val();

                var valor_a_pagar = calcular_total_nota(dados['valor_exames_consolidados'],dados['valor_glosas_valor'],dados['valor_glosas_manuais'], acrescimo, desconto,dados['total_glosas']);
                
                $("#mdl_valor_a_pagar").text(valor_a_pagar.replace(".", ","));



                var input_acrescimo = $('#mdl_motivo_acrescimo');

                    if(input_acrescimo.prop) {
                        var options = input_acrescimo.prop("options");
                    }
                    else {
                        var options = input_acrescimo.attr("options");
                    }
                    $("option", input_acrescimo).remove();

                    if (dados['motivos_acrescimo'] != null) {
                        input_acrescimo.append('<option>Motivo</option>');
                        $.each(dados['motivos_acrescimo'], function(i, r) {
                            
                            input_acrescimo.append($('<option />').val(r['MotivosAcrescimo']['codigo']).text(r['MotivosAcrescimo']['descricao']));
                        });
                    }
                    input_acrescimo.val(dados['nota_fiscal']['NotaFiscalServico']['codigo_motivo_acrescimo']);

                var input_desconto = $('#mdl_motivo_desconto');
                    
                    if(input_desconto.prop) {
                        var options = input_desconto.prop("options");
                    }
                    else {
                        var options = input_desconto.attr("options");
                    }
                    $("option", input_desconto).remove();
    
                    if (dados['motivos_desconto'] != null) {
                        input_desconto.append('<option>Motivo</option>');
                        $.each(dados['motivos_desconto'], function(i, r) {
                            input_desconto.append($('<option />').val(r['MotivosDesconto']['codigo']).text(r['MotivosDesconto']['descricao']));
                        });
                    }
                    input_desconto.val(dados['nota_fiscal']['NotaFiscalServico']['codigo_motivo_desconto']);


                if(dados['gestor_operacao'] == 1){
                        if($("#mdl_total_final").text != $("#mdl_concluir_valor_bruto")){
                            $('#mdl_btn_adicionar_valor').removeClass("hidden");
                        }
                }

            },
            complete: function(dados){
                $("#modal_concluir_nf").modal("show");
            }
        });
    }

    //Todo o cálculo de acrescimos e glosas é feito aqui.
    function calcular_total_nota(total_exames,glosas_valor,glosas_manuais,acrescimo,desconto,total_glosas){
        //glosas de imagem não são incluídas para a somatória do valor consolidado, apenas para o valor a pagar da nota
        var total = 0;
        
        total_exames   = total_exames.replace('.','');
        glosas_valor   = glosas_valor.replace('.', '');
        glosas_manuais = glosas_manuais.replace('.', '');

        var total = parseFloat(total_exames.replace(',', '.')) + parseFloat(glosas_valor.replace(',', '.')) + parseFloat(glosas_manuais.replace(',', '.'));
        

        if(acrescimo){
            acrescimo = acrescimo.replace('.','')
            total += parseFloat(acrescimo.replace(',', '.'));
        }
        if(desconto){
            desconto = desconto.replace('.','')
            total -= parseFloat(desconto.replace(',', '.'));
        }
        
        var total_a_pagar = 0;
        if(total_glosas){
            total_glosas  = total_glosas.replace('.','');
            total_a_pagar = total - (total_glosas.replace(',','.'))
        }
        

        total = total.toLocaleString('pt-br', {minimumFractionDigits: 2});
        
        total_a_pagar = total_a_pagar.toLocaleString('pt-br', {minimumFractionDigits: 2});
        $('.modal_concluir_nf #mdl_total_final').text(total)

        var total_nota = $('.modal_concluir_nf #mdl_concluir_valor_bruto').text();

        if(total == total_nota){
            $('.modal_concluir_nf .modal_data_concluir').prop("disabled",false);
        }else{
            $('.modal_concluir_nf .modal_data_concluir').prop("disabled",true);
        }


        return total_a_pagar;
    }

    $('.modal_concluir_nf').on('change','.valor_corrigido', function(){
        var total_exames = $('#mdl_valor_exames_consolidados').text();
        var glosas_valor = $('#mdl_valor_glosas_valor').text();
        var glosas_manuais = $('#mdl_valor_glosas_manuais').text();
        var total_glosas = $('#mdl_total_glosas').text();
        var acrescimo = $('#mdl_acrescimo').val();
        var desconto = $('#mdl_desconto').val();
        var total_a_pagar = calcular_total_nota(total_exames,glosas_valor,glosas_manuais,acrescimo,desconto,total_glosas);

        $('.modal_concluir_nf #mdl_valor_a_pagar').text(total_a_pagar);
    });

    $('.modal_concluir_nf').on('click','.btn_adicionar_valor',  function(){
        $('#mdl_tr_acrescimo').removeClass("hidden");
        $('#mdl_tr_desconto').removeClass("hidden");
    });

    $('.modal_concluir_nf').on('click','.modal_data_concluir',  function(){
        var validador = true;
        var nota_fiscal             = $('#mdl_codigo_nota_fiscal').text();
        var acrescimo               = $('#mdl_acrescimo').val();
        var desconto                = $('#mdl_desconto').val();
        var valor_glosas_valor      = $('#mdl_valor_glosas_valor').text();
        var valor_glosas_imagens    = $('#mdl_valor_glosas_imagens').text();
        var valor_glosas_manuais    = $('#mdl_valor_glosas_manuais').text();
        var total_glosas            = $('#mdl_total_glosas').text();
        var total_exames            = $('#mdl_valor_exames_consolidados').text();
        var motivo_acrescimo        = $('#mdl_motivo_acrescimo').val();
        var motivo_desconto         = $('#mdl_motivo_desconto').val();
        var valor_bruto             = $('#mdl_concluir_valor_bruto').val();
        var valor_consolidado       = $('#mdl_total_final').val();

        if(valor_bruto != valor_consolidado){
            validador = false;
        }

        if(acrescimo && motivo_acrescimo == 'Motivo'){
			swal("Atenção", "Campo motivo acrescimo deve ser preenchido.", "warning");
            validador = false;
        }

        if(desconto && motivo_desconto == 'Motivo'){
			swal("Atenção", "Campo motivo desconto deve ser preenchido.", "warning");
            validador = false;
        }

        if(motivo_acrescimo == 'Motivo'){
            motivo_acrescimo = null;
        }

        if(motivo_desconto == 'Motivo'){
            motivo_desconto = null;            
        }

        if(validador == true){
            $.ajax({
                type: "POST",
                url: '/portal/notas_fiscais_servico/salvar_conclusao_nf',
                dataType: 'json',      
                data: {
                    nota_fiscal           : nota_fiscal,
                    acrescimo             : acrescimo,
                    motivo_acrescimo      : motivo_acrescimo,
                    desconto              : desconto,
                    motivo_desconto       : motivo_desconto,
                    valor_glosas_valor    : valor_glosas_valor,
                    valor_glosas_imagens  : valor_glosas_imagens,
                    valor_glosas_manuais  : valor_glosas_manuais,
                    total_glosas          : total_glosas,
                    total_exames          : total_exames,
                },
                complete: function(dados){
                    if(dados){
                        swal({
                            title: 'Sucesso!',
                            text: 'Nota fiscal concluída com sucesso, deseja imprimir a ordem de pagamento?',
                            type: 'success',
                            showCancelButton: true,
                            confirmButtonClass: "btn-danger",
                            confirmButtonText: "Sim",
                            cancelButtonText: "Não",
                            closeOnConfirm: true,
                            closeOnCancel: true
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    imprimir_ordem_de_pagamento(nota_fiscal);          
                                }
                        });
                        $("#modal_concluir_nf").modal("hide");
                        atualizaListaNotaFiscalServico();

                    }else{
                        swal('Erro!', 'Erro ao concluir nota fiscal!', 'error');                        
                    }
                }
            });
        }        
    });
    
    function imprimir_ordem_de_pagamento(codigo_nota){
        var url = baseUrl + "/notas_fiscais_servico/imprimir_capa_de_lote/"+codigo_nota;
        window.location.href = url;           
    }

    function atualizaListaNotaFiscalServico() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "notas_fiscais_servico/consolida_nfs_exame_listagem/" + Math.random());
    }

    function reabrir_nota_fiscal(codigo_nota, x){
        //Coloquei em um id diferente para ele não sumir com a listagem de exames consolidados
        $('#icone_carregar_2'+x).show();

        carregamento = $('#carregado_'+x).val();

        if(carregamento != 1) {

            $('#icone_carregar_2'+x).show();
          
            $.ajax({
                
                type: 'POST',
                url: '/portal/notas_fiscais_servico/reabrir_nota_fiscal',

                dataType: 'json',      
                data: {
                    codigo_nota           : codigo_nota,
                },
                beforeSend: function() {
                    $('#icone_carregar_2'+x).html('<img src=\"/portal/img/loading.gif\">');
                },       
                success: function(dados) {
                    if(dados == true){
                        $('.corrigir_valor').removeClass('hidden');
                        $('.finalizar_nf').removeClass('hidden');
                        $('.imprimir_nf').addClass('hidden');
                        $('.reabrir_nota').addClass('hidden');
                    }                        
                },
                complete: function(dados) {
                    $('#icone_carregar_2'+x).hide();
                }
            
            }); 
        }

        
    }


    
</script>
