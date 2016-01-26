<?php

require '../models/Databases.php';
require '../lib/Functions.php';
require '../vendor/autoload.php';

$connection = Databases::connect();

$query = " SELECT cha_cdichamado, cha_qtiavisosemail, emp_dssempresa
           FROM   chamados
           JOIN   empresas ON emp_cdiempresa = cha_cdiempresa
           WHERE  cha_cdiusuario_atendente IS NULL
           AND    cha_cdisituacao NOT IN (9)
           AND    cha_qtiavisosemail < 3
           ORDER  BY cha_cdichamado ";

$stmt = $connection->prepare($query);

$stmt->execute();

$rows = $stmt->fetchAll();

foreach ($rows as $row) {
    // Incrementa 1 na quantidade de avisos enviados
    $chamadoAvisos = $row->cha_qtiavisosemail + 1;
    $chamadoNumero = $row->cha_cdichamado;
    $empresaNome   = $row->emp_dssempresa;
    
    // Envia aviso
    $to = "site@basisit.com.br";
    
    $subject = "BasisIT :: Chamado sem Analista :: Número " . $chamadoNumero . " :: " . $empresaNome;
    
    $txt = "O chamado " . $chamadoNumero . " foi aberto e está sem analista responsável definido.";
    $txt = $txt . "<br /><br />";
    $txt = $txt . "Este é o aviso número " . $chamadoAvisos . " de um total de 3.";
    $txt = $txt . "<br /><br />";
    $txt = $txt . "Após 3 avisos não será mais disparado este email.";
    $txt = $txt . '<br /><br />';
    $txt = $txt . 'OBS: Este e-mail foi gerado automaticamente. Favor não responder para este endereço.';
    $txt = $txt . '<br /><br />';
    $txt = $txt . '<img src="www.basisit.com.br/basisit/template/images/assinatura.png" />';
    
    Functions::email($to, $subject, $txt);
    
    // Salva quantidade de aviso enviados no chamado
    $query = " UPDATE chamados
               SET    cha_qtiavisosemail = :cha_qtiavisosemail
               WHERE  cha_cdichamado = :cha_cdichamado ";
    
    $stmt = $connection->prepare($query);
    
    $stmt->bindParam(':cha_qtiavisosemail', $chamadoAvisos);
    $stmt->bindParam(':cha_cdichamado', $chamadoNumero);
    
    $stmt->execute();
}

Databases::disconnect($connection);