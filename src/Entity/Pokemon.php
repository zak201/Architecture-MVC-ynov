<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "pokemon")]
#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?int $level = null;
    #[ORM\Column]
    private ?int $experience = null;

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): void
    {
        $this->level = $level;
    }

    public function getExperience(): ?int
    {
        return $this->experience;
    }

    public function setExperience(?int $experience): void
    {
        $this->experience = $experience;
    }


    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    #[Route('/pokemon/new', name: 'pokemon_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pokemon = new Pokemon();
        $form = $this->createForm(PokemonType::class, $pokemon);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pokemon);
            $entityManager->flush();

            return $this->redirectToRoute('pokemon_list');
        }

        return $this->render('pokemon/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/pokemon/edit/{id}', name: 'pokemon_edit')]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, PokemonRepository $pokemonRepository): Response
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
        ]);
    }
    #[Route('/pokemon/delete/{id}', name: 'pokemon_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager, PokemonRepository $pokemonRepository): Response
    {
        $pokemon = $pokemonRepository->find($id);
        if (!$pokemon) {
            throw $this->createNotFoundException('Le Pokémon n’existe pas.');
        }

        $entityManager->remove($pokemon);
        $entityManager->flush();

        return $this->redirectToRoute('pokemon_list');
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

}
