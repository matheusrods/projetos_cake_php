<div class='well'>
  <?php echo $bajax->form('Prestador', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Prestador', 'element_name' => 'prestadores_buscar_codigo', 'searcher' => $input_id), 'divupdate' => '.form-procurar-codigo-prestador')) ?>
    <div class="row-fluid inline endereco">
            <?php echo $this->BForm->input('endereco', array('label' => false, 'placeholder' => 'Endereço', 'class' => 'input-xlarge')); ?>
            <?php echo $this->BForm->input('latitude', array('label' => false, 'placeholder' => 'Latitude', 'class' => 'input-small numeric')); ?>
            <?php echo $this->BForm->input('longitude', array('label' => false, 'placeholder' => 'Longitude', 'class' => 'input-small numeric')); ?>
            <?php echo $this->BForm->input('raio', array('label' => false, 'placeholder' => 'Raio (KM)', 'class' => 'input-small numeric')); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-prestador', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>

</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
        jQuery("#limpar-filtro-prestador").click(function(){
            bloquearDiv(jQuery(".form-procurar-codigo-prestador"));
            jQuery(".form-procurar-codigo-prestador").load(baseUrl + "/filtros/limpar/model:Prestador/element_name:prestadores_buscar_codigo/searcher:'.$input_id.'/" + Math.random())
        });        
    });', false);
?>