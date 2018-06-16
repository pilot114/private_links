<?php

namespace App\Controllers;

use App\Entities\Resource;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Admin extends BaseController
{
    public function view()
    {
        $resources = $this->db()->getRepository(':Resource')->findAll();

        return $this->render('admin.twig', [
            'resources' => $resources
        ]);
    }

    public function postLink()
    {
        $link = $this->getParam('link');
        $name = $this->getParam('name');

        $generate = bin2hex(random_bytes(6));

        $res = new Resource();
        $res
            ->setLinks([$link])
            ->setGenerate($generate)
            ->setName($name);

        $em = $this->db();
        $em->persist($res);
        $em->flush();

//        $accessLink = $this->request->getBaseUrl() . '/access/1/' . $generate;

        return new RedirectResponse('/admin');
//        return $this->successResponse([
//            'link' => $accessLink
//        ]);
    }
}