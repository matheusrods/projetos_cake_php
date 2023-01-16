<div class='well'>
  <?php echo $bajax->form('ClienteProdutoContrato', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'ClienteProdutoContrato', 'element_name' => 'clientes_produtos_contratos'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo', array('class' => 'input-mini just-number', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
      <?php echo $this->BForm->input('razao_social', array('class' => 'input-medium', 'placeholder' => 'Nome', 'label' => false)) ?>
      <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'maxlength' => 18, 'label' => false)) ?>
      <?php echo $this->BForm->input('inscricao_estadual', array('class' => 'input-medium', 'placeholder' => 'RG/Inscrição Estadual', 'label' => false)) ?>
      <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos')); ?>
    
      <?php echo $this->BForm->input('contratos', array('label' => 'Sem Contratos', 'type'=>'checkbox', 'div' => 'control-group input checkbox')); ?>
    
    </div>        
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo_gestor', array('label' => false, 'class' => 'input-medium', 'options' => $gestores, 'empty' => 'Todos Gestores')); ?>
      <?php echo $this->BForm->input('codigo_cliente_tipo', array('class' => 'input-medium', 'label' => false, 'options' => $clientes_tipos, 'empty' => 'Todos Tipos')) ?>
      <?php echo $this->BForm->input('codigo_cliente_sub_tipo', array('class' => 'input-medium', 'label' => false, 'options' => $clientes_sub_tipos, 'empty' => 'Todos SubTipo')) ?>
      <?php echo $this->BForm->input('codigo_corretora', array('label' => false, 'class' => 'input-medium', 'options' => $corretoras, 'empty' => 'Todas Corretoras')); ?>
      <?php echo $this->BForm->input('codigo_seguradora', array('label' => false, 'class' => 'input-medium', 'options' => $seguradoras, 'empty' => 'Todas Seguradoras')); ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        //atualizaListaClientes("clientes_produtos_contratos");
        atualizaListaClientesProdutosContratosSemContatros("clientes_produtos_contratos");
        setup_datepicker();
        setup_mascaras();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteProdutoContrato/element_name:clientes_produtos_contratos/" + Math.random())
        });
        
        jQuery(".codigoClienteTipo").bind("change",
            function() {
                jQuery.ajax({
                    "url": baseUrl + "/clientes_sub_tipos/combo/" + jQuery(this).val() + "/" + Math.random(),
                    "success": function(data) {
                        
                        jQuery(".codigoClienteSubTipo").html(data).val();
                        
                    }
                });
            }
        );
    });', false);

?>
