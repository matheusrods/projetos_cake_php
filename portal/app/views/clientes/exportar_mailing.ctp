<?php
echo $this->BForm->create('Cliente');
?>

<div class="well">
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('ClienteEndereco.endereco_codigo_estado',   array('options' => $estados,    'empty' => 'Selecione o estado','placeholder' => 'Selecione o estado','label' => false, 'class' => 'evt-carrega-cidade evt-estado input-large')); ?>
        <?php echo $this->BForm->input('ClienteEndereco.endereco_codigo_cidade',   array('options' => $cidades,    'empty' => 'Selecione a cidade', 'placeholder' => 'Cidade','label' => false, 'type' => 'select', 'class' => 'evt-carrega-bairro evt-cidade input-large')); ?>
        <?php echo $this->BForm->input('codigo_corretora',  array('options' => $corretoras, 'empty' => 'Selecione a corretora', 'label' => false, 'type' => 'select', 'class' => 'input-large')); ?>
        <?php echo $this->BForm->input('codigo_seguradora', array('options' => $seguradoras,'empty' => 'Selecione a seguradora', 'label' => false, 'type' => 'select', 'class' => 'input-large')); ?>        
    </div>
    <div class='row-fluid inline'>
        <?php echo $this->BForm->input('ClienteContato.codigo_tipo_contato', array('options' => $tipo_contato, 'empty' => 'Selecione o tipo contato','label' => false, 'class' => 'input-large')); ?>        
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
</div>
    
<?php echo $this->BForm->end(); ?>

<?php if( isset($dados) && !empty($dados) ): ?>

    <div class="well">
        <span style="font-size:18px; color:red;"><?= $dados ?></span> <strong>Registros encontrados</strong>
    </div>

    <div class='actionbar-left'>
        <?php echo $this->Html->link('<i class="icon-share-alt icon-white"></i> Exportar Contatos', array(''), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Exportar Contatos')); ?>
    </div>

    <br />

    <?php echo $this->BForm->create('Cliente', array('url' => array('action' => 'incluir_mailing_list'))); ?>

        <div id="lista-contatos" style="display:none;">
            <div class='row-fluid inline'>
                <?php echo $this->BForm->input('lista_contato', array('options' => $lista_de_contatos, 'empty' => 'Selecione uma lista de contatos','label' => false, 'class' => 'input-xlarge')); ?>
                <?php echo $this->BForm->input('ClienteEndereco.endereco_codigo_estado',   array('type' => 'hidden')); ?>
                <?php echo $this->BForm->input('ClienteEndereco.endereco_codigo_cidade',   array('type' => 'hidden')); ?>
                <?php echo $this->BForm->input('codigo_corretora',  array('type' => 'hidden')); ?>
                <?php echo $this->BForm->input('codigo_seguradora', array('type' => 'hidden')); ?>
                <?php echo $this->BForm->input('ClienteContato.codigo_tipo_contato',      array('type' => 'hidden')); ?>
            </div>            
            <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary salvar-lista')); ?>
            <style type="text/css">.salvar-lista{ display: none; }</style>
        </div>
    
    <?php echo $this->BForm->end(); ?>
  
<?php endif; ?>

<?php echo $javascript->codeblock(
    
    'jQuery(document).ready(function() {        

        $(".btn-success").click(function(event){
            event.preventDefault();                    
            $("#lista-contatos").slideToggle("show");
        })

        $("#ClienteListaContato").change(function(){
            if( $(this).val() != "" )
                $(".salvar-lista").show();  
        })

    });'    

); 
?>


