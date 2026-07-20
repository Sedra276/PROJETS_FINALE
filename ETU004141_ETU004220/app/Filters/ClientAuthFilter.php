<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ClientAuthFilter implements FilterInterface
{
    public function before(RequestInterface $requete, $arguments = null)
    {
        if (session()->get('client_id') === null) {
            return redirect()->to('/client/connexion');
        }
    }

    public function after(RequestInterface $requete, ResponseInterface $reponse, $arguments = null)
    {
    }
}