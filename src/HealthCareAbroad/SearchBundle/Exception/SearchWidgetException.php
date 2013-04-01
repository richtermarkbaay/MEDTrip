<?php

namespace HealthCareAbroad\SearchBundle\Exception;

class SearchWidgetException extends \Exception
{
    static public function unknownContext($context)
    {
        return new self("Unknown search widget context: {$context}");
    }
}