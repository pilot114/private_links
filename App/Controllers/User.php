<?php

namespace App\Controllers;

use App\Entities\Access;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class User extends BaseController
{
    public function stepOne($data)
    {
        $generate = $data['generate'];
        $em = $this->db();

        if ($this->user) {
            return new RedirectResponse($this->request->getBaseUrl() . '/access/stream');
        }

        // платеж успешен, отправляем письмо на почту и редиректим на ввод пароля
        if ($this->request->isMethod('post')) {

            $ip = $this->request->getClientIp();
            $password = bin2hex(random_bytes(4));

            $repo = $em->getRepository(':Access');
            $access = $repo->findOneBy(['ip' => $ip]);

            $access->setPassword($password);
            $em->persist($access);
            $em->flush();

            $this->mail('pilot114@bk.ru', sprintf("IP: %s password: %s\n", $ip, $password));

            return new RedirectResponse($this->request->getBaseUrl() . '/access/2');
        }

        $repo = $em->getRepository(':Resource');
        $res = $repo->findOneBy(['generate' => $generate]);

        if (!$res) {
            return $this->errorResponse('Запрашиваемый ресурс не существует');
        }

        $ip = $this->request->getClientIp();

        $repo = $em->getRepository(':Access');
        $access = $repo->findOneBy(['ip' => $ip]);

        if ($access){
            if ($access->getPassword()) {
                return new RedirectResponse($this->request->getBaseUrl() . '/access/2');
            }
        } else {
            $access = (new Access())
                ->setIp($ip)
                ->setResource($res);

            $em->persist($access);
            $em->flush();
        }

        // на этом этапе у нас есть access, пока без пароля
        return $this->render('payment.twig', [
            'ip' => $ip,
            'links' => $res->getLinks(),
        ]);
    }

    /**
     * Если залогинен - отправляем на ресурс, иначе - вводить пароль и логиниться
     */
    public function stepTwo()
    {
        if ($this->user) {
            return new RedirectResponse($this->request->getBaseUrl() . '/stream');
        }
        $em = $this->db();

        // если пароль введён
        if ($this->request->isMethod('post')) {
            $repo = $em->getRepository(':Access');
            $access = $repo->findOneBy(['ip' => $this->request->getClientIp()]);

            $password = $this->getParam('password');
            if ($password == $access->getPassword()) {
                // логинимся
                $this->startSession();
                $this->user->set('links', $access->getResource()->getLinks());
                return new RedirectResponse($this->request->getBaseUrl() . '/stream');
            } else {
                $this->errorResponse('Неверный код доступа');
            }
        }

        return $this->render('password.twig', [
            'ip' => $this->request->getClientIp()
        ]);
    }

    // просмотр видео по ссылкам из сессии
    public function stream()
    {
//        $this->startSession();
//        $links = $this->user->get('links');
//        if (!$links) {
//            return new RedirectResponse($this->request->getBaseUrl() . '/access/2');
//        }

        $resources = $this->db()->getRepository(':Resource')->findAll();
        $links = [];
        foreach ($resources as $resource) {
            foreach ($resource->getLinks() as $link) {
                $links[] = $link;
            }
        }

        return $this->render('player.twig', [
            'links' => $links
        ]);
    }
}