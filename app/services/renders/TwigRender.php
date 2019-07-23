<?php


namespace App\services\renders;


class TwigRender implements IRender
{
    public function render($template, $params)
    {
        $loader = new \Twig\Loader\FilesystemLoader([
            $_SERVER["DOCUMENT_ROOT"] . "/../views/twig/",
            $_SERVER["DOCUMENT_ROOT"] . "/../views/"
        ]);
        $twig = new \Twig\Environment($loader);
        $template .= ".twig";
        return $twig->render($template, $params);
    }
}