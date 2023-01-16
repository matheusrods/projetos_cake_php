<div class='row-fluid'>
    <p>Status: <?php echo $this->data['StatusPesquisaSatisfacao']['descricao_pesquisa']?></p>
    <p>Data da Pesquisa: <?php echo $this->data['PesquisaSatisfacao']['data_pesquisa']?></p>
    <p>Cliente: <?php echo $this->data['Cliente']['razao_social']?></p>
    <p>Contato: <?=$this->data['ClienteContato']['nome']?></p>
    <p>Telefone: <?php echo '('.$this->data['ClienteContato']['ddd'].') '.$this->data['ClienteContato']['descricao']?></p>
    <p>Responsável pela pesquisa: <?php echo $this->data['Usuario']['apelido']?></p>
    <p>Observações:</p>
    <p><?=htmlentities($this->data['PesquisaSatisfacao']['observacao']);?></p>
<div> 