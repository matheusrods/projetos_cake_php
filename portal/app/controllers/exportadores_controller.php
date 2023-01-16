<?php class ExportadoresController extends AppController {

    public $name = 'Exportadores';
    public $uses = array();
    
	public function index() {
        if ($this->RequestHandler->isPost()) {
            if (!empty($this->data['Exportador']['quantidade']) && $this->data['Exportador']['quantidade']<50000) {
                ini_set('max_execution_time', 0);
                set_time_limit(0);
                if ($this->data['Exportador']['tipo_exportacao'] == 1) {
                    $this->Exportador =& ClassRegistry::init('Ace1');
                } else {
                    //$this->Exportador =& ClassRegistry::init('Aig');
                    $this->Exportador =& ClassRegistry::init('Ace1');
                }
                $this->gerarArquivo();
            } else {
                $this->set('error_message', 'Informe a quantidade de registros');
            }
        }
        $arquivos = glob(APP.'webroot'.DS.'files'.DS.'export'.DS.'*.csv');
        $arquivos = array_merge($arquivos, glob(APP.'webroot'.DS.'files'.DS.'export'.DS.'*.txt'));
        $this->set(compact('arquivos'));
    }

    private function gerarArquivo() {
        $dbo = $this->Exportador->getDataSource();
        try {
            $this->Exportador->carregar();
            $this->Exportador->query('begin transaction');
            $data_atualizacao = date('Y-m-d H:i:s');
            if (!$this->Exportador->separarRegistros($this->data['Exportador']['quantidade'], $data_atualizacao)) throw new Exception();
            $query = $this->Exportador->queryListar($data_atualizacao);
            $dbo->results = $dbo->_execute($query);
			$filename = ($this->data['Exportador']['tipo_exportacao'] == 1 ? 'ace' : 'aig');
            $filename = APP.'webroot'.DS.'files'.DS.'export'.DS.strtolower($filename).date('YmdHis');
            $filename .= '.csv_gerando';
            $qtd_registros = 0;
            if ($this->data['Exportador']['tipo_exportacao'] == 2) {
                $line = "Nome;DataDeNascimento;Genero;CPF;RG;EstadoCivil;Ocupacao;CartaoNumero;CartaoNomeImpresso;CartaoExpiracao;ConjugeNome;ConjugeDataNascimento;ConjugeCPF;EnderecoLogradouro;EnderecoNumero;EnderecoComplemento;EnderecoBairro;EnderecoCidade;EnderecoUF;EnderecoCEP;FonePrincipalDDD;FonePrincipalNumero;FonePrincipalRamal;FoneSecundarioDDD;FoneSecundarioNumero;FoneSecundarioRamal;FoneCelularDDD;FoneCelularNumero;FoneCelularSecundarioDDD;FoneCelularSecundarioNumero;Email;EmailSecundario;Detalhe1;Detalhe2;Detalhe3;Detalhe4;Detalhe5;Detalhe6;Detalhe7;Filler";
                file_put_contents($filename, $line, FILE_APPEND);
            }
            while ($registro = $dbo->fetchRow()) {
                $qtd_registros ++;
                $line = $this->retornaLinha($registro, $this->data['Exportador']['tipo_exportacao']);
                file_put_contents($filename, $line, FILE_APPEND);
            }
            if ($qtd_registros > 0) {
                rename($filename, substr($filename,0,strlen($filename)-8));
            } else {
                $this->BSession->setFlash('no_data');
            }
            $this->Exportador->commit();
        } catch (Exception $ex) {
            $this->Exportador->rollback();
        }
    }
	
    private function retornaLinha($registro, $tipo_exportacao) {
        if ($tipo_exportacao == 1) {
            $line = $this->quote($registro[0]['nome']).';';
            $line .= $registro[0]['codigo_documento'].';';
            $line .= AppModel::dbDateToDate($registro[0]['data_nascimento']).';';
            $line .= $this->quote($registro[0]['endereco_tipo'].' '.$registro[0]['endereco_logradouro']).';';
            $line .= $registro[0]['numero'].';';
            $line .= $this->quote($registro[0]['endereco_bairro']).';';
            $line .= $this->quote($registro[0]['endereco_cidade']).';';
            $line .= $this->quote($registro[0]['endereco_estado']).';';
            $line .= $registro[0]['endereco_cep'].';';
            $line .= $registro[0]['complemento'].';';
            $line .= $registro[0]['ddd_residencial'].';';
            $line .= $registro[0]['descricao_residencial'].';';
            $line .= $registro[0]['nome_residencial'].';';
            $line .= $registro[0]['ddd_comercial'].';';
            $line .= $registro[0]['descricao_comercial'].';';
            $line .= $registro[0]['nome_comercial'].';';
            $line .= $registro[0]['ddd_referencia'].';';
            $line .= $registro[0]['descricao_referencia'].';';
            $line .= $registro[0]['nome_referencia'].';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= $this->quote($registro[0]['descricao']).";";
            $line .= $this->quote($registro[0]['tempo_relacionamento']).";";
            $line .= $this->quote($registro[0]['status'])."\r\n";
        } else {
            $line = $this->quote($registro[0]['nome']).';';
            $line .= substr(str_replace('/', '', AppModel::dbDateToDate($registro[0]['data_nascimento'])),0,8).';';
            $line .= 'M;';
            $line .= $registro[0]['codigo_documento'].';';
            $line .= $registro[0]['rg'].';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= $this->quote($registro[0]['endereco_tipo'].' '.$registro[0]['endereco_logradouro']).';';
            $line .= $registro[0]['numero'].';';
            $line .= $registro[0]['complemento'].';';
            $line .= $registro[0]['endereco_bairro'].';';
            $line .= $registro[0]['endereco_cidade'].';';
            $line .= $registro[0]['endereco_estado'].';';
            $line .= $registro[0]['endereco_cep'].';';
            $line .= substr($registro[0]['descricao_residencial'],1,2).';';
            $line .= substr(Comum::soNumero($registro[0]['descricao_residencial']),2,8).';';
            $line .= ';';
            $line .= substr($registro[0]['descricao_comercial'],1,2).';';
            $line .= substr(Comum::soNumero($registro[0]['descricao_comercial']),2,8).';';
            $line .= ';';
            $line .= substr($registro[0]['descricao_celular'],1,2).';';
            $line .= substr(Comum::soNumero($registro[0]['descricao_celular']),2,9).';';
            $line .= ';';
            $line .= ';';
            $line .= $registro[0]['email_residencial'].';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';';
            $line .= ';'."\r\n";
        }   
        return $line;
    }

    private function quote($text) {
        return '"'.$text.'"';
    }

}
