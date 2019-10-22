<?php

class User
{
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Register user
    public function register(array $data) {
        $sql = 'INSERT INTO users (name, email, password) VALUES(:name, :email, :password)';
        $this->db->query($sql);
        // bind values
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':password', $data['password']);

        // execute
        if ($this->db->execute()) {
            return true;
        }
        return false;
    }

    public function login($email, $password) {
        $this->db->query('SELECT * FROM users WHERE email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if (password_verify($password, $row->password)) {
            return $row;
        }
        return false;
    }

    // Find user by email
    public function findUserByEmail($email) {

        $sql = 'SELECT *  FROM users WHERE email = :email';
        $this->db->query($sql);
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        // check row
        if ($this->db->rowCount() > 0) {
           return true;
        }
        return false;
    }

    // get user by id
    public function getUserById($id) {

        $sql = 'SELECT *  FROM users WHERE id = :id';
        $this->db->query($sql);
        $this->db->bind(':id', $id);

        return $this->db->single();
    }


}