<?php

class Posts extends Controller
{
    private $postModel;
    private $userModel;

    public function __construct()
    {
        if (!isLogedIn()) {
            redirect('users/login');
        }

        $this->postModel = $this->model('Post');
        $this->userModel = $this->model('User');
    }

    public function index() {
        $posts = $this->postModel->getPosts();
        $data = [
            'posts' => $posts
        ];
        $this->view('posts/index', $data);
    }

    public function add()
    {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // sanitize post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => $_SESSION['user_id'],
                'title_error' => '',
                'body_error' => ''
            ];

            // validate data
            if (empty($data['title'])) {
                $data['title_error'] = 'Please enter title';
            }

            if (empty($data['body'])) {
                $data['body_error'] = 'Please enter body';
            }

            if (empty($data['title_error']) && empty($data['body_error'])) {
                // validated
                if ($this->postModel->addPost($data)) {
                    flash('post_message', 'Post Added');
                    redirect('posts');
                } else {
                    die('Something went wrong');
                }
            } else {
                // load errors
                $this->view('posts/add', $data);
            }

        } else {
            $data = [
                'title' => '',
                'body' => '',
                'title_error' => '',
                'body_error' => ''
            ];
            $this->view('posts/add', $data);
        }
    }

    public function edit($id)
    {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            // sanitize post
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'id' => $id,
                'title' => trim($_POST['title']),
                'body' => trim($_POST['body']),
                'user_id' => $_SESSION['user_id'],
                'title_error' => '',
                'body_error' => ''
            ];

            // validate data
            if (empty($data['title'])) {
                $data['title_error'] = 'Please enter title';
            }

            if (empty($data['body'])) {
                $data['body_error'] = 'Please enter body';
            }

            if (empty($data['title_error']) && empty($data['body_error'])) {
                // validated
                if ($this->postModel->updatePost($data)) {
                    error_log('Updated post');
                    flash('post_message', 'Post Updated');
                    redirect('posts');
                } else {
                    die('Something went wrong');
                }
            } else {
                // load errors
                $this->view('posts/edit', $data);
            }

        } else {
            // get existing post
            $post = $this->postModel->getPostById($id);

            if ($post->user_id != $_SESSION['user_id']) {
                redirect('posts');
            }
            $data = [
                'id' => $id,
                'title' => $post->title,
                'body' => $post->body,
                'title_error' => '',
                'body_error' => ''
            ];
            $this->view('posts/edit', $data);
        }
    }

    public function show($id) {
        $post = $this->postModel->getPostById($id);
        $user = $this->userModel->getUserById($post->user_id);
        $data = [
            'post' => $post,
            'user' => $user
        ];

        $this->view('posts/show', $data);
    }

    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $post = $this->postModel->getPostById($id);

            if ($post->user_id != $_SESSION['user_id']) {
                redirect('posts');
            }
            if ($this->postModel->deletePost($id)) {
                flash('post_message', 'Post Removed');
                redirect('posts');
            } else {
                die('something went wrong');
            }
        } else {
            redirect('posts');
        }

    }
}