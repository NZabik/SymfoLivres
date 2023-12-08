<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/livre')]
class LivreController extends AbstractController
{
    #[Route('/loue', name: 'app_livre_loue', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function loue(): Response
    {
        $user = $this->getUser();
        assert($user instanceof User);
        return $this->render('livre/loue.html.twig', [
            'livres' => $user->getLocation(),
        ]);
    }
    

    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $brochureFile = $form->get('couverture')->getData();


            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('couvertures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $livre->setCouverture($newFilename);
            }

            // ... persist the $product variable or any other work

            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('couverture')->getData();
            // if ($brochureFile) {
            //     $brochureFileName = $fileUploader->upload($brochureFile);
            //     $livre->setCouverture($brochureFileName);
            // }

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('couvertures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $livre->setCouverture($newFilename);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/location', name: 'app_livre_location', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function location(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('location' . $livre->getId(), $request->request->get('_token'))) {
            // date_default_timezone_set('Europe/Paris');
            // $date = new \DateTime;
            // $livre->setUser($this->getUser());
            // $livre->setDateLocation($date);
            $quantite = $livre->getQuantite();
            if ($quantite > 0) {
                $livre->addStock($this->getUser())
                      ->setQuantite($quantite - 1);
                $entityManager->flush();
            } else {
                $livre->setQuantite($quantite = 0);
                $entityManager->flush();
            }

            
        } return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/rendre', name: 'app_livre_rendre', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function rendre(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('rendre' . $livre->getId(), $request->request->get('_token'))) {
            // $livre->setUser(null);
            // $livre->setDateLocation(null);
            $quantite = $livre->getQuantite();
            $livre ->removeStock($this->getUser())
                   ->setQuantite($quantite + 1);
            $entityManager->flush();
            
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
