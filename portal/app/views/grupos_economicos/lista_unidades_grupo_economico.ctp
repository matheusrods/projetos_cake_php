<?php if(!empty($lista_clientes_grupo)):?>
    <div style=" position: relative; left:0px; float: left;">
        <table class="table table-striped" style="width: 500px;">
            <thead>
                <tr>
                    <th class="form-small">Cód. Cliente</th>
                    <th class="form-small">Razão Social</th>
                    <th class="form-small">Nome Fantasia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($lista_clientes_grupo as $dados) : ?>
                    <tr>
                        <td><?php echo $dados['Unidade']['codigo'];?></td>
                        <td><?php echo $dados['Unidade']['razao_social'];?></td>
                        <td><?php echo $dados['Unidade']['nome_fantasia'];?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <div style="position:relative; margin-left: 10px; float: left;">
        <?php echo $this->BForm->create('GrupoEconomico', array('url' => array('controller' => 'grupos_economicos','action' => 'separar_config_grupos',$codigo_cliente))); ?>
            <div class="well">
                <div class='row-fluid inline'>
                    <?php echo $this->BForm->input('novo_codigo_unidade_grupo_economico',  array('options' => $lista_unidades_combo_ge, 'empty' => 'Selecione a Unidade', 'label' => 'Selecione qual é a Unidade que vai ser a Matriz do Grupo Economico', 'type' => 'select', 'class' => 'input-xxlarge')); ?>
                </div>
            </div>

            <div class="row-fluid inline">
                <div class="js-encapsulado">  

                    <div class="inputs-config " style="margin-left: 0; margin-right: 1%">
                        <div class="checkbox-canvas">
                            <div class="row-fluid">
                                <div class="span12">
                                    <?php echo $this->BForm->input('unidade.0.codigo', 
                                        array(
                                            'options' => $lista_unidades_combo, 
                                            'empty' => 'Selecione a Unidade',
                                            'label' => 'Unidade', 
                                            'class' => 'js-uni', 
                                            'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
                                            'div' => 'control-group input text width-full padding-left-10', 
                                            'required' => false, 
                                            'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-uni pointer pull-right" data-toggle="tooltip" title="Adicionar nova Unidade"><i class="icon-plus" ></i></span style="margin-top: -7px">')
                                    ); 
                                    ?>
                                </div>  
                            </div>
                        </div>
                    </div>

                    <div class="js-memory hide">
                        <div class="inputs-config hide " style="margin-left: 0; margin-right: 1%">
                            <div class="checkbox-canvas">
                                <div class="row-fluid">
                                    <div class="span12">
                                        <?php echo $this->BForm->input('unidade.xx.codigo', 
                                            array(
                                                'options' => $lista_unidades_combo, 
                                                'empty' => 'Selecione a Unidade',
                                                'label' => 'Unidade', 
                                                'class' => 'js-uni', 
                                                'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
                                                'div' => 'control-group input text width-full padding-left-10', 
                                                'required' => false, 
                                                'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-uni pointer pull-right" data-toggle="tooltip" title="Adicionar nova Unidade"><i class="icon-plus" ></i></span style="margin-top: -7px">')
                                        ); 
                                        ?>
                                    </div>  
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class='form-actions'>
                <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>                
            </div>

        <?php echo $this->BForm->end(); ?>
    </div>

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
        

    });
</script>