<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Form\TweetType;
use App\Repository\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TweetController extends AbstractController
{
    #[Route('/tweet', name: 'app_tweets', methods: ['GET', 'POST'])]
    public function findAll(TweetRepository $repository,Request $request, EntityManagerInterface $manager): Response
    {
        $tweet = new Tweet();
        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $tweet = $form->getData();
            $manager->persist($tweet);
            $manager->flush();
            $this->addFlash(
                'success',
                'Tweet bien crée!'
            );
            return $this->redirectToRoute('app_tweets');
        }

        $tweets = $repository->findAll();
        return $this->render('tweet/index.html.twig', [
            'tweets' => $tweets,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tweet/find/{id}', name: 'app_tweet')]
    public function findOne(TweetRepository $repository, $id): Response
    {
        $tweets = $repository->findAll();
        $tweet = $repository->find(count($tweets));
        return $this->render('tweet/singleTweet.html.twig', ['tweet' => $tweet]);
    }

    #[Route('/tweet/new', name: 'tweet.new', methods: ['GET', 'POST'])]
    public function new(TweetRepository $repository,Request $request, EntityManagerInterface $manager): Response
    {
        $tweet = new Tweet();
        $form = $this->createForm(TweetType::class, $tweet);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $tweet = $form->getData();
            $manager->persist($tweet);
            $manager->flush();
            $this->addFlash(
                'success',
                'Tweet bien crée!'
            );
            return $this->redirectToRoute('tweet.new');
        }
        $tweets = $repository->findAll();
        $lastTweet = $repository->find($tweets[count($tweets) - 1]->getId());
        return $this->render('tweet/new.html.twig', [
            'lastTweet' => $lastTweet,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tweet/edit/{id}',name: 'tweet.edit', methods: ['GET', 'POST'])]
    public function edit(Tweet $tweet, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $tweet = $form->getData();
            $manager->persist($tweet);
            $manager->flush();
            $this->addFlash(
                'success',
                'Tweet bien modifié!'
            );
            return $this->redirectToRoute('app_tweets');
        }
        return $this->render('tweet/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/tweet/delete/{id}',name: 'tweet.delete', methods: ['GET'])]
    public function delete(EntityManagerInterface $manager, Tweet $tweet) : Response
    {
        $manager->remove($tweet);
        $manager->flush();
        return $this->redirectToRoute('app_tweets');
    }

/*    #[Route('/test',name: 'test', methods: ['GET'])]
    public function test()
    {
        dd("bonjour");
    }*/
}
