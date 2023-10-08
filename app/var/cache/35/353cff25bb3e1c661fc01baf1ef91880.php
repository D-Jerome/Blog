<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* ./layouts/default.html.twig */
class __TwigTemplate_4f22ff9f490e98169a148f8a02d4b35f extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'content' => [$this, 'block_content'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"fr\" class=\"h-100\">

<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <link rel=\"stylesheet\" href=\"https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css\" integrity=\"sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T\" crossorigin=\"anonymous\">
    <title><?= \$title ?? 'Mon Projet 5' ?></title>
</head>

<body class=\"d-flex flex-column h-100\">
    <nav class=\"navbar navbar-expand-lg navbar-dark bg-primary\">
        <a href=\"#\" class=\"navbar-brand\">Mon Projet 5</a>
    </nav>

    <div class=\"container mt-4\">

        ";
        // line 18
        $this->displayBlock('content', $context, $blocks);
        // line 19
        echo "
    </div>
    <footer class=\"bg-light py-4 footer mt-auto\">
        <div class=\"container\">
            
        </div>
        
    </footer>
</body>

</html>";
    }

    // line 18
    public function block_content($context, array $blocks = [])
    {
        $macros = $this->macros;
    }

    public function getTemplateName()
    {
        return "./layouts/default.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  73 => 18,  59 => 19,  57 => 18,  38 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "./layouts/default.html.twig", "/var/www/html/blog-project/app/templates/layouts/default.html.twig");
    }
}
