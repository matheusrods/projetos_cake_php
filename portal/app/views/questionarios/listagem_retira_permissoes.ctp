<?php if(!empty($lista_clientes_grupo)): ?>

    <input id="selecionar_todos" type="checkbox" class="checkbox"> Selecionar Todos
    
    <?php echo $this->BForm->create('ClienteQuestionarios', array('url' => array('controller' => 'questionarios','action' => 'salvar_retira_permissoes/'.$codigo_questionario))); ?>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="form-small"></th>
                <th class="form-small">Cód. Cliente</th>
                <th class="form-small">Razão Social</th>
                <th class="form-small">Nome Fantasia</th>
            </tr>
        </thead>
        <tbody>                
            
            <?php foreach($lista_clientes_grupo as $key => $dados) : ?>
                <tr>
                    <td>
                        <?php echo $this->BForm->input('UnidadeCodigo.'.$key.'.codigo', 
                            array(
                                'type' => 'checkbox', 
                                'label' => false, 
                                'value' => $dados['Unidade']['codigo'], 
                                'multiple', 
                                'hiddenField' => false,
                                'checked' => (in_array($dados['Unidade']['codigo'], $permissoes) ? 'checked' : ''),
                                'class' => 'unidades'
                            )
                        ); ?>                           
                    </td>
                    <td><?php echo $dados['Unidade']['codigo'];?></td>
                    <td><?php echo $dados['Unidade']['razao_social'];?></td>
                    <td><?php echo $dados['Unidade']['nome_fantasia'];?></td>
                </tr>
            <?php endforeach ?>
            
        </tbody>
    </table>
    
    <?php     
    echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary pull-left', 'id' => 'salvar')); 
    echo "<div id='salvando' style='width:30px; height:30px; float: left; margin: 0 10px 20px;'></div>";    
    echo $this->BForm->end();
    ?>  

<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<script type="text/javascript">
    jQuery(document).ready(function(){

        var i = 1;
        $('body').on('click', '.js-add-uni', function() {
            var html = $(this).parents('.js-encapsulado').find('.js-memory').html().replace(/xx/g, i).replace(/Xx/g, i).replace(/disabled="disabled"/g, '');
            $(this).parents('.js-encapsulado').append(html).find('.inputs-config.hide').show();
            $(this).removeClass('js-add-uni').addClass('js-remove-uni').attr('data-original-title', 'Remover Unidade').children('i').removeClass('icon-plus').addClass('icon-minus');
            $('[data-toggle="tooltip"]').tooltip();
            i++;
        });//FINAL CLICK js-add-cid
        
        $('body').on('click', '.js-remove-uni', function() {
            $(this).parents('.inputs-config').remove();
        });//FINAL CLICK js-remove-uni

        $('body').on('click', '.js-uni-click', function() {
            $(this).closest('.checkbox-canvas').find('.js-uni').val($(this).find('td:first-child').text());
            $(this).parents('.checkbox-canvas').find('.js-uni').val($(this).find('td:first-child').text());
            $('.seleciona-uni').remove();
        });//FINAL click CLASSE js-uni-click

        $('body').click(function(event) {
            $('.seleciona-uni').remove();
        });

        // ===============
        $("#selecionar_todos").click(function() { 
            var inputElements = $(this);
            if(inputElements[0].checked){
                $("input[type='checkbox']").prop('checked', $(this).prop('checked'));
            }else{
                location.reload();
            }
        });

        // $("#salvar").click(function() {
        //     $("#salvar").prop('disabled',true);
        //     $("#salvando").html('<img src=\"/portal/img/loading.gif\" title=\"carregando...\" />')
        // });

        //$(".unidades").click(function(event) { 
        $(".unidades").change(function(event) {
            var inputElements = $(this);
            for(var i=0; inputElements[i]; ++i){ 
                if(!inputElements[i].checked){
                    checkedValue = inputElements[i].value;
                    var url = baseUrl+"questionarios/deletar_permissao_ajax/<?php echo $codigo_questionario?>/"+checkedValue;

                    $.ajax({
                        url: url,
                        beforeSend : function(){            
                            $('.lista').html('<img src=\"/portal/img/loading.gif\" title=\"carregando...\" />');
                        },
                        success: function(result){
                            var div = jQuery("div.lista");
                                div.load(baseUrl + "questionarios/lista_permissoes/<?php echo $codigo_questionario?>/" + Math.random());
                            },
                        error: function(result){
                            console.log(result);
                        }
                    });
                }
            }

        });

    });
</script>