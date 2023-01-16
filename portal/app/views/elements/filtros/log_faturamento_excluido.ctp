<div class='well'>
<?php
$codigo_cliente = null;
$razao_social   = null;
if(isset($authUsuario['Usuario']['codigo_cliente']) && !empty($authUsuario['Usuario']['codigo_cliente']) && $authUsuario['Usuario']['codigo_cliente'] != '' ) {
    $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];
    $razao_social   = $authUsuario['Usuario']['nome'];
}
echo $this->Bajax->form('FichaScorecard', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'FichaScorecard', 'element_name' => 'log_faturamento_filtros'), 'divupdate' => '#filtros'))?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Código', TRUE,'FichaScorecard') ?>
        <?if( $codigo_cliente ) {?>
        <?php echo $this->BForm->input("codigo_cliente", array('label' => 'Código', 'class' => 'input-mini just-number', 'readonly'=>true, 'value' => $codigo_cliente)) ?>    
        <?}?>
        <?php echo $this->BForm->input("Cliente.razao_social", array('label' => 'Razão Social', 'class' => 'input-xxlarge', 'id'=>'ClienteRazaoSocial', 'readonly'=>true, 'value' => $razao_social)) ?>
    </div>

    <div class="row-fluid inline">         
        <?php echo $this->BForm->input('cpf', array('class' => 'input-medium cpf', 'label' => false, 'placeholder' => 'CPF')); ?>
        <?php echo $this->BForm->input('placa', array('class' => 'input-small placaveiculo', 'label' => false, 'placeholder' => 'Placa')); ?>
        <?php echo $this->BForm->input('usuario', array('type'=>'text','class' => 'input-small', 'label' => false, 'placeholder' => 'Usuário')); ?>
        <?php echo $this->BForm->input('tipos', array('class' => 'input-small', 'label' => false, 'type'=>'select','options'=>array('1'=>'Sem custos','2'=>'Com Custos'),'empty'=>'Tipos')); ?>
        <?php echo $this->BForm->input('tipo_operacao', array('class' => 'input-xxlarge', 'label' => false, 'type'=>'select','options'=>$tipo_operacao,'empty'=>'Operação')); ?>
    </div>
    <div class="row-fluid inline">
        <?php echo $this->BForm->input('data_inicial', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'Data Inicial')); ?>
        <?php echo $this->BForm->input('data_final', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'Data Final')); ?>
        <?php echo $this->BForm->input('num_consulta', array('class' => 'input-medium', 'label' => false, 'placeholder' => 'Num Consulta')); ?>
    </div>

    <div class="row-fluid inline">
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'id'=>'filtrar', 'class' => 'btn')) ?>
        <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div>
</div>

<?php $this->addScript($this->Buonny->link_js('fichas_scorecard')) ?>
<?php echo $this->Javascript->codeBlock('
    $(document).ready(function() {
        setup_codigo_cliente();
        setup_datepicker();  
    });', false);?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();         
        var div = jQuery("#lista");        
        jQuery("#filtrar").click(function(){     
            bloquearDiv(div);
            div.load(baseUrl + "fichas_scorecard/log_faturamento_excluido_listagem/" + Math.random());
        });
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form"));
            jQuery(".form").load(baseUrl + "/filtros/limpar/model:FichaScorecard/element_name:log_faturamento_excluido/" + Math.random())
        });
    });', false);

?>