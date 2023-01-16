<?php
class CronologiaSm extends AppModel {

	public $name = 'CronologiaSm';
	public $useTable = false;

	public function getCronologiaSm($codigo_sm, $count = false) {
		$this->AtendimentoSm 				= ClassRegistry::init('AtendimentoSm');
		$this->PassoAtendimentoSm 			= ClassRegistry::init('PassoAtendimentoSm');
		$this->HistoricoSm 					= ClassRegistry::init('HistoricoSm');
		$this->MComunicadoSinistro 			= ClassRegistry::init('MComunicadoSinistro');
		$this->Sinistro 					= ClassRegistry::init('Sinistro');
		$this->TEsisEventoSistema 			= ClassRegistry::init('TEsisEventoSistema');
		$this->TEspaEventoSistemaPadrao		= ClassRegistry::init('TEspaEventoSistemaPadrao');
		$this->TViagViagem					= ClassRegistry::init('TViagViagem');
		$this->TRmacRecebimentoMacro		= ClassRegistry::init('TRmacRecebimentoMacro');
		$this->TTermTerminal				= ClassRegistry::init('TTermTerminal');
		$this->TVterViagemTerminal			= ClassRegistry::init('TVterViagemTerminal');
		$this->TRmliRecebimentoMensagLivre	= ClassRegistry::init('TRmliRecebimentoMensagLivre');
		$this->TRperRecebimentoPeriferico	= ClassRegistry::init('TRperRecebimentoPeriferico');
		$this->TRposRecebimentoPosicao		= ClassRegistry::init('TRposRecebimentoPosicao');
		$this->TEnviEnvio					= ClassRegistry::init('TEnviEnvio');
		$this->TEcomEnvioComando			= ClassRegistry::init('TEcomEnvioComando');
		$this->TCpadComandoPadrao			= ClassRegistry::init('TCpadComandoPadrao');
		$this->TRefeReferencia				= ClassRegistry::init('TRefeReferencia');
		$this->TUsuaUsuario					= ClassRegistry::init('TUsuaUsuario');
		$this->TVlocViagemLocal				= ClassRegistry::init('TVlocViagemLocal');
		$this->TEppaEventoPerifericoPadrao	= ClassRegistry::init('TEppaEventoPerifericoPadrao');
		$this->TPitePgItem					= ClassRegistry::init('TPitePgItem');
		$this->TPpadPerifericoPadrao		= ClassRegistry::init('TPpadPerifericoPadrao');
		$this->Usuario						= ClassRegistry::init('Usuario');
		$this->TEmliEnvioMensagemLivre		= ClassRegistry::init('TEmliEnvioMensagemLivre');
		$dbo = $this->AtendimentoSm->getDatasource();
		$dboPg = $this->TEsisEventoSistema->getDatasource();

		$now =  $this->AtendimentoSm->useDbConfig != 'test_suite' ? 'now()' : 'getDate()';


		/*if($this->AtendimentoSm->useDbConfig != 'test_suite'){
			$campo_tempo_evento = "EXTRACT(DAY FROM COALESCE(esis_data_leitura,now()) - esis_data_cadastro)* 24 * 60 + 
                EXTRACT(HOUR FROM COALESCE(esis_data_leitura,now()) - esis_data_cadastro) * 60 + 
                EXTRACT(MINUTE FROM COALESCE(esis_data_leitura,now()) - esis_data_cadastro)
            ";
			$campo_tempo_macro = "EXTRACT(DAY FROM COALESCE(rmac_data_leitura,now()) - rmac_data_computador_bordo)* 24 * 60 + 
                EXTRACT(HOUR FROM COALESCE(rmac_data_leitura,now()) - rmac_data_computador_bordo) * 60 + 
                EXTRACT(MINUTE FROM COALESCE(rmac_data_leitura,now()) - rmac_data_computador_bordo)
            ";
			$campo_tempo_msg = "EXTRACT(DAY FROM COALESCE(rmli_data_leitura,now()) - rmli_data_computador_bordo)* 24 * 60 + 
                EXTRACT(HOUR FROM COALESCE(rmli_data_leitura,now()) - rmli_data_computador_bordo) * 60 + 
                EXTRACT(MINUTE FROM COALESCE(rmli_data_leitura,now()) - rmli_data_computador_bordo)
            ";
		} else {
			$campo_tempo_evento = "DATEDIFF(minute,esis_data_cadastro,COALESCE(esis_data_leitura,now()))";
			$campo_tempo_macro = "DATEDIFF(minute,rmac_data_computador_bordo,COALESCE(rmac_data_leitura,now()))";
			$campo_tempo_msg = "DATEDIFF(minute,rmac_data_computador_bordo,COALESCE(rmac_data_leitura,now()))";
		}*/

		$query_guardian_cte = "select data_inclusao, tipo, texto, COALESCE(refe_latitude,rpos_latitude) as latitude, COALESCE(refe_longitude,rpos_longitude) as longitude, operador, operador_tratou, data_leitura from (
		select esis_data_cadastro as data_inclusao, 'evento sistema' as tipo, espa_descricao as texto,
		    CASE 
                WHEN esis_espa_codigo in (47,113,139,140,5012,5023,5024,5025,5026,5027) then cast(esis_valor as bigint)
                else null
            end as rece_codigo, 
		    CASE
		        WHEN esis_espa_codigo in (71, 69, 5007, 5015, 5016) THEN vloc_refe_codigo
		        else null
		    end as refe_codigo,
        	TUsuaUsuario.usua_login as operador,
        	TUsuaUsuarioLeitura.usua_login as operador_tratou, esis_data_leitura as data_leitura
		from {$this->TEsisEventoSistema->databaseTable}.{$this->TEsisEventoSistema->tableSchema}.{$this->TEsisEventoSistema->useTable} TEsisEventoSistema
			inner join {$this->TEspaEventoSistemaPadrao->databaseTable}.{$this->TEspaEventoSistemaPadrao->tableSchema}.{$this->TEspaEventoSistemaPadrao->useTable} TEspaEventoSistemaPadrao on espa_codigo = esis_espa_codigo
			inner join {$this->TPitePgItem->databaseTable}.{$this->TPitePgItem->tableSchema}.{$this->TPitePgItem->useTable} TPitePgItem on pite_espa_codigo = espa_codigo and pite_ppad_codigo is null
			left join {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} TViagViagem on esis_viag_codigo = viag_codigo
			left join {$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable} TVlocViagemLocal on vloc_viag_codigo = viag_codigo and vloc_codigo = cast(esis_valor as bigint) 
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuario on esis_usu_pfis_responsavel = TUsuaUsuario.usua_pfis_pess_oras_codigo
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuarioLeitura on esis_usu_codigo_leitura = TUsuaUsuarioLeitura.usua_pfis_pess_oras_codigo
		where viag_codigo_sm = ".$codigo_sm."
		union
		select esis_data_cadastro as data_inclusao, 'evento recebido' as tipo, espa_descricao as texto,
		    CASE 
                WHEN esis_espa_codigo in (47,113,139,140,5012,5023,5024,5025,5026,5027) then cast(esis_valor as bigint)
                else null
            end as rece_codigo, 
		    CASE
		        WHEN esis_espa_codigo in (71, 69, 5007, 5015, 5016) THEN vloc_refe_codigo
		        else null
		    end as refe_codigo,
        	TUsuaUsuario.usua_login as operador,
        	TUsuaUsuarioLeitura.usua_login as operador_tratou, esis_data_leitura as data_leitura
		from {$this->TEsisEventoSistema->databaseTable}.{$this->TEsisEventoSistema->tableSchema}.{$this->TEsisEventoSistema->useTable} TEsisEventoSistema
			inner join {$this->TEspaEventoSistemaPadrao->databaseTable}.{$this->TEspaEventoSistemaPadrao->tableSchema}.{$this->TEspaEventoSistemaPadrao->useTable} TEspaEventoSistemaPadrao on espa_codigo = esis_espa_codigo
			inner join {$this->TPitePgItem->databaseTable}.{$this->TPitePgItem->tableSchema}.{$this->TPitePgItem->useTable} TPitePgItem on pite_espa_codigo = espa_codigo and pite_ppad_codigo is not null
			inner join {$this->TPpadPerifericoPadrao->databaseTable}.{$this->TPpadPerifericoPadrao->tableSchema}.{$this->TPpadPerifericoPadrao->useTable} TPpadPerifericoPadrao on ppad_codigo = pite_ppad_codigo
			left join {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} TViagViagem on esis_viag_codigo = viag_codigo
			left join {$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable} TVlocViagemLocal on vloc_viag_codigo = viag_codigo and vloc_codigo = cast(esis_valor as bigint) 
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuario on esis_usu_pfis_responsavel = TUsuaUsuario.usua_pfis_pess_oras_codigo
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuarioLeitura on esis_usu_codigo_leitura = TUsuaUsuarioLeitura.usua_pfis_pess_oras_codigo
		where viag_codigo_sm = ".$codigo_sm."
		union
		select rmac_data_cadastro as data_inclusao, 'macro recebida' as tipo, rmac_texto as texto, rmac_rece_codigo as rece_codigo, null as refe_codigo, null as operador, usua_login as operador_tratou, rmac_data_leitura as data_leitura
		from {$this->TRmacRecebimentoMacro->databaseTable}.{$this->TRmacRecebimentoMacro->tableSchema}.{$this->TRmacRecebimentoMacro->useTable} TRmacRecebimentoMacro 
			inner join {$this->TTermTerminal->databaseTable}.{$this->TTermTerminal->tableSchema}.{$this->TTermTerminal->useTable} TTermTerminal on term_numero_terminal = rmac_term_numero_terminal and term_vtec_codigo = rmac_vtec_codigo
			inner join {$this->TVterViagemTerminal->databaseTable}.{$this->TVterViagemTerminal->tableSchema}.{$this->TVterViagemTerminal->useTable} TVterViagemTerminal on vter_term_codigo = term_codigo and vter_precedencia='1' and vter_ativo='S'
			inner join {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} TViagViagem on viag_codigo = vter_viag_codigo
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuario on rmac_usua_pfis_pess_oras_codigo = usua_pfis_pess_oras_codigo
		where viag_codigo_sm = ".$codigo_sm." and rmac_data_computador_bordo between viag_data_inicio and COALESCE(viag_data_fim, ".$now.")
		union
		select rmli_data_cadastro as data_inclusao, 'msg recebida' as tipo, rmli_texto as texto, rmli_rece_codigo as rece_codigo, null as refe_codigo, null as operador, usua_login as operador_tratou, rmli_data_leitura as data_leitura
		from {$this->TRmliRecebimentoMensagLivre->databaseTable}.{$this->TRmliRecebimentoMensagLivre->tableSchema}.{$this->TRmliRecebimentoMensagLivre->useTable} TRmliRecebimentoMensagLivre 
			inner join {$this->TTermTerminal->databaseTable}.{$this->TTermTerminal->tableSchema}.{$this->TTermTerminal->useTable} TTermTerminal on term_numero_terminal = rmli_term_numero_terminal and term_vtec_codigo = rmli_vtec_codigo
			inner join {$this->TVterViagemTerminal->databaseTable}.{$this->TVterViagemTerminal->tableSchema}.{$this->TVterViagemTerminal->useTable} TVterViagemTerminal on vter_term_codigo = term_codigo and vter_precedencia='1' and vter_ativo='S'
			inner join {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} TViagViagem on viag_codigo = vter_viag_codigo
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuario on rmli_usua_pfis_pess_oras_codigo = usua_pfis_pess_oras_codigo
		where viag_codigo_sm = ".$codigo_sm." and rmli_data_computador_bordo between viag_data_inicio and COALESCE(viag_data_fim,".$now.")
		union
		select envi_data_enviado as data_inclusao, 'envio comando' as tipo, cpad_descricao as texto, null as rece_codigo, null as refe_codigo, COALESCE(usua_login,'Automático') as operador, null as operador_tratou, envi_data_leitura as data_leitura
		from {$this->TEnviEnvio->databaseTable}.{$this->TEnviEnvio->tableSchema}.{$this->TEnviEnvio->useTable} TEnviEnvio 
			inner join {$this->TEcomEnvioComando->databaseTable}.{$this->TEcomEnvioComando->tableSchema}.{$this->TEcomEnvioComando->useTable} TEcomEnvioComando on ecom_envi_codigo = envi_codigo
			inner join {$this->TCpadComandoPadrao->databaseTable}.{$this->TCpadComandoPadrao->tableSchema}.{$this->TCpadComandoPadrao->useTable} TCpadComandoPadrao on cpad_codigo = ecom_cpad_codigo
			inner join {$this->TVterViagemTerminal->databaseTable}.{$this->TVterViagemTerminal->tableSchema}.{$this->TVterViagemTerminal->useTable} TVterViagemTerminal on vter_term_codigo = envi_term_codigo and vter_precedencia='1' and vter_ativo='S'
			inner join {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} TViagViagem on viag_codigo = vter_viag_codigo
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuario on envi_usua_pfis_pess_oras_codigo = usua_pfis_pess_oras_codigo
		where viag_codigo_sm = ".$codigo_sm." and envi_data_enviado between viag_data_inicio and COALESCE(viag_data_fim,".$now.")
		union
		select envi_data_enviado as data_inclusao, 'envio comando' as tipo, cast(emli_texto as varchar(500)) as texto, null as rece_codigo, null as refe_codigo, COALESCE(usua_login,'Automático') as operador, null as operador_tratou, envi_data_leitura as data_leitura
		from {$this->TEnviEnvio->databaseTable}.{$this->TEnviEnvio->tableSchema}.{$this->TEnviEnvio->useTable} TEnviEnvio 
			inner join {$this->TEmliEnvioMensagemLivre->databaseTable}.{$this->TEmliEnvioMensagemLivre->tableSchema}.{$this->TEmliEnvioMensagemLivre->useTable} TEmliEnvioMensagemLivre on emli_envi_codigo = envi_codigo			
			inner join {$this->TVterViagemTerminal->databaseTable}.{$this->TVterViagemTerminal->tableSchema}.{$this->TVterViagemTerminal->useTable} TVterViagemTerminal on vter_term_codigo = envi_term_codigo and vter_precedencia='1' and vter_ativo='S'
			inner join {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} TViagViagem on viag_codigo = vter_viag_codigo
			left join {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable} TUsuaUsuario on envi_usua_pfis_pess_oras_codigo = usua_pfis_pess_oras_codigo
		where viag_codigo_sm = ".$codigo_sm." and envi_data_enviado between viag_data_inicio and COALESCE(viag_data_fim,".$now.")		
		) as e
        left join rpos_recebimento_posicao on rpos_rece_codigo = rece_codigo
        left join (
			select refe_codigo, refe_latitude, refe_longitude
			from {$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}
					INNER JOIN {$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable} ON vloc_refe_codigo = refe_codigo
					INNER JOIN {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} ON vloc_viag_codigo = viag_codigo and viag_codigo_sm = ".$codigo_sm."
			) as refe on refe.refe_codigo = e.refe_codigo
		";

