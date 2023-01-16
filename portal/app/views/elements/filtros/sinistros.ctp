<div class='well'>
    <h5><?= $this->Html->link('Definir Filtros', 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show definir')) ?></h5>
    <div id='filtros'>
        <?php echo $bajax->form('Sinistro', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Sinistro', 'element_name' => 'sinistros'), 'divupdate' => '.form-procurar')) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this, 'Sinistro') ?>
            <?php echo $this->BForm->input('natureza', array('class' => 'input-medium', 'label' => false, 'options'=>$natureza, 'empty'=>'Tipo de Sinistro')); ?>
            <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_transportador','Transportador',false,'Sinistro') ?>
            <?php echo $this->Buonny->input_codigo_cliente($this,'codigo_embarcador','Embarcador',false,'Sinistro') ?>
            <?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa')); ?>
        </div>  
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_documento_profissional', array('class' => 'input-medium', 'label' => false, 'placeholder' => 'CPF Motorista')); ?>
            <?php echo $this->BForm->input('nome_profissional', array('class' => 'input-xlarge', 'label' => false, 'placeholder' => 'Motorista')); ?>
            <?php echo $this->BForm->input('codigo_seguradora', array('class' => 'input-large', 'label'=> false, 'options'=>$seguradoras, 'empty'=>'Seguradora')); ?>
            <?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora', 'Corretora', false, 'Sinistro') ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn btn-filtro')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        var div = jQuery("div.lista_sinistro");
        bloquearDiv(div);
        div.load(baseUrl + "/sinistros/listagem/" + Math.random());

        setup_mascaras();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Sinistro/element_name:sinistros/" + Math.random())
        });  

        $(".definir").click(function(event){
            jQuery("div#filtros").slideToggle("slow");
        })
    });', false);
?>