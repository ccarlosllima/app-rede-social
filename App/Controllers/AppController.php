<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action 
{
    public function timeline()
    {
        $this->validaAutenticacao();

        $tweet = Container::getModel('tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);
                    
        $tweets =  $tweet->getAll();
        
        $this->view->tweets = $tweets;

        $usuario = Container::getModel('usuario');
        $usuario->__set('id',$_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->totoal_tweets = $usuario->getTotoalTweets();
        $this->view->totoal_seguindo = $usuario->getTotoalSeguindo();
        $this->view->totoal_seguidores = $usuario->getTotoalSeguidores();
        
        $this->render('timeline');


    }

    public function tweet()
    {
        $this->validaAutenticacao();

        $tweet = Container::getModel('tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header('location:/timeline');
        
    }
    public function quemSeguir()
    {
        $this->validaAutenticacao();

        $pesquisandorPor = isset($_GET['pesquisarPor'])?$_GET['pesquisarPor']:'';

        $usuarios = array();

        if ($pesquisandorPor != ''){

            $usuario = Container::getModel('usuario');
            $usuario->__set('nome',$pesquisandorPor);
            $usuario->__set('id',$_SESSION['id']);
            $usuarios = $usuario->getAll();
        }
        $this->view->usuarios = $usuarios;
        
        $usuario = Container::getModel('usuario');
        $usuario->__set('id',$_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->totoal_tweets = $usuario->getTotoalTweets();
        $this->view->totoal_seguindo = $usuario->getTotoalSeguindo();
        $this->view->totoal_seguidores = $usuario->getTotoalSeguidores();
        

        $this->render('quemSeguir');
    
    }

    public function validaAutenticacao()
    {
        session_start();

        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header("location:/?login=error");
        }
    }
    public function acao(){
        $this->validaAutenticacao();
       
        $acao = isset($_GET['acao']) ? $_GET['acao']:'';
        $id_usuario_segindo = isset($_GET['id_usuario']) ? $_GET['id_usuario']:'';

        $usuario = Container::getModel('usuario');
        $usuario->__set('id',$_SESSION['id']);

        if ($acao == 'seguir') {
            $usuario->seguirUsuario($id_usuario_segindo);
        }else if ($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($id_usuario_segindo);   
        }
        header('Location: /quem_seguir');
        
    }
    public function removerTweet(){
        $tweet = Container::getModel('tweet');
        $tweet->__set('id',$_GET['id']);
       
        if ( $tweet->delete() > 0) {
            \header('location:/timeline');
        }
    }

}

?>