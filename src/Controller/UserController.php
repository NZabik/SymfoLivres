<?php

namespace App\Controller;

use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
    #[Route('/utilisateur/edition/{id}', name: 'app_user_edit', methods: ['GET', 'POST'])]
    /**
     * Allow to update the name and the pseudo of the user
     *
     * @param UserRepository $userRepository
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(UserRepository $userRepository, int $id, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher, SluggerInterface $slugger): Response
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        //Vérification si le user est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        //Vérification si le user connecté est le même que nous avons récupéré
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('home.index');
        }
        //Création du formulaire
        $form = $this->createForm(UserType::class, $user);
       
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            
            
            
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
                $user = $form->getData();
                $brochureFile = $form->get('profil')->getData();
                if ($brochureFile) {
                    $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
    
                    // Move the file to the directory where brochures are stored
                    try {
                        $brochureFile->move(
                            $this->getParameter('profil_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
    
                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents
                    $ancien = $user->getProfil();
                    $fileSystem = new Filesystem();
                    $fileSystem->remove('../public/uploads/profil/'.$ancien);
                    
                    $user->setProfil($newFilename);
                }
                
                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées'
                );
                return $this->redirectToRoute('home.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe est incorrect'
                );
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(UserRepository $userRepository, int $id, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response
    {
        //Récupération du user par son id
        $user = $userRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(UserPasswordType::class, $user);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
                $user->setPlainPassword($form->getData()->getNewPassword());
                $user -> setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->getData()->getNewPassword()
                    )
                );
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Le mot de passe a été modifié'
                );
                return $this->redirectToRoute('home.index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect'
                );
            }
        }
        return $this->render('user/edit_Password.html.twig', ['form' => $form->createView()]);
    }









    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
