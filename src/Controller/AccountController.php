<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use DateTimeImmutable;
use App\Form\UserFormType;
use App\Repository\VideoRepository;
use App\Entity\Video;



class AccountController extends AbstractController
{
    #[Route('/account', name: "app_account")] 
    public function show(VideoRepository $video_repo):Response{

        if (!$this->getUser()){
            $this->addFlash('error','You need to login in order to access your account');
                return $this->redirectToRoute('app_login'); 
        }  
          // Fetch the content uploaded by the user
          $user = $this->getUser();
          $videos = $video_repo->findBy(['user' => $user], ['createdAt' => 'DESC']); // Adjust this if you are using a different entity
  
          return $this->render('account/show.html.twig', [
              'user' => $user,
              'videos_repo' => $videos, // Pass the recipes to the template
          ]); 
      }

    #[Route('/account/edit', name: "app_account_edit")]
    public function edit(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response{

        
            if (!$this->getUser()){
                $this->addFlash('error','You need to login in order to access your account');
                    return $this->redirectToRoute('app_login'); 
        
            }

            $user=$this->getUser();
            $form = $this->createForm(UserFormType::class, $user);
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){
                $user->setUpdatedAt(new DateTimeImmutable());
                $em->flush();
                $this->addFlash('success', $translator->trans('Account successfuly updated !'));
                return $this->redirectToRoute('app_account');
            }
            return $this->render('account/edit.html.twig', [
                            'user' => $user,
                            'monForm' => $form->createView()
            ]);
            
        
    }


}
