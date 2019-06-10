<?php

/**  */
namespace Test\lib;

/**
 * Class Render
 */
class Render extends ContainerAware
{
    /**
     * @param string     $template
     * @param array|null $vars
     *
     * @return string
     */
    public function render($template, $vars=null)
    {
        $file = sprintf(
            '%s/Resource/view/%s',
            $this->get('app')->getRootDir(),
            $template
        );
        if (is_array($vars) && !empty($vars)) {
            extract($vars);
        }

        ob_start();

        include $file;

        return ob_get_clean();
    }
}
