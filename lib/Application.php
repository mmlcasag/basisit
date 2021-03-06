<?php

/**
* @package Exemplo simples com MVC
* @author DigitalDev
* @version 0.1.1
* 
* Camada - Sistema / Controlladores
* Diret�rio Pai - lib 
* 
* Verifica qual classe controlador (Controller) o usu�rio deseja chamar
* e qual m�todo dessa classe (Action) deseja executar
* Caso o controlador (controller) n�o seja especificado, o IndexControllers ser� o padr�o
* Caso o m�todo (Action) n�o seja especificado, o indexAction ser� o padr�o
*/
class Application {
	
	/**
	* Usada pra guardar o nome da classe
	* de controle (Controller) a ser executada
	* @var string
	*/
	protected $st_controller;
	
	/**
	* Usada para guardar o nome do metodo da
	* classe de controle (Controller) que dever� ser executado
	* @var string
	*/
	protected $st_action;
	
	/**
	* Verifica se os parametros de controlador (Controller) e acao (Action) foram
	* passados via parametros "Post" ou "Get" e os carrega tais dados
	* nos respectivos atributos da classe
	*/
	private function loadRoute() {
            /*
            * Se o controller nao for passado por GET,
            * assume-se como padr�o o controller 'IndexController';
            */
            $this->st_controller = isset($_REQUEST['controle']) ?  $_REQUEST['controle'] : 'index';

            /*
            * Se a action nao for passada por GET,
            * assume-se como padr�o a action 'IndexAction';
            */
            $this->st_action = isset($_REQUEST['acao']) ?  $_REQUEST['acao'] : 'index';
	}
	
	/**
	* Instancia classe referente ao Controlador (Controller) e executa
	* m�todo referente e  acao (Action)
	* @throws Exception
	*/
	public function dispatch() {
		
		$this->loadRoute();
		
		// verificando se o arquivo de controle existe
		$st_controller_file = 'controllers/'.ucfirst($this->st_controller).'Controller.php';
		if(file_exists($st_controller_file))
			require_once $st_controller_file;
		else
			throw new Exception('Arquivo '.$st_controller_file.' nao encontrado');
		
		// verificando se a classe existe
		$st_class = $this->st_controller.'Controller';
		if(class_exists($st_class))
			$o_class = new $st_class;
		else
			throw new Exception("Classe '$st_class' nao existe no arquivo '$st_controller_file'");
		
		// verificando se o metodo existe
		$st_method = $this->st_action.'Action';
		if (method_exists($o_class, $st_method))
			$o_class->$st_method();
		else 
			throw new Exception("Metodo '$st_method' nao existe na classe $st_class'");
		
	}
	
	/**
	* Redireciona a chamada http para outra p�gina
	* @param string $st_uri
	*/
	static function redirect($link) {
		
		header("Location: $link");
		
	}
	
}
?>