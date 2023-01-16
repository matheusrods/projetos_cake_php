<p><strong>Emails: </strong></p>

<div class="input-group">
    <input type="checkbox" class="liberacao_select_all" style="margin:10px;"> Selecionar Todos</span>
</div>

<?php $codigo_inicial_email = 0; ?>

<div id="emails">
    <?php if(isset($contatos_cliente['ClienteContato']) && count($contatos_cliente['ClienteContato']) > 1) : ?>
        <?php   $i = count($contatos_cliente)-1; ?>
        <?php foreach ($contatos_cliente['ClienteContato'] as $key => $dados): ?>

            <div class="inputs-config span12 hide" style="margin-left: 0; margin-right: 1%; display: block;">
                <div class="checkbox-canvas">
                    <div class="row-fluid">
                        <div class="span12 email_checando" style="display:inline-flex">
                            
                            <input type="checkbox" name="data[ClienteContato][<?= $key; ?>][email]" data-codigo="<?= $key; ?>" id="ClienteContato_<?= $key; ?>email_check" class="select_liberacao checkbox_emails check_email_<?= $key; ?>" style="float: left; margin:10px;"/>

                            <?php echo $this->BForm->input('ClienteContato.'.($key).'.email', array(
                            'value' => $dados['ClienteContato']['descricao'],
                            'data-codigo' => $key, 
                            'class' => 'js-cid-10 descricao-email', 
                            'label' => 'Email', 
                            'style' => 'width: 44%; margin-bottom: 0; margin-top: -6px', 
                            'div' => 'control-group input text width-full padding-left-10', 
                            'after' => '<span style="margin-top: -7px;margin-left: 40px" class="btn btn-default js-remove-email pointer" data-toggle="tooltip" title="Remover Email"><i class="icon-minus" ></i></span>')); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php $codigo_inicial_email = $key + 1; ?>
    <?php endif; ?>
</div>

<div class="js-encapsulado">
    <div class="inputs-config span12" style="margin-left: 0; margin-right: 1%">
        <div class="checkbox-canvas">
            <div class="row-fluid">
                <div class="span12 email_checando" style="display:inline-flex">
                    <input type="checkbox" name="data[ClienteContato][<?= $codigo_inicial_email; ?>][email]" data-codigo="<?= $codigo_inicial_email; ?>" id="ClienteContato_<?= $codigo_inicial_email; ?>email_check" class="select_liberacao checkbox_emails check_email_<?= $codigo_inicial_email; ?>" style="float: left; margin:10px;"/>

                        <?php echo $this->BForm->input('ClienteContato.'.($codigo_inicial_email).'.email', array(                        
                        'data-codigo' => $codigo_inicial_email, 
                        'class' => 'js-cid-10 descricao-email', 
                        'label' => 'Email', 
                        'style' => 'width: 44%; margin-bottom: 0; margin-top: -6px', 
                        'div' => 'control-group input text width-full padding-left-10', 
                       'after' => '<span style="margin-top: -7px;margin-left: 40px" class="btn btn-default js-add-email pointer" data-toggle="tooltip" title="Adicionar novo Email"><i class="icon-plus" ></i></span style="margin-top: -7px;margin-left: 40px">')); ?>
                </div>  
            </div>
        </div>
    </div>
    <div class="js-memory hide">
        <div class="inputs-config hide span12" style="margin-left: 0; margin-right: 1%">
            <div class="checkbox-canvas">
                <div class="row-fluid">
                    <div class="span12 email_checando" style="display:inline-flex">
                        <input type="checkbox" name="data[ClienteContato][xx][email]" data-codigo="xx" id="ClienteContato_xxemail_check" class="select_liberacao checkbox_emails check_email_xx" style="float: left; margin:10px;"/>
                        <?php echo $this->BForm->input('ClienteContato.xx.email', 
                            array('label' => 'Email', 
                                'data-codigo' => 'xx', 
                                'class' => 'js-cid-10 descricao-email', 
                                'style' => 'width: 44%; margin-bottom: 0; margin-top: -6px', 
                                'div' => 'control-group input text width-full padding-left-10', 
                                'required' => false, 
                                'after' => '<span style="margin-top: -7px;margin-left: 40px" class="btn btn-default js-add-email pointer" data-toggle="tooltip" title="Adicionar novo Email"><i class="icon-plus" ></i></span style="margin-top: -7px;margin-left: 40px">')
                        ); 
                        ?>
                    </div>  
                </div>
            </div>
        </div>
    </div>
</div>
