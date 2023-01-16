<?php
class RelatorioSmSituacaoFrota extends AppModel {

	var $name = 'RelatorioSmSituacaoFrota';
	var $useTable = false;
	
	public function dados($pess_oras_codigo_centralizador, $pess_oras_codigo, $condicoes_filtro) {
	    $tUposUltimaPosicao = ClassRegistry::init('TUposUltimaPosicao');
        $dados = $tUposUltimaPosicao->situacaoFrota($pess_oras_codigo_centralizador, $pess_oras_codigo, $condicoes_filtro);
        $dados = $this->agrupaDados($dados);
        return $dados;
	}
	
	private function agrupaDados($dados) {
	    $dadosAgrupados = array();
	    
	    foreach($dados as $dado) {
	        $index = $dado[0]['tvei_codigo'];
	        $dadosAgrupados[$index]['descricao'] = $dado[0]['tvei_descricao'];
	        $dadosAgrupados[$index]['referencias'][$dado[0]['refe_codigo']] = $dado[0]['refe_descricao'];
	        $dadosAgrupados[$index]['totais'][$dado[0]['alvo_codigo']]['cref_descricao'] = $dado[0]['alvo_descricao'];
	        $dadosAgrupados[$index]['totais'][$dado[0]['alvo_codigo']]['valores'][$dado[0]['refe_codigo']] = $dado[0]['total'];
	        $dadosAgrupados[$index]['total_linha']['cref_descricao'] = 'Total';
	        $dadosAgrupados[$index]['total_linha']['valores'][$dado[0]['refe_codigo']] = 
	            (isset($dadosAgrupados[$index]['total_linha']['valores'][$dado[0]['refe_codigo']])
	                ? $dadosAgrupados[$index]['total_linha']['valores'][$dado[0]['refe_codigo']]
	                : 0) + $dado[0]['total'];
	    }
	    
	    return $dadosAgrupados;
	}
	
	public function dadosAnalitico($pess_oras_codigo_centralizador, $pess_oras_codigo, $condicoes_filtro, $codigo_cliente) {
		$tUposUltimaPosicao = ClassRegistry::init('TUposUltimaPosicao');
		$dados = $tUposUltimaPosicao->situacaoFrotaAnalitico($pess_oras_codigo_centralizador, $pess_oras_codigo, $condicoes_filtro);
		$dados = $tUposUltimaPosicao->adicionaTransportadoraDbBuonny($codigo_cliente, $dados);
		return $dados;
	}
	
}