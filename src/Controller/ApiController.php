<?php

namespace App\Controller;

use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiController extends AbstractController
{
    #[Route('/api/regions', name: 'get_regions_api')]
    public function addRegionByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        //Récupération des régions en JSON
        $regionJSON = file_get_contents("https://geo.api.gouv.fr/regions");

        //Méthode 1
        //$regionArr = $serializer->decode($regionJSON, "json");
        //$regionObject = $serializer->denormalize($regionArr, 'App\Entity\Region');

        //Méthode 2 -> passe en objet directement
        $regionObject = $serializer->deserialize($regionJSON, 'App\Entity\Region[]', 'json');
        //$em = $this->getDoctrine()->getManager();
        //dd($regionObject);


        foreach($regionObject as $region){
            $em->persist($region);
        }
        $em->flush();

        return new JsonResponse("success", Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/show_regions', name: 'get_regions_api_BD')]
    public function showRegion(SerializerInterface $serializer, RegionRepository $regionRepository): Response
    {
        //Récupération des régions dans la BDD
        $regionObject = $regionRepository->findAll();

        //Serialize Object to JSON
        $regionJSON = $serializer->serialize($regionObject, "json");

        return new JsonResponse($regionJSON, Response::HTTP_OK, [], true);
    }

    #[Route('/api/post_regions', name: 'post_regions_api_BD')]
    public function addRegion(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        //Récupération du contenu du body
        $regionJSON = $request->getContent();
        dd($regionJSON);
        
    }
}

