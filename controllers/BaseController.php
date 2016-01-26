<?php

class BaseController {
    
    function BaseController() {
        $index = new IndexController();
        
        if (!$index->verificarSessaoAtiva()) {
            $index->expirarSessao();
        } else {
            $index->atualizarSessao();
        }
    }
    
}
