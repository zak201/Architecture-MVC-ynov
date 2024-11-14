<?php

namespace App\Controller;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Pokemon;
use App\Form\PokemonType;
use App\Repository\PokemonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PokemonController extends AbstractController
{
    #[Route('/pokemons', name: 'pokemon_list')]
    public function index(Request $request, PokemonRepository $pokemonRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $pokemonRepository->createQueryBuilder('p');
        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1), // Page par défaut
            10 // Limite de résultats par page
        );

        return $this->render('pokemon/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/pokemon/new', name: 'pokemon_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pokemon);
            $entityManager->flush();

            return $this->redirectToRoute('pokemon_list');
        }

        return $this->render('pokemon/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/pokemon/{id}', name: 'pokemon_show')]
    public function show(int $id, PokemonRepository $pokemonRepository): Response
    {
        $pokemon = $pokemonRepository->find($id);

        if (!$pokemon) {
            throw $this->createNotFoundException('Le Pokémon n’existe pas.');
        }

        return $this->render('pokemon/show.html.twig', [
            'pokemon' => $pokemon,
        ]);
    }

    #[Route('/pokemon/{id}/edit', name: 'pokemon_edit')]
    public function edit(int $id, Request $request, PokemonRepository $pokemonRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokemonRepository->find($id);

        if (!$pokemon) {
            throw $this->createNotFoundException('Le Pokémon n’existe pas.');
        }

        $form = $this->createForm(PokemonType::class, $pokemon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('pokemon_list');
        }

        return $this->render('pokemon/edit.html.twig', [
            'form' => $form->createView(),
            'pokemon' => $pokemon,
        ]);
    }

    #[Route('/pokemon/{id}/delete', name: 'pokemon_delete', methods: ['POST'])]
    public function delete(int $id, PokemonRepository $pokemonRepository, EntityManagerInterface $entityManager): Response
    {
        $pokemon = $pokemonRepository->find($id);

        if (!$pokemon) {
            throw $this->createNotFoundException('Le Pokémon n’existe pas.');
        }

        $entityManager->remove($pokemon);
        $entityManager->flush();

        return $this->redirectToRoute('pokemon_list');
    }
    #[Route('/pokemon/', name: 'pokemon_redirect')]
    public function redirectToPokemonList(): Response
    {
        return $this->redirectToRoute('pokemon_list');
    }
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->redirectToRoute('pokemon_list');
    }

}
