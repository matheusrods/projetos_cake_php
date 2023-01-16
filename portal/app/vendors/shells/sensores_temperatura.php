<?php
class SensoresTemperaturaShell extends Shell {

	function main() {
		echo "==================================================\n";
		echo "* Incluir \n";
		echo "* \n";
		echo "* \n";
		echo "==================================================\n\n";
		echo "=> insereSensorTemperatura: realiza a busca de totas a viagens finalizadas ou iniciadas maior que a data atual \n\n";
	}

	function run(){
		 echo 'INICIO: '.date("H:i:s") . "\n";
		if (!$this->im_running('sensores_temperatura')) {
			$this->TStemSensoresTemperatura = ClassRegistry::init('TStemSensoresTemperatura');
			$this->insereSensorTemperatura();
		} else {
			echo "Já em execução";
		}
	}

	private function im_running($tipo) {
		$cmd = shell_exec("ps aux | grep '{$tipo}'");
		// 1 execução é a execução atual
		return substr_count($cmd, 'cake.php -working') > 1;
	}


	function insereSensorTemperatura(){
		$this->TReceRecebimento = ClassRegistry::init('TReceRecebimento');
		$this->TTermTerminal 	= ClassRegistry::init('TTermTerminal');
		$this->TOrteObjetoRastreadoTermina 	= ClassRegistry::init('TOrteObjetoRastreadoTermina');
		$this->TVeicVeiculo 	= ClassRegistry::init('TVeicVeiculo');
		$this->TRperRecebimentoPeriferico 	= ClassRegistry::init('TRperRecebimentoPeriferico');
		$dia  = date('d', strtotime('-10 minute'));
		$query= "WITH sensores AS ( ";
		$query.="SELECT rece_codigo, rece_data_computador_bordo, veic_oras_codigo, veic_placa, term_codigo, term_numero_terminal, term_vtec_codigo,";
		$query.="CAST(replace(replace(r1.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor1_rper_valor, ";
		$query.="CAST(replace(replace(r2.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor2_rper_valor, ";
		$query.="CAST(replace(replace(r3.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor3_rper_valor, ";
		$query.="CAST(replace(replace(r4.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor4_rper_valor, ";
		$query.="CAST(replace(replace(r5.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor5_rper_valor, ";
		$query.="CAST(replace(replace(r6.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor6_rper_valor, ";
		$query.="CAST(replace(replace(r7.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor7_rper_valor, ";
		$query.="CAST(replace(replace(r8.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor8_rper_valor, ";
		$query.="CAST(replace(replace(r9.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor9_rper_valor, ";
		$query.="CAST(replace(replace(r10.rper_valor, '0-0', '0' ),',','.') AS numeric) as stem_sensor10_rper_valor "; 		
		$query.="FROM rece_recebimento_$dia ";
		$query.="INNER JOIN term_terminal ON term_numero_terminal = rece_term_numero_terminal AND term_vtec_codigo = rece_vtec_codigo ";
		$query.="INNER JOIN orte_objeto_rastreado_termina ON orte_term_codigo = term_codigo ";
		$query.="INNER JOIN veic_veiculo ON veic_oras_codigo = orte_oras_codigo ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r1 ON r1.rper_rece_codigo = rece_codigo AND r1.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r1.rper_vtec_codigo = rece_vtec_codigo AND r1.rper_data_computador_bordo = rece_data_computador_bordo  AND r1.rper_eppa_codigo = 11 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r2  ON r2.rper_rece_codigo = rece_codigo AND r2.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r2.rper_vtec_codigo = rece_vtec_codigo AND r2.rper_data_computador_bordo = rece_data_computador_bordo AND r2.rper_eppa_codigo = 33 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r3 ON r3.rper_rece_codigo = rece_codigo AND r3.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r3.rper_vtec_codigo = rece_vtec_codigo AND r3.rper_data_computador_bordo = rece_data_computador_bordo AND r3.rper_eppa_codigo = 34 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r4 ON r4.rper_rece_codigo = rece_codigo AND r4.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r4.rper_vtec_codigo = rece_vtec_codigo AND r4.rper_data_computador_bordo = rece_data_computador_bordo AND r4.rper_eppa_codigo = 35 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r5 ON r5.rper_rece_codigo = rece_codigo AND r5.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r5.rper_vtec_codigo = rece_vtec_codigo AND r5.rper_data_computador_bordo = rece_data_computador_bordo AND r5.rper_eppa_codigo = 36 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r6 ON r6.rper_rece_codigo = rece_codigo AND r6.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r6.rper_vtec_codigo = rece_vtec_codigo AND r6.rper_data_computador_bordo = rece_data_computador_bordo AND r6.rper_eppa_codigo = 37 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r7 ON r7.rper_rece_codigo = rece_codigo AND r7.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r7.rper_vtec_codigo = rece_vtec_codigo AND r7.rper_data_computador_bordo = rece_data_computador_bordo AND r7.rper_eppa_codigo = 38 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r8 ON r8.rper_rece_codigo = rece_codigo AND r8.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r8.rper_vtec_codigo = rece_vtec_codigo AND r8.rper_data_computador_bordo = rece_data_computador_bordo AND r8.rper_eppa_codigo = 39 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r9 ON r9.rper_rece_codigo = rece_codigo AND r9.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r9.rper_vtec_codigo = rece_vtec_codigo AND r9.rper_data_computador_bordo = rece_data_computador_bordo AND r9.rper_eppa_codigo = 40 ";
		$query.="LEFT JOIN rper_recebimento_periferico_$dia r10 ON r10.rper_rece_codigo = rece_codigo AND r10.rper_term_numero_terminal = rece_term_numero_terminal ";
		$query.="AND r10.rper_vtec_codigo = rece_vtec_codigo AND r10.rper_data_computador_bordo = rece_data_computador_bordo AND r10.rper_eppa_codigo = 41 ";
		$query.="WHERE rece_data_computador_bordo BETWEEN NOW() - interval '4 minutes' AND NOW() ";
		$query.="AND (r1.rper_valor IS NOT NULL OR r2.rper_valor IS NOT NULL OR r3.rper_valor IS NOT NULL OR r4.rper_valor IS NOT NULL OR r5.rper_valor IS NOT NULL ";
		$query.="OR r6.rper_valor IS NOT NULL OR r7.rper_valor IS NOT NULL OR r8.rper_valor IS NOT NULL OR r9.rper_valor IS NOT NULL OR r10.rper_valor IS NOT NULL ) ";
		$query.="AND NOT EXISTS (SELECT stem_rece_codigo FROM trafegus.public.stem_sensores_temperatura WHERE stem_rece_codigo = rece_codigo ) ";
		$query.="ORDER BY rece_data_computador_bordo ) ";
		$query.="SELECT rece_codigo, rece_data_computador_bordo, veic_oras_codigo, veic_placa, term_codigo, ";
		$query.="term_numero_terminal, term_vtec_codigo, stem_sensor1_rper_valor, stem_sensor2_rper_valor, ";
		$query.="stem_sensor3_rper_valor, stem_sensor4_rper_valor, stem_sensor5_rper_valor, stem_sensor6_rper_valor, ";
		$query.="stem_sensor7_rper_valor, stem_sensor8_rper_valor,stem_sensor9_rper_valor,stem_sensor10_rper_valor, ";
		$query.="round( ";
		$query.="(CASE WHEN stem_sensor1_rper_valor IS NOT NULL THEN stem_sensor1_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor2_rper_valor IS NOT NULL THEN stem_sensor2_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor3_rper_valor IS NOT NULL THEN stem_sensor3_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor4_rper_valor IS NOT NULL THEN stem_sensor4_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor5_rper_valor IS NOT NULL THEN stem_sensor5_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor6_rper_valor IS NOT NULL THEN stem_sensor6_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor7_rper_valor IS NOT NULL THEN stem_sensor7_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor8_rper_valor IS NOT NULL THEN stem_sensor8_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor9_rper_valor IS NOT NULL THEN stem_sensor9_rper_valor ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor10_rper_valor IS NOT NULL THEN stem_sensor10_rper_valor ELSE 0 END) / ";
		$query.="(CASE WHEN stem_sensor1_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor2_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor3_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor4_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor5_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor6_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor7_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor8_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor9_rper_valor IS NOT NULL THEN 1 ELSE 0 END + ";
		$query.="CASE WHEN stem_sensor10_rper_valor IS NOT NULL THEN 1 ELSE 0 END) ";
		$query.=", 2) AS media_temperatura ";
		$query.="FROM sensores ";
		$query.="ORDER BY rece_data_computador_bordo ";
        $query_insert = "INSERT INTO {$this->TStemSensoresTemperatura->databaseTable}.{$this->TStemSensoresTemperatura->tableSchema}.{$this->TStemSensoresTemperatura->useTable}(stem_rece_codigo, stem_data_cadastro, stem_veic_oras_codigo, stem_veic_placa, stem_term_codigo, stem_term_numero_terminal, stem_vtec_codigo, stem_sensor1_rper_valor, stem_sensor2_rper_valor, stem_sensor3_rper_valor, stem_sensor4_rper_valor, stem_sensor5_rper_valor, stem_sensor6_rper_valor, stem_sensor7_rper_valor, stem_sensor8_rper_valor, stem_sensor9_rper_valor, stem_sensor10_rper_valor, stem_media_sensores ) $query";
        $retorno = $this->TStemSensoresTemperatura->query($query_insert);        
	}	
}
?>