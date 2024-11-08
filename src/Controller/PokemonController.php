<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokemonController extends AbstractController
{
    #[Route('/pokemons', name: 'pokemon_list')]
    public function index(PokemonRepository $pokemonRepository): Response
    {
        $pokemons = $pokemonRepository->findAll();

        return $this->render('pokemon/index.html.twig', [
            'pokemons' => $pokemons,
        ]);
    }
}

