<?php

class Databases {
    
    static function connect() {
        
        // Base de Acesso Local
        $host = 'localhost';
        $banco = 'basisit1';
        $usuario = 'root';
        $senha = '';
        
        // Base de Produção
        $host = '177.153.16.177';
        $banco = 'basisit1';
        $usuario = 'basisit1';
        $senha = 'C0ntr0l3H0r4s';
        
        // Base de Teste
        $host = '177.153.16.177';
        $banco = 'basisit_teste';
        $usuario = 'basisit_teste';
        $senha = 'T3st3Marcio';
        
        $dsn = "mysql:host=$host;dbname=$banco";
        
        $connection = new PDO($dsn, $usuario, $senha);
        
        // Makes all DB errors throw exceptions
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // With this there's no need to fetch passing PDO::FETCH_OBJ as a parameter
        $connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        
        return $connection;
        
    }
    
    static function disconnect($connection) {
        
        $connection = null;
        
    }
    
}