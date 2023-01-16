<?php $natureza  = array('Recuperado','Roubo Parcial','Furto Parcial', 'Roubo Total', 'Furto Total', 'Tentativa');  ?>
<div class='well'>
    <h5><?= $this->Html->link('Definir Filtros', 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show definir')) ?></h5>
    <div id='filtros'>

        <?php echo $bajax->form('Sinistro', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Sinistro', 'element_name' => 'sinistros_analitico'), 'divupdate' => '.form-procurar')) ?>

        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'Sinistro') ?>
        </div>  
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo', array('class' => 'input-small', 'label' => false, 'placeholder' => 'No. Sinistro', 'type' => 'text')); ?>
            <?php echo $this->BForm->input('Sinistro.sm', array('class' => 'input-small', 'label' => false, 'placeholder' => 'No. SM')); ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador', 'Embarcador','', 'Sinistro') ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador', 'Transportador','', 'Sinistro') ?>
            <?php echo $this->BForm->input('natureza', array('class' => 'input-medium', 'label' => false, 'options'=>$natureza, 'empty'=>'Tipo Sinistro')); ?>
            <?php echo $this->BForm->input('codigo_documento_profissional', array('class' => 'input-small formata-cpf', 'type' => 'text', 'label' => false, 'placeholder' => 'CPF do Motorista' )); ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('seguradora', array('label' => false, 'class' => 'input-medium', 'options' => $seguradoras, 'empty' => 'Todas Seguradoras')); ?>
            <?php echo $this->BForm->input('corretora', array('class' => 'input-medium', 'label' => false,  'options' => $corretoras, 'empty' => 'Todas Corretoras')); ?>

        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function() {
        setup_mascaras();
        atualizaListaAnaliticoSinistros();
        
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Sinistro/element_name:sinistros_analitico/" + Math.random())
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
<? //debug(count($corretoras)) ?>
<? //debug(count($seguradoras)) ?>
<? //debug($this->data['Sinistro']); ?>
