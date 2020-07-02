<?php

namespace Prodavay\GraylogTestBundle\Controller;

use Prodavay\GraylogTestBundle\Service\LogService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProdavayGraylogTestController extends AbstractController
{
    /**
     * @var LogService
     */
    protected $service;

    public function __construct(LogService $service)
    {
        $this->service = $service;
    }
}