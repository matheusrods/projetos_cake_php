<div class='well'>
  
    <?php echo $bajax->form('UsuarioGrupoCovid', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'UsuarioGrupoCovid', 'element_name' => 'usuario_grupo_covid'), 'divupdate' => '.form-procurar')) ?>
        
    <?php echo $this->Buonny->input_grupo_economico($this, 'UsuarioGrupoCovid', $unidades, $setores, $cargos,null); ?>
    
    <?php echo $this->element('usuario_grupo_covid/fields_filtros'); ?>

    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro-usuario-grupo-covid', 'class' => 'btn')) ;?>
  <?php echo $this->BForm->end() ?>
</div>

<?php $this->addScript($this->Buonny->link_js('comum.js')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListagem("usuario_grupo_covid/listagem/");
        
        jQuery("#limpar-filtro-usuario-grupo-covid").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:UsuarioGrupoCovid/element_name:usuario_grupo_covid/" + Math.random())
            if(importacao_pedidos_exame_botao.html()) {
                importacao_pedidos_exame_botao.html(" ");
            }
        });
    });', false);
?>