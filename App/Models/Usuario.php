<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model 
{
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $value)
    {
        $this->$atributo = $value;
    }

    /**
     * SALVA USUARIO NO BANCO DE DADOS
     */
    public function salvar()
    {
        $query = 'INSERT INTO usuarios(nome, email, senha) VALUES(:nome, :email, :senha)';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();
        return $this;
    }
    /**
     * DETERMINA UMA CONDIÇÃO PARA O CADASTRO PODER SER CONCLUIDO COM SUCESSO
     */
    public function validarCadastro()
    {
        $valido = true;
        
        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }
        if (strlen($this->__get('email')) < 3 ) {
            $valido = false;
        }
        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }
        return $valido;
    }
    /**
     * MÉTODO RESPONSAVEL POR VERIFICAR SE O EMAIL DE UM USUARIO JÁ ESTA CADASTRADO NO BANCO
     */
    public function getUsuarioPorEmail()
    {
        $query = 'SELECT nome, email, senha FROM usuarios WHERE email = :email';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email',$this->__get('email'));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    /**
     * MÉTODO RESPONSAVEL POR BUSCAR UM USUÁRIO NO BANCO DE DADOS
     */
    public function autenticar()
    {
        $query = 'SELECT * FROM usuarios WHERE email = :email AND senha = :senha';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha',$this->__get('senha'));
        $stmt->execute();
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($usuario['id'] != "" && $usuario['nome'] != "") {
            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
        }

        return $this;
    }
    /**
     * MÉTODO RESPONSÁVEL POR BUSCAR TODOS OS USUARIOS 
     */
    public function getAll()
    {
        $query = '
            SELECT 
                u.id, 
                u.nome, 
                u.email,
                (
                    select
                        count(*)
                    from 
                        usuarios_seguidores as us
                    where
                        us.id_usuario = :id_usuario and us.id_usuario_seguindo = u.id
                ) as seguindo_sn  
            FROM 
                usuarios as u 
            WHERE 
                u.nome LIKE :nome AND u.id != :id_usuario
        ';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
    }
    /**
     * MÉTODO RESPONSÁVEL POR SEGUIR UM USUARIO
     */
    public function seguirUsuario($id_usuario_seguindo)
    {
       $query = 'insert into usuarios_seguidores(id_usuario, id_usuario_seguindo) values(:id_usuario, :id_usuario_seguindo)';
       $stmt = $this->db->prepare($query);
       $stmt->bindValue(':id_usuario',$this->__get('id'));
       $stmt->bindValue(':id_usuario_seguindo',$id_usuario_seguindo);
       $stmt->execute();

       return true;
    }
    /**
     * MÉTODO RESPONSÁVEL POR DEIXAR DE SEGUIR UM USUARIO
     */
    public function deixarSeguirUsuario($id_usuario_seguindo)
    {
        $query = 'delete from usuarios_seguidores where id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo';   
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo',$id_usuario_seguindo);
        $stmt->execute();

        return true;
    }
    /**
     * MÉTODO RESPONSÁVEL POR BUSCAR AS INFORMAÇÕES DO USUARIO
     */
    public function getInfoUsuario()
    {
        $query = "select nome from usuarios where id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
     /**
     * MÉTODO RESPONSÁVEL POR BUSCAR O TOTAL DE TWEETS
     */
    public function getTotoalTweets()
    {
        $query = "select count(*) as total_tweets from tweets where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
     /**
     * MÉTODO RESPONSÁVEL POR BUSCAR O TOTAL DE USUARIOS QUE ESTA SEGUINDO
     */
    public function getTotoalSeguindo()
    {
        $query = "select count(*) as total_seguindo from usuarios_seguidores where id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
     /**
     * MÉTODO RESPONSÁVEL POR BUSCAR O TOTAL DE USUARIOS QUE ESTA SEGUINDO-ME
     */
    public function getTotoalSeguidores()
    {
        $query = "select count(*) as total_seguidores from usuarios_seguidores where id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

   
}


?>