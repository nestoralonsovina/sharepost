<?php
// simple page redirect
function redirect($page) {
    header('Location: ' . '/' . $page);
}
