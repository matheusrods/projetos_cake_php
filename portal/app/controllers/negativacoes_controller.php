<?php
class NegativacoesController extends AppController {
    var $name = 'Negativacoes';
    var $uses = array('Negativacao','Remessa');
    
    function index() {
        $this->pageTitle = 'Negativação Serasa';
        $total_inclusoes = count($this->Negativacao->registrosParaInclusao());
        $total_exclusoes = count($this->Negativacao->registrosParaExclusao());
        $this->set(compact('total_inclusoes', 'total_exclusoes'));
    }
    
    function gerar_arquivos() {
        $this->autoRender = false;
        
        $arquivo = $this->data['Negativacao']['arquivo'];
        $codigo_operacao = $this->data['Negativacao']['codigo_operacao'];
        
        if($arquivo == 'prorede') {
            $this->gerar_prorede($codigo_operacao);
        } elseif($arquivo == 'convem'){
            $this->gerar_txt($codigo_operacao);
        }
    }
    
    function gerar_prorede($codigo_operacao) {
        $this->autoRender = false;
        $this->Remessa->nova_remessa();
        
        switch ($codigo_operacao) {
            case 'E':
                $codigo_operacao = 'C';
                break;
            case 'R':
                break;
            default:
                $codigo_operacao = 'A';
        }
        
        $registro_header   = '';
        $registro_detalhe  = '';
        $registro_trailler = '';
        
        $nome_contato = 'ELCIO';
        $n_sequencial = 1;
        $n_remessa = $this->Remessa->id;
        
        $registro_header  = '0';
        $registro_header .= '06326025000166';
        $registro_header .= date('Ymd', time());
        $registro_header .= 'SERASA-PROREDE ';
        $registro_header .= str_pad($n_remessa, 4, '0', STR_PAD_LEFT);
        $registro_header  = str_pad($registro_header, strlen($registro_header) + 162, ' ', STR_PAD_RIGHT);
        $registro_header  = str_pad($registro_header, strlen($registro_header) + 30, ' ', STR_PAD_RIGHT);
        $registro_header  = str_pad($registro_header, strlen($registro_header) + 15, ' ', STR_PAD_RIGHT);
        $registro_header .= str_pad($n_sequencial, 7, '0', STR_PAD_LEFT);
        $registro_header .= "\r\n";
        
        $n_sequencial += 1;
        $registro_detalhe  = '1';
        $registro_detalhe .= str_pad('BUONNY PROJETOS E SERVICOS', 27, ' ', STR_PAD_RIGHT);
        $registro_detalhe .= str_pad('BUONNY', 23, ' ', STR_PAD_RIGHT);
        $registro_detalhe .= str_pad('ALAMEDA DOS GUATAS 191', 35, ' ', STR_PAD_RIGHT);
        $registro_detalhe .= str_pad('SAUDE', 15, ' ', STR_PAD_RIGHT);
        $registro_detalhe .= '04053040';
        $registro_detalhe .= str_pad('SAO PAULO', 15, ' ', STR_PAD_RIGHT);
        $registro_detalhe .= 'SP';
        $registro_detalhe .= '00011';
        $registro_detalhe .= '034432525';
        $registro_detalhe .= '00000';
        $registro_detalhe .= str_pad('ELCIO R. GALLO', 30, ' ', STR_PAD_RIGHT);
        $registro_detalhe .= '006326025000166';
        $registro_detalhe .= '00000';
        $registro_detalhe .= $codigo_operacao;
        $registro_detalhe .= date('Ymd', time());
        $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 30, ' ', STR_PAD_RIGHT);
        $registro_detalhe .= '2335';
        $registro_detalhe .= '0000120836';
        $registro_detalhe .= '1';
        $registro_detalhe .= str_pad($n_sequencial, 7, '0', STR_PAD_LEFT);
        $registro_detalhe .= "\r\n";
        
        $n_sequencial += 1;
        $registro_trailler = '9';
        $registro_trailler = str_pad($registro_trailler, strlen($registro_trailler) + 203, ' ', STR_PAD_RIGHT);
        $registro_trailler = str_pad($registro_trailler, strlen($registro_trailler) + 30, ' ', STR_PAD_RIGHT);
        $registro_trailler = str_pad($registro_trailler, strlen($registro_trailler) + 15, ' ', STR_PAD_RIGHT);
        $registro_trailler .= str_pad($n_sequencial, 7, '0', STR_PAD_LEFT);
        $registro_trailler .= "\r\n";
        
        $nome_arquivo = 'PROREDE'.date('Ymd', time()).str_pad($n_remessa, 4, '0', STR_PAD_LEFT).($codigo_operacao == 'C' ?'_EXCLUSAO':'_INCLUSAO').'.txt';
        header('Content-Type: application/force-download');
        header("Content-Disposition: attachment; filename=\"$nome_arquivo\"");
        
