<div class='well'>
  <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'clientes_funcionarios_laudo_pcd' ), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
      <?php echo $this->BForm->input('razao_social', array('class' => 'input-xlarge', 'placeholder' => 'Razão Social', 'label' => false)) ?>
      <?php echo $this->BForm->input('nome_fantasia', array('class' => 'input-xlarge', 'placeholder' => 'Nome Fantasia', 'label' => false)) ?>
      <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>  
      <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'default' => 1)); ?>
    </div>        
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        AtualizaLista();
        setup_datepicker();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:clientes_funcionarios_laudo_pcd/" + Math.random())
        });
    });

      function AtualizaLista() {
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "clientes/listagem_cliente_funcionario_laudo_pcd/" + Math.random());
      }
    ', false);
?>
