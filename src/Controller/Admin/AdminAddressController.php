<?php

namespace App\Controller\Admin;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/address')]
class AdminAddressController extends AbstractController
{
    // 1️⃣ Liste des adresses
    #[Route('/', name: 'admin_address_list')]
    public function list(AddressRepository $repo): Response
    {
        $addresses = $repo->findAll();

        return $this->render('admin/address/list.html.twig', [
            'addresses' => $addresses
        ]);
    }

    // 2️⃣ Ajouter une nouvelle adresse
    #[Route('/new', name: 'admin_address_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($address);
            $em->flush();

            $this->addFlash('success', 'Adresse ajoutée avec succès.');
            return $this->redirectToRoute('admin_address_list');
        }

        return $this->render('admin/address/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // 3️⃣ Éditer une adresse existante
    #[Route('/edit/{id}', name: 'admin_address_edit')]
    public function edit(Address $address, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush(); // l'entité est déjà gérée par Doctrine
            $this->addFlash('success', 'Adresse modifiée avec succès.');
            return $this->redirectToRoute('admin_address_list');
        }

        return $this->render('admin/address/edit.html.twig', [
            'form' => $form->createView(),
            'address' => $address,
        ]);
    }

    // 4️⃣ Supprimer une adresse
    #[Route('/delete/{id}', name: 'admin_address_delete')]
    public function delete(Address $address, EntityManagerInterface $em): Response
    {
        $em->remove($address);
        $em->flush();

        $this->addFlash('success', 'Adresse supprimée avec succès.');
        return $this->redirectToRoute('admin_address_list');
    }
}
