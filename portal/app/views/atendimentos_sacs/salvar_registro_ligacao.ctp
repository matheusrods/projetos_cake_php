<div class='well'>
<?php if(!empty($viagens)):?>
    <h4>SMs</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">SM</th>
                <th>Status</th>
                <th class="input-mini">Codigo</th>
                <th>Transportadora</th>
                <th class="input-mini">Codigo</th>
                <th>Embarcador</th>
                <th >Motorista</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($viagens as $sm): ?>
                <tr>
                    <td><?php echo $this->Buonny->codigo_sm($sm['TViagViagem']['viag_codigo_sm']) ?></td>
                    <td><?php echo $sm['status_sms'] ?></td>
                    <td><?php echo $transportador_cod ?></td>
                    <td><?php echo $sm['Transportador']['pess_nome'] ?></td>
                    <td><?php echo $embarcador_cod ?></td>
                    <td><?php echo $sm['Embarcador']['pess_nome'] ?></td>
                    <td><?php echo $sm['Motorista']['pess_nome'] ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>  
    </table>
<?php else:?>
    <div class="alert">Nenhuma SM foi encontrada.</div>
<?php endif;?>    
</div>
<div class='well'>
<?php if(!empty($atendimentos)):?>
    <h4>Ultimos registros</h4>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-mini">SM</th>
                <th class="input-mini">Atendente</th>
                <th class="input-mini">Ramal</th>
                <th class="input-medium">Data Cadastrada</th>
                <th>Motivo</th>
                <th>Tecnologia</th>
                <th style='width:60px'></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($atendimentos as $atendimento): ?>
                <tr>
                    <td><?php echo $this->Buonny->codigo_sm($atendimento[0]['sm']) ?></td>
                    <td><?php echo $atendimento[0]['apelido'] ?></td>
                    <td><?php echo $atendimento[0]['ramal'] ?></td>
                    <td><?php echo date('d/m/Y H:i:s', strtotime(str_replace('/', '-', $atendimento[0]['data_cadastrada']))) ?></td>
                    <td><?php echo $atendimento[0]['motivo'] ?></td>
                    <td><?php echo $atendimento[0]['tecnologia'] ?></td>
                    <td><?php echo $this->Html->link('', array('action' => 'visualizar', $atendimento[0]['codigo'], rand()), array('title' => 'Visualizar', 'class' => 'icon-eye-open')) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>  
    </table>
    <?php else:?>
        <div class="alert">Nenhum dado foi encontrado.</div>
    <?php endif;?>    
</div>

<div class='well inline'>
        <strong>Placa: </strong><?php echo $this->Buonny->placa($veic_veiculo['TVeicVeiculo']['veic_placa'], Date('d/m/Y 00:00:00'), Date('d/m/Y 23:59:59')) ;?>
        <strong>Estação: </strong><?= $veic_veiculo['TErasEstacaoRastreamento']['eras_descricao'] ?>
        <?php if($veic_veiculo['TUsuaUsuario']['usua_login']):?>
            <strong>Operador: </strong><?= $veic_veiculo['TUsuaUsuario']['usua_login']?>
            <strong>Ramal: </strong><?= $veic_veiculo['TUsuaUsuario']['usua_ramal']?>
            <?php $ramal = $veic_veiculo['TUsuaUsuario']['usua_ramal'];?>
        <?php else:?>
            <strong>Nenhum operador logado no momento.</strong>
        <?php endif;?>
    </div>

<?php echo $this->BForm->create('AtendimentoSac',array('url' => array('controller' => 'atendimentos_sacs','action' => 'salvar_registro_ligacao/'.$placa), 'type' => 'POST')) ?>
<div class='well'>
    <div class="form-control">

        <?php echo $this->BForm->hidden('codigo_sm', array('value' => $codigo_sm));?>
        <?php echo $this->BForm->hidden('placa', array('value' => $placa));?>
        <?php echo $this->BForm->hidden('codigo_cliente_transportador', array('value' => $transportador_cod)); ?>
        <?php echo $this->BForm->hidden('codigo_cliente_embarcador', array('value' => $embarcador_cod ));?>
        <?php echo $this->BForm->hidden('codigo_tecnologia', array('value' => $equipamento['Equipamento']['Codigo'] ));?>
        <?php echo $this->BForm->hidden('ramal_encaminhado', array('value' => $veic_veiculo['TUsuaUsuario']['usua_ramal'] ));?>
        <?php echo $this->BForm->input('codigo_motivo_atendimento', array('label' => 'Motivo da ligação', 'class' => 'input-large', 'options' => $motivos, 'empty' => 'Selecione um motivo')); ?>
        <?php echo $this->BForm->input('observacao', array('class' => 'input-xxlarge', 'maxlength' => 500, 'placeholder' => null, 'label' => 'Observação', 'type' => 'textarea')) ?>
        <?php //echo $this->BForm->input('ramal_encaminhado', array('class' => 'input-mini just-number', 'maxlength' => 10, 'placeholder' => false, 'label' => 'Ramal')) ?>
    </div>    
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary', 'id' => 'submit')) ?>
<?php echo $this->BForm->end();?>
</div>

<?php echo $this->Buonny->link_js('estatisticas') ?>