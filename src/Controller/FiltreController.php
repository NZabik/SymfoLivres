<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Editeur;
use App\Entity\Genre;
use Doctrine\ORM\QueryBuilder;
use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FiltreController extends AbstractController
{
    #[Route('/filtre', name: 'app_filtre')]
    public function index(): Response
    {
        return $this->render('filtre/index.html.twig', [
            'controller_name' => 'FiltreController',
        ]);
    }
    public function filterBar()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('filterSearch'))
            ->add('auteur', EntityType::class, [
                'class' => Auteur::class,
                'label' => 'Auteurs',
                'placeholder' => '',
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'text-info'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-info'
                ]
            ])
            ->getForm();
        return $this->render('filtre/filterBar.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/filterSearch', name: 'filterSearch')]
    public function filterSearch(Request $request, LivreRepository $livre)
    {
        $query = $request->request->all('form')['auteur'];
        if ($query) {
            $livres = $livre->findArticlesByAuteur($query);
        }
        return $this->render('filtre/index.html.twig', [
            'livres' => $livres
        ]);
    }
    public function filterBar2()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('filterSearch2'))
            ->add('editeur', EntityType::class, [
                'class' => Editeur::class,
                'label' => 'Editeurs',
                'placeholder' => '',
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'text-warning'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-warning'
                ]
            ])
            ->getForm();
        return $this->render('filtre/filterBar2.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/filterSearch2', name: 'filterSearch2')]
    public function filterSearch2(Request $request, LivreRepository $livre)
    {
        $query = $request->request->all('form')['editeur'];
        if ($query) {
            $livres = $livre->findArticlesByEditeur($query);
        }
        return $this->render('filtre/index.html.twig', [
            'livres' => $livres
        ]);
    }
    public function filterBar3()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('filterSearch3'))
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'label' => 'Genres',
                'placeholder' => '',
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'text-danger'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-danger'
                ]
            ])
            ->getForm();
        return $this->render('filtre/filterBar3.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/filterSearch3', name: 'filterSearch3')]
    public function filterSearch3(Request $request, LivreRepository $livre)
    {
        $query = $request->request->all('form')['genre'];
        if ($query) {
            $livres = $livre->findArticlesByGenre($query);
        }
        return $this->render('filtre/index.html.twig', [
            'livres' => $livres
        ]);
    }
    public function filterBarAll()
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('filterSearchAll'))
            ->add('auteur', EntityType::class, [
                'class' => Auteur::class,
                'required' => false,
                'label' => 'Auteurs',
                'placeholder' => '',
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'text-info'
                ]
            ])
            ->add('editeur', EntityType::class, [
                'class' => Editeur::class,
                'required' => false,
                'label' => 'Editeurs',
                'placeholder' => '',
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'text-warning'
                ]
            ])
            ->add('genre', EntityType::class, [
                'class' => Genre::class,
                'required' => false,
                'label' => 'Genres',
                'placeholder' => '',
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'text-danger'
                ]
            ])
            ->add('recherche', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-outline-info'
                ]
            ])
            ->getForm();
        return $this->render('filtre/filterBarAll.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/filterSearchAll', name: 'filterSearchAll')]
    public function filterSearchAll(Request $request, LivreRepository $livre)
    {
        $query = $request->request->all('form')['auteur'];
        $query2 = $request->request->all('form')['editeur'];
        $query3 = $request->request->all('form')['genre'];
        if  (($query == "") && ($query2 == "") && ($query3 == "")){
            return $this->redirectToRoute( 'app_livre_index' )
        ;
        } else if ($query or $query2 or $query3) {
            $livres = $livre->findArticlesByAll($query , $query2 , $query3);
        }
        return $this->render('filtre/index.html.twig', [
            'livres' => $livres
        ]);
    }
}