		if($this->AtendimentoSm->useDbConfig != 'test_suite'){
			$query_guardian_cte = " 
				select data_inclusao, tipo, texto collate SQL_Latin1_General_CP1_CI_AS, latitude, longitude, operador, operador_tratou, data_leitura from
				        openquery(LK_GUARDIAN,'".str_replace('"',"",str_replace("'", "''",$query_guardian_cte ))."') as eventos_guardian
			";

			$query_refe = "
				select * from openquery(LK_GUARDIAN,'
					select refe_codigo, refe_descricao, refe_latitude_min, refe_latitude_max, refe_longitude_min, refe_longitude_max, refe_latitude, refe_longitude from {$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}
						INNER JOIN {$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable} ON vloc_refe_codigo = refe_codigo
						INNER JOIN {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} ON vloc_viag_codigo = viag_codigo and viag_codigo_sm = ".$codigo_sm."
				') as TRefeReferencia
			";
			$query_usua = "
				select * from openquery(LK_GUARDIAN,'
					select usua_pfis_pess_oras_codigo, usua_login from {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable}
				') as TUsuaUsuario
			";
		} else {
			$query_guardian_cte = " select data_inclusao, tipo, texto, latitude, longitude, operador, operador_tratou, data_leitura from (".$query_guardian_cte.") as eventos_guardian";
			$query_refe = "select * from (
				select refe_codigo, refe_descricao, refe_latitude_min, refe_latitude_max, refe_longitude_min, refe_longitude_max, refe_latitude, refe_longitude from {$this->TRefeReferencia->databaseTable}.{$this->TRefeReferencia->tableSchema}.{$this->TRefeReferencia->useTable}
					INNER JOIN {$this->TVlocViagemLocal->databaseTable}.{$this->TVlocViagemLocal->tableSchema}.{$this->TVlocViagemLocal->useTable} ON vloc_refe_codigo = refe_codigo
					INNER JOIN {$this->TViagViagem->databaseTable}.{$this->TViagViagem->tableSchema}.{$this->TViagViagem->useTable} ON vloc_viag_codigo = viag_codigo and viag_codigo_sm = ".$codigo_sm."
			) as TRefeReferencia
			";
			$query_usua = "select * from (
				select usua_pfis_pess_oras_codigo, usua_login from {$this->TUsuaUsuario->databaseTable}.{$this->TUsuaUsuario->tableSchema}.{$this->TUsuaUsuario->useTable}
			) as TUsuaUsuario";
		}

