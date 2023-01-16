<div class='well'>
    <?php echo $this->BForm->create('TEviaEstaViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'estatisticas_viagens', 'action' => 'listar_sms'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_embarcador', 'Embarcador', false, 'TEviaEstaViagem'); ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_transportador', 'Transportador', false, 'TEviaEstaViagem'); ?>
            <?php echo $this->BForm->input('codigo_seguradora', array('type' => 'select', 'options' => $seguradoras, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Seguradoras')); ?>
            <?php echo $this->BForm->hidden('codigo_corretora_pjur'); ?>
            <?php echo $this->Buonny->input_codigo_corretora($this, 'codigo_corretora', 'Corretora', false, 'TEviaEstaViagem'); ?>
            <?php echo $this->BForm->input('tecn_codigo', array('class' => 'input-medium', 'label' => false, 'options' => $tecnologias,'empty' => 'Tecnologia')) ?>
            <?php echo $this->BForm->input('usua_oras_codigo', array('class' => 'input-medium', 'label' => false, 'options' => $operadores, 'empty' => 'Operador')) ?>
        </div>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('tipo_viagem', array('class' => 'input-medium', 'label' => false, 'options' => $tipos_viagem)) ?>
            <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php if (!empty($lista)): ?>
    <div class="lista">
        <?php 
            echo $this->Paginator->options(array('update' => 'div.lista')); 
        ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('SM', 'SM') ?></th>
                    <th><?= $this->Paginator->sort('Embarcador', 'Embarcador') ?></th>
                    <th><?= $this->Paginator->sort('Transportador', 'Transportador') ?></th>
                    <th><?= $this->Paginator->sort('Seguradora', 'Seguradora') ?></th>
                    <th><?= $this->Paginator->sort('Corretora', 'Corretora') ?></th>
                    <th><?= $this->Paginator->sort('Tecnologia', 'Tecnologia') ?></th>
                    <th><?= $this->Paginator->sort('Operador', 'Operador') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista as $viagem): ?>
                    <tr>
                        <td><?= $this->Buonny->codigo_sm($viagem['TEviaEstaViagem']['evia_viag_codigo_sm']) ?></td>
                        <td><?= $viagem['Embarcador']['pjur_razao_social'] ?></td>
                        <td><?= $viagem['Transportador']['pjur_razao_social'] ?></td>
                        <td><?= $viagem['TEviaEstaViagem']['evia_segu_nome'] ?></td>
                        <td><?= $viagem['TEviaEstaViagem']['evia_corr_nome'] ?></td>
                        <td><?= $viagem['TTecnTecnologia']['tecn_descricao'] ?></td>
                        <td><?= $viagem['TUsuaUsuario']['usua_login'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7"><strong>Total: </strong><?php echo $this->Paginator->counter(array('format' => '%count%')); ?></td>
                </tr>
            </tfoot>
        </table>
        <div class='row-fluid'>
            <div class='numbers span6'>
                <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
            </div>
            <div class='counter span6'>
                <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            </div>
        </div>
        <?php echo $this->Javascript->codeBlock("
            jQuery(document).ready(function(){
                $('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });
            });", false);
        ?>
    </div>
<?php endif; ?>
<?php echo $this->Js->writeBuffer(); ?>