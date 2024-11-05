<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

use DateTimeImmutable;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use App\Form\SearchType;
use App\Model\SearchData;

 

// #[Route('/')]

final class VideoController extends AbstractController
{
    
    // PAGE HOME
    #[Route(path:'/', name: 'app_home')]
    public function index(
        Request $request, 
        VideoRepository $repo,
        PaginatorInterface $paginator,
        TranslatorInterface $translator,
        EntityManagerInterface $em
        ): Response 

    {
        if ($this->getUser()){
            
            if (!$this->getUser()->isVerified()){
            $this->addFlash('info', $translator->trans('recipeController.Your email address is not verified. Please check your email box with instructions'));
            }


            // If logged in, show all videos (both premium and non-premium)
            $data = $repo->findBy([], ['createdAt' => 'DESC']);

        } else {
            // If not logged in, show only non-premium videos
            // $data = $repo->findBy(['premium' => false], ['createdAt' => 'DESC']);
            $data = $repo->findBy([], ['createdAt' => 'DESC']);
        }
        

        //PAGINATION

        // $data = $em->getRepository(Video::class)->findBy([], ['createdAt' => 'DESC']);

        $videos=$paginator->paginate(
            $data,
            $request->query->getInt('page',1),
            6
        );


        //SEARCH
        
        $search = false;
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //dd($searchData);
            $searchData->page = $request->query->getInt('page', 1);
            $nbVideosFind =  $paginator->paginate($repo->findBySearch($searchData));

            $videos = $paginator->paginate(
                $repo->findBySearch($searchData),
                $request->query->get('page', 1), 3
            );  

            $search = true;

            return $this->render('video/index.html.twig', [
                'form' => $form,
                'videos_all' => $videos,
                'search' => $search,
                'searchData' => $searchData->query,
                'nbVideosFind' => $nbVideosFind
            ]);
        }
        
        return $this->render('video/index.html.twig', [
            'form' => $form->createView(),
            'videos_all' => $videos,
            'search' => $search,
            'total_videos' => count($videos),  // Pass total number of videos
        ]);
    }

    // EMBED YOUTUBE VIDEOS
    public function convertYoutubeLink($videoLink):string
        {
            if (strpos($videoLink, 'watch?v=') !== false) {
                $videoLink = str_replace('watch?v=', 'embed/', $videoLink);
            }
            return $videoLink;
        }


    // PAGE VIDEO CREATE NEW
    #[Route(path:'video/create', name: 'app_video_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        if ($this->getUser()){
            if (!$this->getUser()->isVerified()){
                $this->addflash('error', 'You must to be registered or have confirmed your email in order to add a video');
                return $this->redirectToRoute('app_home'); 
            } 
        }else{
            $this->addFlash('error','You need to be logged in');
            return $this->redirectToRoute('app_login'); 
        }


        $video = new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);      

        if ($form->isSubmitted() && $form->isValid()) {

            // $video->setUser($this->getUser());

            // Convert the YouTube video link to embed format
            $embedLink = $this->convertYoutubeLink($video->getVideoLink());
            $video->setVideoLink($embedLink); // Save the embed version of the link
            $video->setUser($user); // Associate the video with the user

            $video->setCreatedAt(new DateTimeImmutable());
            $video->setUpdateAt(new DateTimeImmutable());
            $entityManager->persist($video);
            $entityManager->flush();
            $this->addFlash('success', 'La video' . $video->getTitle() . 'a été crée'); 
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/create.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }


    // PAGE VIDEO SHOW
    #[Route(path: 'video/{id}', name: 'app_video_show')]
    public function show(Video $video): Response
    {
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }


    
    // PAGE VIDEO EDIT
    #[Route('/{id}/edit', name: 'app_video_edit')]
    public function edit(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()){ 
            if (!$this->getUser()->isVerified()){
                $this->addflash('error', 'You must confirm your email in order to edit a video');
                return $this->redirectToRoute('app_home'); 
            
            }else if 
                ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
                    $this->addFlash('error', 'You do not own this video, thus you can not edit it');
                    return $this->redirectToRoute('app_home');
                }

        }else{
            $this->addFlash('error','You need to login in order to edit your own video');
            return $this->redirectToRoute('app_login'); 
        }


        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'La video' . $video->getTitle() . 'a été edité'); 
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('video/edit.html.twig', [
            'video' => $video,
            'form' => $form,
        ]);
    }


    
    // PAGE VIDEO DELETE
    #[Route('/{id}', name: 'app_video_delete', methods: ['POST'])]
    public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
    {

        if ($this->getUser()){ 
            if (!$this->getUser()->isVerified()){
                $this->addflash('error', 'You must confirm your email in order to delete own videos');
                return $this->redirectToRoute('app_home'); 
            
            }else if 
                ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
                    $this->addFlash('error', 'You do not own this video, thus you can not delete it');
                    return $this->redirectToRoute('app_home');
                }

        }else{
            $this->addFlash('error','You need to login in order to delete your own video');
            return $this->redirectToRoute('app_login'); 
        }

        // if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($video);
        //     $entityManager->flush();
        // }

            if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
                $entityManager->remove($video);
                $entityManager->flush();
                $this->addFlash('success', 'The video has been deleted.');
            } else {
                $this->addFlash('error', 'Invalid CSRF token.');
            }
        

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

}
