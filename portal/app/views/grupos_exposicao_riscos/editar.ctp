<?php echo $this->BForm->create('GrupoExposicaoRisco', array('url' => array('controller' => 'grupos_exposicao_riscos','action' => 'editar', $codigo_cliente, $codigo_grupo_exposicao, $codigo_grupos_exposicao_risco), 'type' => 'post')); ?>
<?php echo $this->element('grupos_exposicao_riscos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>

<div style="display:none;">
    <table id="listagem_epix">
        <tr class="linhas"  id="modelo_epi">
            <td><?php echo $this->BForm->input('GrupoExposicaoRiscoEpi.x.codigo_epi', array('label' => false, 'class' => 'input-xxlarge codigo_epi', 'readonly' => true, 'size'=> 1, 'options' => array())); ?></td>
            <td><?php echo $this->BForm->input('GrupoExposicaoRiscoEpi.x.controle_epi', array('label' => false, 'class' => 'input-medium controle_epi','options' => array('E' => 'Existente', 'R' => 'Recomendado'))); ?></td>
            <td><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash ', 'title' => 'Excluir Epi', 'onclick' => 'excluirEpi(this)')); ?></td>
      </tr>
    <table>
</div>

<div style="display:none;">
    <table>
    <tr class="linhas" id="modelo_epc">
        <td><?php echo $this->BForm->input('GrupoExposicaoRiscoEpc.x.codigo_epc', array('label' => false, 'class' => 'input-xxlarge codigo_epc', 'readonly' => true, 'size'=> 1, 'options' => array())); ?>
        </td>
        <td><?php echo $this->BForm->input('GrupoExposicaoRiscoEpc.x.controle_epc', array('label' => false, 'class' => 'input-medium controle_epc','options' => array('E' => 'Existente', 'R' => 'Recomendado'))); ?></td>
        <td><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash ', 'title' => 'Excluir EPC', 'onclick' => 'excluirEpc(this)')); ?></td>
    </tr>
    <table>
</div> 

<div style="display:none;">
    <table>
        <tr class="linhas"  id="modelo_fonte_geradora">
            <td><?php echo $this->BForm->input('GrupoExposicaoRiscoFonteGeradora.x.codigo_fonte_geradora', array('label' => false, 'class' => 'input-xxlarge codigo_fonte_geradora', 'readonly' => true, 'options' => array())); ?></td>
            <td><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-trash ', 'title' => 'Excluir Fonte Geradora', 'onclick' => 'excluirFonteGeradora(this)')); ?></td>
      </tr>
    <table>
</div>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaLista();
        jQuery("#limpar-filtro-grupo-exposicao-risco").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:GrupoExposicaoRisco/element_name:grupos_exposicao_riscos/codigo_cliente:'.$codigo_cliente.' /codigo_grupo_exposicao:'.$codigo_grupo_exposicao.'/" + Math.random())
        });
        
        function atualizaLista() {
          var div = jQuery("div.lista");
          bloquearDiv(div);
          div.load(baseUrl + "grupos_exp_riscos_fontes_gera/listagem/'.$codigo_cliente.'/" + Math.random());
        }
        
    });', false);
?>