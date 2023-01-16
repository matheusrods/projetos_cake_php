<div class='well'>
  <?php echo $bajax->form('SmsOutbox', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'SmsOutbox', 'element_name' => 'sms'), 'divupdate' => '.form-procurar')) ?>
    <div id="filtros">
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this,'SmsOutbox','data_inicial','data_final',true); ?>
            <?php echo $this->BForm->input('fone_de', array('class' => 'input', 'placeholder' => 'Modem', 'label' => 'Modem','options' => $modem, 'empty' => 'QUALQUER', 'default' => 0)) ?>
            <?php echo $this->BForm->input('fone_para', array('class' => 'input-medium telefone', 'placeholder' => 'Celular', 'label' => 'Celular')) ?>  
            <?php echo $this->BForm->input('sistema_origem', array('class' => 'input', 'placeholder' => 'Sistema Origem', 'label' => 'Sistema Origem','options' => $sistema_origem, 'empty' => 'Selecione', 'default' => 0)) ?> 
        </div>        
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id' => 'btn-filtrar')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
    </div>    
  <?php echo $this->BForm->end() ?>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        setup_mascaras();
        
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "sms/listagem_sms/" + Math.random());
       
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:SmsOutbox/element_name:sms/" + Math.random())
        });
        
    });', false);
?>