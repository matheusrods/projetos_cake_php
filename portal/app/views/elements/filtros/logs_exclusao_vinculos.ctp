<div class="well">  
    <div id='filtros'>  
    <?php echo $bajax->form('LogAtendimento', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogAtendimento', 'element_name' => 'logs_exclusao_vinculos'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">      
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente','LogAtendimento') ?>  
            <?php echo $this->BForm->input('codigo_documento',array('label' => 'CPF','type' => 'text','class' => 'input-medium cpf', 'placeholder' => 'CPF')) ?>
            <?php echo $this->BForm->input('usuario', array('class' => 'input-medium', 'label'=>'Usuário')) ?>
            <?php echo $this->BForm->input('data_inicial', array('label' => 'Data Inicial', 'placeholder' => 'Data Inicial', 'type' => 'text', 'class' => 'data input-small')); ?>
            <?php echo $this->BForm->input('data_final', array('label' => 'Data Final', 'placeholder' => 'Data Final', 'type' => 'text', 'class' => 'data input-small')); ?>
        </div>    
        <?php echo $this->BForm->submit('Filtrar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar filtro', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>  
</div>
<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard') )) ?>
<?php echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){
    $(".btn").click(function(){
      var div = jQuery("div.lista");
      bloquearDiv(div);
      div.load(baseUrl + "logs_exclusao_vinculos/listagem/" + Math.random());
    });        
    setup_mascaras();
    setup_codigo_cliente();
    setup_datepicker();
    jQuery("#limpar-filtro").click(function(){
        bloquearDiv(jQuery(".form-procurar"));           
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogAtendimento/element_name:logs_exclusao_vinculos/" + Math.random())
    });
});', false);?>