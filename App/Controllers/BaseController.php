<?php

namespace App\Controllers;


use Doctrine\ORM\Mapping\Driver\YamlDriver;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class BaseController
{
    /**
     * @var Request
     */
    protected $request;
    public $config;

    public $user;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->config = require __DIR__ . '/../../config/main.php';
    }

    public function successResponse($data)
    {
        return new JsonResponse(['result' => $data, 'error' => null]);
    }

    public function errorResponse($errMessage)
    {
        return new JsonResponse(['result' => null, 'error' => $errMessage]);
    }

    public function getParam($key)
    {
        return $this->request->request->get($key);
    }

    public function startSession()
    {
        $session = new Session();
        $session->start();
        $this->user = $session;
    }

    /**
     * @throws ORMException
     */
    public function db()
    {
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'path' => $this->config['root_dir'] . '/db.sqlite',
        ];

        $config = Setup::createConfiguration(true);
        $config->setMetadataDriverImpl(new YamlDriver($this->config['root_dir'] . '/config/entities', '.yml'));
        $config->addEntityNamespace('', 'App\Entities');

        return EntityManager::create($dbParams, $config);
    }

    /**
     * @param $receiver
     * @param $message
     * @return int
     */
    public function mail($receiver, $message)
    {
        $transport = (new \Swift_SmtpTransport('smtp.example.org', 25))
            ->setUsername('test')
            ->setPassword('test')
        ;
//        $transport = new Swift_SendmailTransport('/usr/sbin/sendmail -bs');

        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message('Wonderful Subject'))
            ->setFrom(['pilot114@bk.ru' => 'Admin'])
            ->setTo([ $receiver ])
            ->setBody($message)
        ;

        return $mailer->send($message);
    }

    /**
     * @param $templateName
     * @param $data
     * @return Response
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($templateName, $data)
    {
        $loader = new \Twig_Loader_Filesystem($this->config['root_dir'] . '/views');
        $twig = new \Twig_Environment($loader, [
            'cache' => $this->config['root_dir'] . '/cache',
            'debug' => true
        ]);
        $twig->addExtension(new \Twig_Extension_Debug());

        return new Response($twig->render($templateName, $data));
    }

}