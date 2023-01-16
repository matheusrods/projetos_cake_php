<?php
class ImportaComponent extends Component {
    var $name = 'Importa';
    private $fp;
    private $parse_header;
    private $header;
    private $delimiter;
    private $length;
    private $lines;
    private $quantidade;

    /*
    function initialize(&$controller, $settings = array()) {
        //saving the controller reference for later use
        $this->controller =& $controller;
    }
    */

    /*
    *  Pega arquivo csv e retorna os campos comforme a linha 1
    *
    */
    function importar_csv($file_name,$parse_header=false,$delimiter="\t",$campos, $qtde_registro, $max_lines=1000,$length = 8000) {
        if (!empty($file_name)) {
            $this->pega_arquivo($file_name,$parse_header,$delimiter,$campos,$qtde_registro,$length);
            if (is_array($this->header)) {
                return $this->pegar_dados_csv($max_lines);
            } else {
                return $this->header;
            }
        } else {
            return "Erro";
        }
    }

    function pega_arquivo($file_name, $parse_header = false, $delimiter = "\t",$campos,$qtde_registro, $lines=1000,  $length = 8000) {
        $this->fp = fopen($file_name, "r");
        $this->parse_header = $parse_header;
        $this->delimiter = $delimiter;
        $this->length = $length;
        $this->lines = $lines;
        // Verifica se possui o Cabecalho com os Campos
        if ($this->parse_header) {
            $this->header = fgetcsv($this->fp, $this->length,$this->delimiter);
        }

        if (!empty($campos)) {
            $header_new= array();
            $x=0;
            //Faz a troca do CABECALHO na mesma posicao do CSV (!IMPORTANTE)
            foreach($this->header as $valor) {
                $header_new[] =  $campos[$valor];
                $x++;
            }
            unset($this->header);
            $this->header =  $header_new;
            if ($qtde_registro != $x) {
                $this->header = 'A Quantidade de Items no Cabeçalho no arquivo CSV é incompátivel';
            }
        }
    }

    function pegar_dados_csv($max_lines = 0) {
        //if $max_lines is set to 0, then get all the data
        $data = array();
        if ($max_lines > 0)
            $line_count = 0;
        else
            $line_count = -1; // so loop limit is ignored

        while ($line_count < $max_lines && ($row = fgetcsv($this->fp, $this->length, $this->delimiter)) !== FALSE) {
            if ($this->parse_header) {

                foreach ($this->header as $i => $heading_i) {
                    $row_new[$heading_i] = $row[$i];
                }
                $data[] = $row_new;
            } else {
                $data[] = $row;
            }
            if ($max_lines > 0)
                $line_count++;
        }
        return $data;
    }


}