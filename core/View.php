<?php

namespace core;

use core\Exceptions\ViewNotFoundException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

class View
{
    public FilesystemLoader $loader;
    public Environment $environment;

    public function __construct(string $view_path, string $layout_path = '', array $other_paths = [])
    {
        $other_paths = Filesystem::processPaths($other_paths);
        
        $this->loader = new FilesystemLoader([$view_path, $layout_path, ...$other_paths]);
        $this->environment = new Environment($this->loader, [
//            'cache' => App::$app->config['cache_path']
            'cache' => false,
            'debug' => true,
        ]);
        $this->environment->addExtension(new TwigFunctions());
        $this->environment->addFilter(TwigFunctions::filter());
        // TODO: remove debug extension on production
        $this->environment->addExtension(new DebugExtension());
    }

    public static function make(array $other_paths = []): static
    {
        return new static(App::$app->config['view_path'], App::$app->config['layout_path'], $other_paths);
    }


    /**
     * @throws ViewNotFoundException
     */
    public function renderView($view, array $params = []): string
    {
        if (!array_key_exists('app', $params)) {
            $params['app'] = App::$app; // Injecting app into views
        }

        try {
            return $this->environment->render("$view.twig", $params);
        } catch (LoaderError|SyntaxError|RuntimeError $e) {
            throw new ViewNotFoundException($e->getMessage());
        }
    }
}