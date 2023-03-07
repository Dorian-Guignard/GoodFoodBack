<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as RestController;


/**
 * @Route("/api")
 * @RestController()
 */
class UserConnectoController extends AbstractController
{
    /**
     * @Route("/api/users/connect", name="api_user_connect")
     */
    public function getUser()
    {
        $user = $this->getUser(); // récupère l'utilisateur connecté

        // si l'utilisateur est connecté, retourne les informations de l'utilisateur
        if ($user) {
            return new JsonResponse([
                'email' => $user->getUserIdentifier(),
                // autres informations de l'utilisateur
            ]);
        } else {
            // sinon, retourne une erreur 401 Unauthorized
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }
    }
}