        $arquivo = $registro_header.$registro_detalhe.$registro_trailler;
        echo utf8_decode($arquivo);
    }

    function gerar_txt($codigo_operacao) {
        $this->autoRender = false;
        
        $registro_header   = '';
        $registro_detalhe  = '';
        $registro_trailler = '';
        
        $negativacoes = $codigo_operacao == 'I' ? $this->Negativacao->registrosParaInclusao(): $this->Negativacao->registrosParaExclusao();
        
        $nome_contato = 'ELCIO';
        $n_sequencial = 1;
        $n_remessa = $this->Remessa->ultima_remessa();
        
        $registro_header  = '0';
        $registro_header .= '006326025';
        $registro_header .= date('Ymd', time());
        $registro_header .= '0011';
        $registro_header .= '34432603';
        $registro_header .= '0000';
        $registro_header .= str_pad($nome_contato, 70,' ', STR_PAD_RIGHT);
        $registro_header .= 'SERASA-CONVEM04';
        $registro_header .= str_pad($n_sequencial, 6, '0', STR_PAD_LEFT);
        $registro_header .= 'E';
        $registro_header  = str_pad($registro_header, strlen($registro_header) + 4, ' ', STR_PAD_RIGHT);
        $registro_header  = str_pad($registro_header, strlen($registro_header) + 3, ' ', STR_PAD_RIGHT);
        $registro_header  = str_pad($registro_header, strlen($registro_header) + 400, ' ', STR_PAD_RIGHT);
        $registro_header  = str_pad($registro_header, strlen($registro_header) + 60, ' ', STR_PAD_RIGHT);
        $registro_header .= str_pad($n_sequencial, 7, '0', STR_PAD_LEFT);
        $registro_header .= "\r\n";

        foreach($negativacoes as $negativacao) {
            $n_sequencial += 1;
            
            $registro_detalhe = '';
            $registro_detalhe .= '1';
            $registro_detalhe .= $codigo_operacao;
            $registro_detalhe .= substr($negativacao['Cliente']['codigo_documento'], strlen($negativacao['Cliente']['codigo_documento']) - 6, 6);
            $registro_detalhe .= $this->Negativacao->trataDataVencimento(substr($negativacao['Negativacao']['vencimento_nota'], 0, 10));
            $registro_detalhe .= $this->Negativacao->trataDataVencimento(substr($negativacao['Negativacao']['vencimento_contrato'], 0, 10));
            $registro_detalhe .= $negativacao['Negativacao']['natureza'] . ' ';
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 4, ' ', STR_PAD_RIGHT);
            $registro_detalhe .= $negativacao['Negativacao']['tipo_pessoa'];
            $registro_detalhe .= $negativacao['Negativacao']['tipo_pessoa'] == 'J' ? '1': '2';
            $registro_detalhe .= str_pad($negativacao['Negativacao']['numero_documento'], 15, '0', STR_PAD_LEFT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 2, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 1, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 15, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 2, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 1, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 1, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 15, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 2, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 1, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 15, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 2, ' ', STR_PAD_RIGHT);
            $registro_detalhe .= str_pad($negativacao['Negativacao']['devedor_nome'], 70, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 8, '0', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 70, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 70, ' ', STR_PAD_RIGHT);
            $registro_detalhe .= str_pad($negativacao['Negativacao']['devedor_endereco'], 45, ' ', STR_PAD_RIGHT);
            $registro_detalhe .= str_pad($negativacao['Negativacao']['devedor_bairro'], 20, ' ', STR_PAD_RIGHT);
            $registro_detalhe .= str_pad($negativacao['Negativacao']['devedor_municipio'], 25, ' ', STR_PAD_RIGHT);
            $registro_detalhe .= $negativacao['Negativacao']['devedor_uf'];
            $registro_detalhe .= $negativacao['Negativacao']['devedor_cep'];
            $registro_detalhe .= str_pad($negativacao['Negativacao']['valor_nota'], 15, '0', STR_PAD_LEFT);
            $registro_detalhe .= str_pad($negativacao['Negativacao']['numero_do_contrato'], 16, '0', STR_PAD_LEFT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 9, '0', STR_PAD_RIGHT);
            $registro_detalhe .= 'J';
            $registro_detalhe .= '1';
            $registro_detalhe .= '006326025000166';
            $registro_detalhe .= 'BUONNY PROJETOS E SERVICOS DE RISCOS SECURITA';
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 8, ' ', STR_PAD_RIGHT);
            $registro_detalhe  = str_pad($registro_detalhe, strlen($registro_detalhe) + 60, ' ', STR_PAD_RIGHT);
            $registro_detalhe .= str_pad($n_sequencial, 7, '0', STR_PAD_LEFT);
            $registro_detalhe .= "\r\n";
        }

        $n_sequencial += 1;
        
        $registro_trailler = '9';
        $registro_trailler = str_pad($registro_trailler, strlen($registro_trailler) + 532, ' ', STR_PAD_RIGHT);
        $registro_trailler = str_pad($registro_trailler, strlen($registro_trailler) + 60, ' ', STR_PAD_RIGHT);
        $registro_trailler .= str_pad($n_sequencial, 7, '0', STR_PAD_LEFT);
        
        $nome_arquivo = 'CONVEM'.date('Ymd', time()).str_pad($n_remessa, 4, '0', STR_PAD_LEFT).($codigo_operacao == 'I' ?'_INCLUSAO':'_EXCLUSAO').'.txt';
        header('Content-Type: application/force-download');
        header("Content-Disposition: attachment; filename=\"$nome_arquivo\"");
        
        $arquivo = $registro_header.$registro_detalhe.$registro_trailler;
        echo utf8_decode($arquivo);
        
        $this->Negativacao->negativar($negativacoes, $codigo_operacao);
    }
}