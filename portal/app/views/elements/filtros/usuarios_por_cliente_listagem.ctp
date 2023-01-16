<div class='well' id='filtros'>
        <?php echo $bajax->form('Usuario', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Usuario', 'element_name' => 'usuarios_por_cliente_listagem'), 'divupdate' => '.form-procurar')) ?>
            <?= $this->element('filtros/usuario_por_cliente_filtros') ?>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
</div>
<?php $codigo = !empty($this->data['Usuario']['codigo_cliente']) ? $this->data['Usuario']['codigo_cliente'] : '0';?>
<?php echo $this->Javascript->codeBlock('
     jQuery(document).ready(function(){
     atualizaListaUsuariosPorCliente('.$codigo.');
     
     jQuery("#limpar-filtro").click(function() {
        bloquearDiv(jQuery(".form-procurar"));
        jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Usuario/element_name:usuarios_por_cliente_listagem/" + Math.random())
        location.reload();
    });

});', false);?>