		$query_atendimento_sms = "
		select historicos_sms.data_inclusao as data_inclusao, 'registro ocorrencia' as tipo, convert(varchar(8000), historicos_sms.texto) as texto,historicos_sms.latitude, historicos_sms.longitude, COALESCE(usuario.apelido COLLATE SQL_Latin1_General_CP1_CI_AS, usua_login) as operador, null as operador_tratou, historicos_sms.data_inclusao as data_leitura
		from {$this->AtendimentoSm->databaseTable}.{$this->AtendimentoSm->tableSchema}.{$this->AtendimentoSm->useTable} atendimentos_sms
			left join {$this->PassoAtendimentoSm->databaseTable}.{$this->PassoAtendimentoSm->tableSchema}.{$this->PassoAtendimentoSm->useTable} passos_atendimentos_sms on passos_atendimentos_sms.codigo_atendimento_sm = atendimentos_sms.codigo
			left join {$this->HistoricoSm->databaseTable}.{$this->HistoricoSm->tableSchema}.{$this->HistoricoSm->useTable} historicos_sms on historicos_sms.codigo_passo_atendimento_sm = passos_atendimentos_sms.codigo
			left join {$this->Usuario->databaseTable}.{$this->Usuario->tableSchema}.{$this->Usuario->useTable} usuario on historicos_sms.codigo_usuario = usuario.codigo
			left join ($query_usua) as TUsuaUsuario on historicos_sms.codigo_usuario_inclusao_guardian = TUsuaUsuario.usua_pfis_pess_oras_codigo
		where atendimentos_sms.codigo_sm = ".$codigo_sm;

