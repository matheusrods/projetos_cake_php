<?php if(is_array($array_exames) && count($array_exames) >= 1) : ?>
    <?php foreach ($array_exames as $codigo_exame => $exame): ?>
        <div class="double-scroll">
            <table class="table table-striped">
                <thead>
                    <br><br>
                    <?php if(count($consolidadas[$codigo_exame]) > 1) : ?>
                        <h4><?= $exame['exame']?> - <?= count($consolidadas[$codigo_exame]) ?> Exames</h4>  
                    <?php else :?>    
                        <h4><?= $exame['exame']?> - <?= count($consolidadas[$codigo_exame]) ?> Exame</h4>  
                    <?php endif; ?>
                    <div>
                        <tr>
                            <th>Selecionar todos</th>
                            <th>Codigo do Exame</th>
                            <th>Pedido de Exame</th>
                            <th>Funcionário</th>
                            <th>Valor Custo</th>
                            <th>Valor Cobrado</th>
                        </tr>
                    </div>
                   <div>
                        <tr>
                            <th><input id="<?php echo $codigo_exame ?>" type="checkbox" onchange="ajustar_a_todos(<?= $codigo_exame ?>, <?= count($consolidadas[$codigo_exame]) ?>)" data-toggle="tooltip" data-placement="top" class="all_<?= $codigo_exame ?>" title="Selecione os exames em que deseja atualizar os valores. Aqui você pode marcar e desmarcar todos os exames, ou pode selecionar individualmente nas caixas abaixo" /></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class='aplicar_a_todos_<?= $codigo_exame?>', style="text-align: left;" onkeyup="aplicar_a_todos(<?= $codigo_exame ?>, <?= count($consolidadas[$codigo_exame]) ?>)">
                                <?php echo $this->BForm->input('valor_corrigido_'.$codigo_exame, array('class' => 'input-small valor_corrigido valor_corrigido_todos', 'value' => null ,'label' => false, 'data-toggle'=>'tooltip', 'data-placement'=>"right",'title'=>'Aqui você pode aplicar o mesmo valor a todos os exames','maxlength'=>9)); ?>
                            </th>
                        </tr>

                   </div>
               

                </thead>
                <tbody>
                    <?php $total[$codigo_exame] = 0 ?>
                    <?php $totalCorrigido[$codigo_exame] = 0 ?>
                    <?php foreach($consolidadas[$codigo_exame] as $key => $value) : ?>

                        <tr>
                            <td id="codigo_exame">
                                <?php echo $this->BForm->input('exame'.$key.'codigo', array('onchange'=>"ajustaValores('{$key}','{$codigo_exame}')",'type'=>'checkbox', 'id'=>'exame_'.$key.'_'.$codigo_exame , 'label' => false,'value'=> $value['codigo_item_pedido_exame'], 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge pedido_exame_codigo_'.$codigo_exame)); ?>
                            </td>
                            <td><?= $value['codigo_item_pedido_exame'] ?></td>
                            <td><?= $value['codigo_pedido_exame'] ?></td>
                            <td><?= $value['funcionario_nome'] ?></td>
                            <td id='valor_custo_<?=$key?>_<?= $codigo_exame ?>'><?= $value['valor_custo'] ?></td>
                            <td class='corrigido_<?= $codigo_exame?>', style="text-align: left;" onkeyup="comparaValores(<?= $key ?>,<?= $codigo_exame ?>)">
                                <?php echo $this->BForm->input('valor_corrigido_'.$key.'_'.$codigo_exame, array('disabled'=>'true','class' => 'input-small valor_corrigido  valor_corrigido_'.$codigo_exame,'data-toggle'=>'tooltip', 'data-placement'=>"right",'title'=>'Aqui você pode aplicar um valor a um exame específico', 'value' => isset($value['valor_corrigido']) ? $value['valor_corrigido'] : "",'label' => false, 'maxlength'=>9)); ?>
                            </td>
                        </tr>
                    <?php $total[$codigo_exame] += str_replace(',','.',$value['valor_custo']) ?>
                    <?php $totalCorrigido[$codigo_exame] += isset($value['valor_corrigido']) ? str_replace(',','.',$value['valor_corrigido']) : str_replace(',','.',$value['valor_custo']) ?>

                    <?php endforeach; ?>    
                </tbody>
            </table>
        <tfoot>
            <div>
                <br>
                <span>
                    <th>Total valor custo: </th>
                    <span>R$ <?= $this->Buonny->moeda($total[$codigo_exame]) ?></span>
                    <br>
                    <th>Total valor cobrado: </th>
                    <span id="total_corrigido_<?= $codigo_exame ?>">R$ <?= $this->Buonny->moeda($totalCorrigido[$codigo_exame]) ?></span>
                </span>
            </div>
            <div>
                <button class="btn btn-success" data-toggle="tooltip", data-placement="right", title="Ao clicar no botão, verifique se o exame está selecionado e com o valor correto", onclick="salvar_consolidacao(<?= $codigo_exame ?>,);">APLICAR VALORES CORRIGIDOS</button>
            </div>
        </tfoot>
        </div>
    <?php endforeach; ?>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js"></script>

<script>
	$(document).ready(function(){    
        $(".valor_corrigido").mask("999,99");        
	});

    function ajustar_a_todos(codigo_exame, consolidadas){
        all = $('body .all_'+codigo_exame);
        var checado = all.is(":checked");

        $('.pedido_exame_codigo_'+codigo_exame).prop('checked', checado);
        
        if(checado){
            $('.valor_corrigido_'+codigo_exame).prop("disabled",false);
            var valor_all = $('#valor_corrigido_'+codigo_exame).val();
            if(valor_all && valor_all != ','){
                var key = 0;

                while (key < consolidadas) {

                    $('#valor_corrigido_'+key+'_'+codigo_exame).val(valor_all);
                    comparaValores(key,codigo_exame);  
                    
                    key++; 
                }  
            }
        }else{
            $('.valor_corrigido_'+codigo_exame).prop("disabled",true);
        }
    }

    function ajustaValores(chave,codigo_exame){
        var checado = $('#exame_'+chave+'_'+codigo_exame).is(":checked");

        if(checado){
            $('#valor_corrigido_'+chave+'_'+codigo_exame).prop("disabled",false);

            var valor_all = $('#valor_corrigido_'+codigo_exame).val();
            if(valor_all && valor_all != ','){
                $('#valor_corrigido_'+chave+'_'+codigo_exame).val(valor_all);
                comparaValores(chave,codigo_exame);  
            }
        }else{
            $('#valor_corrigido_'+chave+'_'+codigo_exame).prop("disabled",true);
        }
    }
    
    function comparaValores(chave,codigo_exame) {
        var valor_custo = $('#valor_custo_'+chave+'_'+codigo_exame).text();
        var valor_corrigido = $('#valor_corrigido_'+chave+'_'+codigo_exame).val();

        if(valor_custo != valor_corrigido){
            $('#valor_corrigido_'+chave+'_'+codigo_exame).css("color","red");
        }else {
            $('#valor_corrigido_'+chave+'_'+codigo_exame).css("color","black");
        }

        classe_corrigido = $('.corrigido_'+codigo_exame);

        var total = null;
        $.each(classe_corrigido, function(key,val){
            valor = parseFloat($('#valor_corrigido_'+ key +'_' + codigo_exame).val().replace(',', '.'));
            total += valor;
        });
        if(isNaN(total)){
            total = 0;
            total = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        } else{
            total = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }

        $('#total_corrigido_'+codigo_exame).text(total);
    }

    

    function aplicar_a_todos(codigo_exame, consolidadas){
        var valor_all = $('#valor_corrigido_'+codigo_exame).val();
        if(valor_all && valor_all != ','){
            var key = 0;

            while (key < consolidadas) {
                var checado = $('#exame_'+key+'_'+codigo_exame).is(":checked");
                if(checado == true){
                    $('#valor_corrigido_'+key+'_'+codigo_exame).val(valor_all);
                    comparaValores(key,codigo_exame);                    
                }     
                key++;           
            }
        }
    }

    

    $(document).on("click", ".botao_aplicar",function(){
        nota_fiscal = $('#notas_fiscais').val();
        if(nota_fiscal && exames_selecionados){
             salvar_consolidacao(nota_fiscal, exames_selecionados);
        }else   {
            alert("Erro ao selecionar os dados");
        }
    });


    function salvar_consolidacao(codigo_exame) {
        var exames_selecionados = [];
        var valor_corrigido = [];
        retorno = false; 

        $('.pedido_exame_codigo_'+codigo_exame+ ':checkbox').each(function(key){
            if ($(this).is(":checked")) {
                
                exames_selecionados[key] = $(this).val();
                valor_corrigido[key] = $('#valor_corrigido_'+key+'_'+codigo_exame).val();
                if(exames_selecionados && valor_corrigido){					     			
                    retorno = true;         
                }																
            }
        });
        if(retorno == true){
            $.ajax({
                type: "POST",
                url: '/portal/notas_fiscais_servico/salvar_valor_corrigido',
                dataType: 'json',      
                data: {valor_corrigido: valor_corrigido, exames_selecionados: exames_selecionados},
                complete: function(dados){
                    if(dados){
                        alert('Dados salvos com sucesso!');
                    }else{
                        alert("Erro ao salvar os dados"); 
                    }
                }
            
            });    
        }else{
            alert("Selecione um exame para prosseguir"); 
        }
          
    }

</script>