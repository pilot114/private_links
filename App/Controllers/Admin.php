<?php

namespace App\Controllers;

use App\Entities\Resource;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Admin extends BaseController
{
    public function view()
    {
        $resources = $this->db()->getRepository(':Resource')->findAll();

        $files = array_diff(scandir($this->config['root_dir'] . '/public/conf'), ['.', '..']);

        return $this->render('admin.twig', [
            'files' => $files,
            'resources' => $resources,
        ]);
    }

    public function postLink()
    {
        $links = $this->getParam('links');
        $name = $this->getParam('name');
        $description = $this->getParam('description');
        $sum = $this->getParam('sum');

        if (!$links || !$name || !$sum) {
            return new Response('Не все параметры заданы');
        }

        $generate = bin2hex(random_bytes(6));

        $res = new Resource();
        $res
            ->setLinks($links)
            ->setGenerate($generate)
            ->setName($name)
            ->setSum($sum)
            ->setDescription($description);

        $em = $this->db();
        $em->persist($res);
        $em->flush();

        return new RedirectResponse('/admin');
    }
}