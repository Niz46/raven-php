<?php
declare(strict_types=1);

namespace Src\Controller;

class PageController
{
    public function home(): void
    {
        // base.php will include the default home page (no $viewFile set)
        include __DIR__ . '/../views/base.php';
    }

    public function demoLogin(): void
    {
        // tell base.php which view to include
        $viewFile = __DIR__ . '/../views/pages/login.php';
        include __DIR__ . '/../views/base.php';
    }
}
