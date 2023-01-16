<?php
class MapaHelper extends Helper{
	var $helpers = array('GoogleMap', 'GeoPortalMap');
	public function desenhaMapa($options){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->desenhaMapa($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->desenhaMapa($options);
		}
	}
	public function setup($options = NULL){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->setup($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->setup($options);
		}
	}
    public function iniciaBloco($options = null){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->iniciaBloco($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->iniciaBloco($options);
		}
    }
    public function encerraBloco($options = null){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->encerraBloco($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->encerraBloco($options);
		}
    }
    public function carregaArraysArmazenamento($arrays){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->carregaArraysArmazenamento($arrays);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->carregaArraysArmazenamento($arrays);
		}
    }
    public function map(){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->map();
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->map();
		}
    }
    public function criaRota($options = null, $seq_rota = null){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->criaRota($options, $seq_rota);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->criaRota($options, $seq_rota);
		}
    }
    public function criaRotaComPosicoes($options = null, $seq_rota = null, $posicoes = null){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->criaRotaComPosicoes($options, $seq_rota, $posicoes);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->criaRotaComPosicoes($options, $seq_rota, $posicoes);
		}
    }
    public function carregaRotasGeradas($qtd_rotas){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->carregaRotasGeradas($qtd_rotas);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->carregaRotasGeradas($qtd_rotas);
		}
    }
    public function carregaSteps($options = null){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->carregaSteps($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->carregaSteps($options);
		}
    }
    public function criaMarcadoresPosicoesRota($posRota, $posicoes, $userCodigoPerfil = NULL){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->criaMarcadoresPosicoesRota($$posRota, $posicoes, $userCodigoPerfil);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->criaMarcadoresPosicoesRota($$posRota, $posicoes, $userCodigoPerfil);
		}
    }
    public function criaArrayPosicoesRota($tipo = 1, $posRota = array()){
    	if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->criaArrayPosicoesRota($$tipo, $posRota);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->criaArrayPosicoesRota($$tipo, $posRota);
		}
    }
	public function criaMarcadores($options = null){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->criaMarcadores($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->criaMarcadores($options);
		}
	}
	public function criaRetangulos($options = null){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->criaRetangulos($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->criaRetangulos($options);
		}
	}
	public function criaLinhas($options = null){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->criaLinhas($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->criaLinhas($options);
		}
	}
	public function mapaEdicaoAlvo($options){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->mapaEdicaoAlvo($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->mapaEdicaoAlvo($options);
		}
	}
	public function mapaEdicao($options){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->mapaEdicao($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->mapaEdicao($options);
		}
	}

	public function mapaFornecedores($options){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->mapaFornecedores($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->mapaFornecedores($options);
		}
	}

	public function mapaClientes($options){
		if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
			return $this->GoogleMap->mapaClientes($options);
		}elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->mapaClientes($options);
		}
	}
	
	public function localizaCredenciado($options){
		// if(Ambiente::getMapa() == Ambiente::MAPA_GOOGLE){
		// 	return $this->GoogleMap->mapaClientes($options);
		// }elseif(Ambiente::getMapa() == Ambiente::MAPA_GEOPORTAL){
			return $this->GeoPortalMap->localizaCredenciado($options);
		// }
	}
	

	
}
?>