		$query_comunicado_sinistro = "
		select comunicado_sinistro.data_inclusao as data_inclusao, 'comunicado evento' as tipo, convert(varchar(8000), comunicado_sinistro.descricao_ocorrencia) as texto, null as latitude, null as longitude, null as operador, null as operador_tratou, null as data_leitura
		from {$this->MComunicadoSinistro->databaseTable}.{$this->MComunicadoSinistro->tableSchema}.{$this->MComunicadoSinistro->useTable} comunicado_sinistro
		where comunicado_sinistro.sm = ".$codigo_sm;

		$query_sinistro = "
		select sinistro.data_evento as data_inclusao, 'sinistro' as tipo, convert(varchar(8000), sinistro.observacao) as texto, sinistro.latitude, sinistro.longitude, null as operador, null as operador_tratou, null as data_leitura
		from {$this->Sinistro->databaseTable}.{$this->Sinistro->tableSchema}.{$this->Sinistro->useTable} sinistro
		where sinistro.sm = ".$codigo_sm;

		$query_cronologia = $query_atendimento_sms." UNION ".
							$query_comunicado_sinistro." UNION ".
							$query_sinistro." UNION ".
							$query_guardian_cte;

		
		$joins = array(
			array(
				'table' => "({$query_refe})",
				'alias' => 'refe',
				'conditions' => array(
					'COALESCE(CronologiaSm.latitude,-9999) between refe.refe_latitude_min and refe.refe_latitude_max',
					'COALESCE(CronologiaSm.longitude,-9999) between refe.refe_longitude_min and refe.refe_longitude_max',
				),
				'type' => 'LEFT'
			),
		);

