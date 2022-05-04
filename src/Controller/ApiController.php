<?php

namespace App\Controller;

use App\Entity\Region;
use App\Entity\Departement;
use App\Repository\CommuneRepository;
use App\Repository\DepartementRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiController extends AbstractController
{
    #[Route('/api/regions', name: 'get_regions_api')]
    public function addRegionsByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
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

    #[Route('/api/departements', name: 'get_departements_api')]
    public function addDepartementsByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $departementJSON = file_get_contents("https://geo.api.gouv.fr/departements");
        $departementObject = $serializer->deserialize($departementJSON, 'App\Entity\Departement[]', 'json');

        foreach($departementObject as $departement){
            $em->persist($departement);
        }
        $em->flush();

        return new JsonResponse("success", Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/communes', name: 'get_communes_api')]
    public function addCommunesByApi(SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $communeJSON = file_get_contents("https://geo.api.gouv.fr/communes");
        $communeObject = $serializer->deserialize($communeJSON, 'App\Entity\Commune[]', 'json');

        foreach($communeObject as $commune){
            $em->persist($commune);
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

    #[Route('/api/show_departements', name: 'get_departements_api_BD')]
    public function showDepartement(SerializerInterface $serializer, DepartementRepository $departementRepository): Response
    {
        //Récupération des régions dans la BDD
        $departementObject = $departementRepository->findAll();
        //Serialize Object to JSON
        $departementJSON = $serializer->serialize($departementObject, "json");
        return new JsonResponse($departementJSON, Response::HTTP_OK, [], true);
    }

    #[Route('/api/show_communes', name: 'get_communes_api_BD')]
    public function showCommune(SerializerInterface $serializer, CommuneRepository $communeRepository): Response
    {
        //Récupération des régions dans la BDD
        $communeObject = $communeRepository->findAll();
        //Serialize Object to JSON
        $communeJSON = $serializer->serialize($communeObject, "json");
        return new JsonResponse($communeJSON, Response::HTTP_OK, [], true);
    }



    #[Route('/api/post_regions', name: 'post_regions_api_BD')]
    public function addRegion(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $em): Response
    {
        //Récupération du contenu du body
        $regionJSON = $request->getContent();
        $regionObject = $serializer->deserialize($regionJSON, Region::class, 'json');

        $errors = $validator->validate($regionObject);

        if(count($errors) > 0){
            $errorsString = (string) $errors;
            return new Response($errorsString);
        }

        $em->persist($regionObject);
        $em->flush();
        //dd($regionObject);
        return new Response('Région valide');
    }
}

