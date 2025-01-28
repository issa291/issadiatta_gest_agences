<?php

namespace App\Controller;
namespace App\Controller;

use App\Entity\Agence;
use App\Form\AgenceType;
use App\Repository\AgenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AgenceController extends AbstractController
{
    #[Route('/agence/liste', name: 'agence_index', methods: ["GET", "POST"])]
    public function liste(Request $request,AgenceRepository $agenceRepository,  EntityManagerInterface $entityManager, PaginatorInterface $paginatorInterface  ): Response {
        // Récupérer les filtres
        $adresseFilter = $request->query->get('adresse', '');
        $telephoneFilter = $request->query->get('telephone', '');
    
        // Construire la requête avec filtres
        $queryBuilder = $entityManager->getRepository(Agence::class)->createQueryBuilder('a');
    
        if (!empty($adresseFilter)) {
            $queryBuilder->andWhere('a.adresse LIKE :adresse')
                         ->setParameter('adresse', '%' . $adresseFilter . '%');
        }
    
        if (!empty($telephoneFilter)) {
            $queryBuilder->andWhere('a.telephone LIKE :telephone')
                         ->setParameter('telephone', '%' . $telephoneFilter . '%');
        }
        $agencess = $agenceRepository->findAll();

        // Paginer les résultats
        $agences = $paginatorInterface->paginate(
            $agencess, // Données à paginer
            $request->query->getInt('page', 1), 
            5 // Nombre d'éléments par page
        );
       
    
        // Créer une nouvelle instance d'Agence pour le formulaire
        $agence = new Agence();
        $form = $this->createForm(AgenceType::class, $agence);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer l'agence en base de données
           

            $entityManager->persist($agence);
            $entityManager->flush();
    
            // Rediriger vers la liste des agences après l'ajout
            return $this->redirectToRoute('agence_index');
        }
    
        // Passer les agences et le formulaire à la vue
        return $this->render('agence/liste.html.twig', [
            'agences' => $agences,
            'form' => $form->createView(),
            'adresseFilter' => $adresseFilter,
            'telephoneFilter' => $telephoneFilter,
        ]);

        
    }
    #[Route('/agence/supprimer/{id}', name: 'agence_supprimer', methods: ["POST"])]
public function supprimer(Agence $agence, EntityManagerInterface $entityManager): Response
{
    $entityManager->remove($agence);
    $entityManager->flush();

    $this->addFlash('success', 'Agence supprimée avec succès.');
    return $this->redirectToRoute('agence_index');
}

    
}