		if ($count) {
			$fields = Array('count(0) AS "count"');
		} else {
			$fields = Array(
				'CONVERT(varchar(20),data_inclusao,120) AS data_inclusao',
				'tipo',
				'texto',
				'refe_descricao',
				'refe_latitude',
				'refe_longitude',
				'operador',
				'operador_tratou',
				'CONVERT(varchar(20),data_leitura,120) AS data_leitura'
			);
		}
		
		$params_build = Array(
			'fields' => $fields,
			'table' => "($query_cronologia)",
			'alias' => 'CronologiaSm',
			'joins' => $joins,
			'conditions' => null,
			
		);

		if (!$count) {
			$params_build['order'] = Array('data_inclusao');
		}

		$query = $dbo->buildStatement(
			$params_build, $this
		);

		/* Utilizado para otimização da query, visto que, caso não passado o SQLServer tenta utilizar
			o aggregate a partir da tabela de profissionais, antes de realizar qualquer tipo de filtro,
			gerando lentidão na consulta.
			Tal processo apenas é útil na consulta do COUNT, já que, para uma consulta de dados, o aggregate
			não é utilizado.
		*/
		if ($count) {
			$query .= " OPTION (FORCE ORDER) ";
		}

		$this->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		//debug($query);
		$retorno = $this->query($query);
		return $retorno;
	}

}
?>