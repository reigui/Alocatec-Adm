<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

class Store {
    // Define ou atualiza um valor no estado global (sessão)
    public static function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    // Recupera um valor do estado global
    public static function get($key) {
        return $_SESSION[$key] ?? null;
    }

    // Remove um valor
    public static function remove($key) {
        unset($_SESSION[$key]);
    }

    // Verifica se usuário está logado
    public static function isLogged() {
        return isset($_SESSION['usuario']);
    }

    // Logout
    public static function logout() {
        session_destroy();
    }
}
}