<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Data</th>
            <th>Hora</th>
            <th>Login</th>
            <th>Tipo Usuário</th>
            <th>Departamento</th>
            <th>Perfil</th>
            <th>Cliente</th>
            <th>IP</th>
            <th>Tipo Consulta</th>
            <th>Reg. Consulta</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?= $log[0]['dt_inclusao'] ?></td>
            <td><?= $log[0]['hora_inclusao'] ?></td>
            <td><?= $log['LogConsulta']['login'] ?></td>
            <td><?= (empty($log['Usuario']['codigo_cliente']) ? 'Interno' : 'Externo')?></td>
            <td><?= $log['Departamento']['descricao']?></td>
            <td><?= $log['Uperfil']['descricao']?></td>
            <td><?= $log['Cliente']['razao_social']?></td>
            <td><?= $log['LogConsulta']['ip'] ?></td>
            <td><?= (isset($tipos_consulta[$log['LogConsulta']['codigo_tipo_consulta']]) ? $tipos_consulta[$log['LogConsulta']['codigo_tipo_consulta']] : '') ?></td>
            <td>
                <?php if ($log['LogConsulta']['codigo_tipo_consulta']==LogConsultaTipo::TIPO_CONSULTA_VEICULOS): ?>
                    <?php
                        $placa = str_replace("-","",$log['LogConsulta']['foreign_key']);
                        $placa = Comum::formatarPlaca($placa);
                        $data_inclusao_inicial = $filtros['data_inclusao_inicial'];
                        $data_inclusao_final = $filtros['data_inclusao_final'];
                    ?>
                    <?php echo $this->Buonny->placa( $placa, $data_inclusao_inicial, $data_inclusao_final); ?>

                <?php elseif ($log['LogConsulta']['codigo_tipo_consulta']==LogConsultaTipo::TIPO_CONSULTA_SM): ?>
                    <?php echo $this->Buonny->codigo_sm($log['LogConsulta']['foreign_key']); ?>
                <?php else: ?>
                    <?php echo $log['LogConsulta']['foreign_key'] ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
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
<?php echo $this->Js->writeBuffer(); ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>

