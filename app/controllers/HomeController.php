<?php
class HomeController
{
    /**
     * Render the home page.
     */
    public function index()
    {
        include __DIR__ . '/../views/home.php';
    }
}
