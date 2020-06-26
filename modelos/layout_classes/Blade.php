<?php

namespace Layout;

use eftec\bladeone\BladeOne;

class Blade
{
  public static function view(string $root, string $view, array $variables = [])
  {
    $blade = new BladeOne($root."view", $root.BLADE_CACHE, BLADE_OPTION);
    echo $blade->run($view, $variables);
  }
}
