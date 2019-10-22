<?php

class Post
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getPosts() {
        $sql = 'SELECT *,
                posts.id as postId,
                users.id as userId,
                posts.created_at as postCreated,
                users.created_at as userCreated
                FROM posts
                INNER JOIN users
                ON posts.user_id = users.id
                ORDER BY posts.created_at DESC
                ';
        $this->db->query($sql);

        return $this->db->resultSet();
    }

    public function addPost(array $data) {

        $sql = 'INSERT INTO posts (title, user_id, body) VALUES(:title, :user_id, :body)';
        $this->db->query($sql);
        // bind values
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':user_id', $data['user_id']);
        $this->db->bind(':body', $data['body']);

        // execute
        return $this->db->execute() ? true : false;
    }

    public function getPostById($id) {
        $sql = 'SELECT * FROM posts where id = :id';
        $this->db->query($sql);
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    public function updatePost(array $data) {

        error_log(print_r($data, true));

        $sql = 'UPDATE posts SET title = :title, body = :body WHERE id = :id';
        $this->db->query($sql);
        // bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':body', $data['body']);
        // execute
        return $this->db->execute() ? true : false;
    }

    public function deletePost($id) {
        $this->db->query('DELETE FROM posts WHERE id = :id');
        $this->db->bind(':id', $id);

        return $this->db->execute() ? true : false;
    }

}