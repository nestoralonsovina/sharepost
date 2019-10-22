<?php
session_start();

// Flash message helper
// EXAMPLE - flash('register success', 'you are now registered')
// DISPLAY IN VIEW - echo flash('register_success');
function flash($name = '', $message = '', $class = 'alert alert-success') {
    if (!empty($name) and !empty($message)) {
        if (!empty($_SESSION[$name])) {
            unset($_SESSION[$name]);
        }

        if (!empty($_SESSION[$name . '_class'])) {
            unset($_SESSION[$name . '_class']);
        }

        $_SESSION[$name] = $message;
        $_SESSION[$name . '_class'] = $class;
    } else if (empty($message) and !empty($_SESSION[$name])) {
        $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
        echo "<div class='$class' id='msg-flash'>$_SESSION[$name]</div>";

        unset($_SESSION[$name]);
        unset($_SESSION[$name . '_class']);
    }
}

function isLogedIn() {
    if (isset($_SESSION['user_id'])) {
        return true;
    }
    return false;
}
