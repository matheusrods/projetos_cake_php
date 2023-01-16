<div class='well'>
  <?php echo $bajax->form('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Cliente', 'element_name' => 'clientes_grupos_homogeneos'), 'divupdate' => '.form-procurar')) ?>
    <div class="row-fluid inline">
      <?php echo $this->BForm->input('codigo', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => false, 'type' => 'text')) ?>
      <?php echo $this->BForm->input('razao_social', array('class' => 'input-medium uppercase', 'placeholder' => 'Nome', 'label' => false)) ?>
      <?php echo $this->BForm->input('codigo_documento', array('class' => 'input-medium', 'placeholder' => 'CNPJ/CPF', 'label' => false)) ?>
      <?php echo $this->BForm->input('inscricao_estadual', array('class' => 'input-medium', 'placeholder' => 'RG/Inscrição Estadual', 'label' => false)) ?>      
      <?php echo $this->BForm->input('ativo', array('label' => false, 'class' => 'input-small', 'options' => array('Inativos', 'Ativos'), 'empty' => 'Todos', 'default' => 1)); ?>
      <?php echo $this->BForm->input('ultima_atualizacao', array('placeholder'=>'Atualização', 'label' => false, 'class' => 'input-small data', 'title' => 'Clientes com atualização anteriores à')); ?>      
    </div>        
    <div class="row-fluid inline">
    	<?php echo $this->BForm->input('codigo_gestor', array('label' => false, 'class' => 'input-medium', 'options' => isset($gestores) ? $gestores : array(), 'empty' => 'Todos Gestores Comerciais')); ?>
    	<?php echo $this->BForm->input('codigo_gestor_contrato', array('label' => false, 'class' => 'input-medium', 'options' => isset($gestores) ? $gestores : array(), 'empty' => 'Todos Gestores de Contrato')); ?>
    	<?php echo $this->BForm->input('codigo_gestor_operacao', array('label' => false, 'class' => 'input-medium', 'options' => isset($gestores) ? $gestores : array(), 'empty' => 'Todos Gestores de Operação')); ?>
    	
      <?php echo $this->BForm->input('codigo_corretora', array('label' => false, 'class' => 'input-medium', 'options' => isset($corretoras) ? $corretoras : array(), 'empty' => 'Todas Corretoras')); ?>
      <?php echo $this->Buonny->input_codigo_endereco_regiao($this, isset($filiais) ? $filiais : array(), 'Todas Regiões','codigo_endereco_regiao', false, 'Cliente') ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
      <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ; ?>
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaClientes("clientes_grupos_homogeneos");
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Cliente/element_name:clientes_grupos_homogeneos/" + Math.random())
        });
    });', false);

?>
