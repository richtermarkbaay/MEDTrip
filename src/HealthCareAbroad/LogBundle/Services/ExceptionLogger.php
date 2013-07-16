<?php
namespace HealthCareAbroad\LogBundle\Services;

interface ExceptionLogger
{
    function logException(\Exception $e);
}