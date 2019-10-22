<?php

class Users extends Controller
{
    private $userModel;

    public function __construct() {
        $this->userModel = $this->model('User');
    }

    public function register() {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process the form

            // sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_error' => '',
                'email_error' => '',
                'password_error' => '',
                'confirmed_password_error' => ''
            ];

            $error = false;

            // Validate email
            if (empty($data['email'])) {
               $data['email_error'] = 'Please enter email';
               $error = true;
            } else if ($this->userModel->findUserByEmail($data['email'])) {
                $data['email_error'] = 'Email is already registered.';
                $error = true;
            }

            // Validate name
            if (empty($data['name'])) {
                $data['name_error'] = 'Please enter name';
                $error = true;
            }

            // Validate password
            if (empty($data['password'])) {
                $data['password_error'] = 'Please enter password';
                $error = true;
            } else if (strlen($data['password']) < 6) {
                $data['password_error'] = 'Password must be at least 6 characters';
                $error = true;
            }

            // Validate confirmed password
            if (empty($data['password'])) {
                $data['confirmed_password_error'] = 'Please enter confirm password';
                $error = true;
            } else if ($data['password'] != $data['confirm_password']) {
                $data['confirmed_password_error'] = 'Passwords don\'t match';
                $error = true;
            }

            if ($error == false) {
                // Validated

                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register User
                if ($this->userModel->register($data)) {
                    flash('register_success', 'Your are registered and can login');
                    redirect('users/login');
                } else {
                    die('Something went wron');
                }
            } else {
                // Load view with errors
                $this->view('users/register', $data);
            }



        } else {
            // Init data

            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_error' => '',
                'email_error' => '',
                'password_error' => '',
                'confirmed_password_error' => ''
            ];

            // Load the HTMLForm
            // Load view
            $this->view('users/register', $data);
        }
    }

    public function login() {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_error' => '',
                'password_error' => '',
            ];

            $error = false;

            // Validate email
            if (empty($data['email'])) {
                $data['email_error'] = 'Please enter email';
                $error = true;
            }

            if (empty($data['password'])) {
                $data['password_error'] = 'Please enter password';
                $error = true;
            }

            // user not found
            if (!$this->userModel->findUserByEmail($data['email'])) {
                $data['email_error'] = 'No user with this mail.';
                $error = true;
            }

            // check no errors
            if ($error == false) {
                // Validated
                $loggedInUser = $this->userModel->login($data['email'], $data['password']);

                if ($loggedInUser !== false) {
                   $this->createUserSession($loggedInUser);
                   redirect('posts');
                } else {
                    $data['password_error']  = 'Password Incorrect';
                    $this->view('users/login', $data);
                }

            } else {
                // Load view with errors
                $this->view('users/login', $data);
            }

        } else {
            // Init data

            $data = [
                'email' => '',
                'password' => '',
                'email_error' => '',
                'password_error' => '',
            ];

            // Load the HTMLForm
            // Load view
            $this->view('users/login', $data);
        }
    }

    public function logout() {
       unset($_SESSION['user_id']);
       unset($_SESSION['user_email']);
       unset($_SESSION['user_name']);
       session_destroy();
       redirect('users/login');
    }


    public function createUserSession($user) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;
    }

}