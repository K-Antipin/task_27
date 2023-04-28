<?php

namespace App\core;

class View
{
    public function render($content_view, $template_view = null, $payload = null)
    {
        if ($template_view) {
            include_once LAYOUT . $template_view;
        }
    }

}