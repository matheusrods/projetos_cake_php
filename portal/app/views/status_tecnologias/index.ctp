<table class='table table-striped'>

    <thead>
        <tr>
            <th>Tecnologia</th>
            <th class='input-small numeric'>Em Viagem</th>
            <th class='input-mini numeric'>Veículos</th>
            <th class='input-small'>Última</th>
            <th class='input-mini numeric'>Qtd Min</th>
            <th class='input-mini numeric'>%Min</th>
            <th class='input-mini'>Status</th>
            <th>Tecnologia</th>
            <th class='input-small numeric'>Em Viagem</th>
            <th class='input-mini numeric'>Veículos</th>
            <th class='input-small'>Última</th>
            <th class='input-mini numeric'>Qtd Min</th>
            <th class='input-mini numeric'>%Min</th>
            <th class='input-mini'>Status</th>
        </tr>
    </thead>
    <tbody>
    	<?php foreach ($contas as $key => $conta) : ?>
            <?php if (empty($conta['0']['qtd_total_em_viagem'])): ?>
                <?php $percentual = 0; ?>
            <?php else:?>
                <?php $percentual = round($conta['0']['qtd_atualizado_em_viagem'] / $conta['0']['qtd_total_em_viagem'] * 100,1) ?>
            <?php endif ?>
    		<?php if ($key % 2 == 0): ?>
    			<tr>
    		<?php endif ?>
    		<td><?= $conta['0']['ctec_descricao'] ?></td>
    		<td class='input-mini numeric'><?= $this->Buonny->moeda($conta['0']['qtd_total_em_viagem'], array('places' => 0)) ?></td>
            <td class='input-mini numeric'><?= $this->Buonny->moeda($conta['0']['qtd_total'], array('places' => 0)) ?></td>
            <?php $class = 'badge badge-empty '.($conta['0']['atualizado_data'] ? 'badge-success' : 'badge-important') ?>
            <td class='input-small'><span class="<?php echo $class ?>"><?= AppModel::dbDateToDate($conta['0']['ultima_atualizacao']) ?></span></td>
            <td class='input-mini numeric'><?= AppModel::dbDateToDate($conta['0']['ctec_minimo_monitoramento']) ?></td>
            <?php $class = 'badge badge-empty '.($conta['0']['ctec_percentual_posicionando'] >= 50 ? 'badge-success' : ($conta['0']['ctec_percentual_posicionando'] >= 25 ? 'badge-warning' : 'badge-important')) ?>
            <td class='input-mini numeric'><span class="<?php echo $class ?>"><?= $conta['0']['ctec_percentual_posicionando'] ?></span></td>
    		<?php $class = 'badge badge-empty '.($conta['0']['atualizado'] ? 'badge-success' : 'badge-important') ?>
    		<td><span class="<?php echo $class ?>"><?= $percentual ?></span></td>
    		<?php if ($key % 2 == 1): ?>
    			</tr>
    		<?php endif ?>
    	<?php endforeach ?>
    	<?php if ($key % 2 == 0): ?>
    		<td></td>
    		<td></td>
    		<td></td>
    		</tr>
    	<?php endif ?>
    </tbody>
</table>
<?php
   	$i 		=0;
    $n 		=0;
    $ip 	=1;
    $ii 	=1;
	$par 	= array();
	$impar 	= array();

	foreach ($macroViagem as $values) {

    	if(($n%2)==0){

    		$par_macro[$ip] = $n;
    		$ip++;
    	} else {

	    	$impar_macro[$ii] = $n;
	    	$ii++;
	    }
	  $i++;
	  $n++;
   	}
?>

<h4>Heart Beat</h4>
<br />
<table class='table guardian'>
    <thead>
        <tr>
            <th class="input-xlarge">Descrição</th>
            <th class = 'atualiza' >Ultima Atualização</th>
            <th class='input-medium numeric'>Status</th>
            <th class="input-xlarge">Descrição</th>
            <th class='atualiza'>Ultima Atualizações</th>
            <th class='input-mini numeric'>Status</th>        
        </tr>
    </thead>
    <tbody>
        <?php foreach ($macroViagem as $key => $macro) : ?>           
            <?php if ($key % 2 == 0): ?>
                <tr>
            <?php endif ?>
            <td><?= $macro['THbeaHeartBeat']['hbea_descricao'] ?></td>
            <td><?php echo empty($macro['THbeaHeartBeat']['hbea_last_run']) ? 'Não conectado' :
            date('d/m/Y H:i:s',strtotime(str_replace('/','-',$macro['THbeaHeartBeat']['hbea_last_run']))); ?></td>
            <td class='input-mini numeric'> <span class="<?php echo $macro['THbeaHeartBeat']['status'] ?>"></span></td>

            <?php if ($key % 2 == 1): ?>
                </tr>
            <?php endif ?>
        <?php endforeach ?>
        <?php if ($key % 2 == 0): ?>
            <td></td>
            <td></td>
            <td></td>
            </tr>
        <?php endif ?>
    </tbody>
</table>
<?php
   echo $this->Javascript->codeBlock('setInterval(function(){ location.reload();}, 60000);', false);
?>
