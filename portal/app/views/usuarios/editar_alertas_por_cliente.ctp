<div class="content">
    <ul class="nav nav-tabs">
        <li class="active">
            <a href="#dados" data-toggle="tab">Dados do usuário</a>
        </li>
    </ul>
    <?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?>
    <div class="tab-content">
        <div class="tab-pane active" id="dados">
            <?php echo $this->BForm->create('Usuario', array('action' => 'editar_alertas_por_cliente', $this->passedArgs[0] )); ?>
            <?php echo $this->element('usuarios/fields_alertas_por_cliente'); ?>
        </div>
    </div>
</div>
<?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php $this->addScript($this->Buonny->link_js('autocomplete')) ?>