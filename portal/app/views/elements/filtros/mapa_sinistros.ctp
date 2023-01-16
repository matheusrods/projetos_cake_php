<div class='well'>
    <h5><?= $this->Html->link('Definir Filtros', 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show definir')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('Sinistro', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Sinistro', 'element_name' => 'mapa_sinistros'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'Sinistro') ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('type' => 'text', 'class' => 'input-small', 'label' => false, 'placeholder' => 'No. Sinistro')); ?>
            <?php echo $this->BForm->input('sm', array('class' => 'input-small', 'label' => false, 'placeholder' => 'No. SM')); ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador', 'Embarcador','', 'Sinistro') ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador', 'Transportador','', 'Sinistro') ?>
            <?php echo $this->BForm->input('codigo_documento_profissional', array('class' => 'input-small formata-cpf', 'type' => 'text', 'label' => false, 'placeholder' => 'CPF do Motorista' )); ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_seguradora', array('label' => false, 'class' => 'input-medium', 'options' => $seguradoras, 'empty' => 'Todas Seguradoras')); ?>
            <?php echo $this->BForm->input('codigo_corretora', array('class' => 'input-medium', 'label' => false,  'options' => $corretoras, 'empty' => 'Todas Corretoras')); ?>
        </div>

        <div class="row-fluid inline">
            <span class="label label-info">Tipo de Sinistro</span>
            <br />
            <?php echo $this->BForm->input('natureza', array('class' => 'checkbox inline', 
            'options' => array('Recuperado','Roubo Parcial','Furto Parcial', 'Roubo Total', 'Furto Total', 'Tentativa'), 
            'multiple' => 'checkbox', 
            'checked' => FALSE, 
            'label' => FALSE)); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn', 'id'=>'filtrar')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function() {
        setup_mascaras();        
        var div = jQuery("div.lista");
        bloquearDiv(div); 
        div.load(baseUrl + "/sinistros/listagem_mapa_sinistro/" + Math.random());
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Sinistro/element_name:mapa_sinistros/" + Math.random())
        });  

        $(".definir").click(function(event){
            jQuery("div#filtros").slideToggle("slow");
        })

        if( $("#SinistroCodigoEmbarcador").val() != "" )
            $("#SinistroCodigoEmbarcador").blur();

        if( $("#SinistroCodigoTransportador").val() != "" )
            $("#SinistroCodigoTransportador").blur();
                     
        
    });', false);
?>
