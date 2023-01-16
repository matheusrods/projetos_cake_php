<?php
class ProcessamentosShell extends Shell
{
    var $uses = array(
        'Processamento'
    );

    public function main()
    {
        echo "**********************\n";
        echo "* Processamentos \n";
        echo "**********************\n";
    }

    public function deleta(){
        echo "Buscando arquivos processados para serem deletados.. \n";
        $URLS = $this->Processamento->shellGetAllFilesFromDaysAgo();
        foreach($URLS as $url){
            if(!empty($url[0]['caminho']) && !is_null($url[0]['caminho'])){
                echo "Enviando requisição para deletar o processamento(arquivo) {".$url[0]['codigo']."} ... \n";
                $deletado = $this->FileServerDeletaArquivo($url[0]['caminho']);
                if($deletado){
                    echo "\t Setando como deletado o processamento(arquivo) {".$url[0]['codigo']."} ... \n";
                    $this->Processamento->shellSetAsDeletedFile($url[0]['codigo']);
                }else{
                    echo "\t ERROR - SHELL: falha na tentativa de deletar o processamento(arquivo) {".$url[0]['codigo']."} ... \n";
                    $this->log("FALHA - SHELL: requisição para deletar processamento(arquivo) - codigo ".$url[0]['codigo']." | url: ".$url[0]['caminho']);
                }
            }
        }
        echo "FIM deletando processamento(arquivos)! \n";
    }

    private function FileServerDeletaArquivo($caminho){

        if(!strpos($caminho, "it_health"))//url quebrada, deleta arquivo
            return true;

        $url_delete = substr($caminho, 0, strpos($caminho, "it_health")) . "delete/" . substr($caminho, strpos($caminho, "it_health"));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_delete);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        $result = curl_exec($ch);
        //$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        unset($ch);

        if(is_string($result) && strpos($result, "\"request\":")){
            $data_result = json_decode($result);
            if($data_result->response == "O arquivo não foi encontrado")
                $data_result->error = false;
        }else{
            $data_result = new stdClass();
            $data_result->error = true;
            $data_result->response = 'Arquivo inexistente ou caminho incorreto!';
        }

        if($data_result->error == true){
            $this->log("ERROR - SHELL, FILE SERVER: processamento - não foi possivel deletar arquivo: " . $data_result->response);
            return false;
        }else{
            return true;
        }
    }


}

