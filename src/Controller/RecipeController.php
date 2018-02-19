<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Config\Definition\Exception\Exception;

use Aws\S3\S3Client;

use App\Entity\Recipe;

class RecipeController extends Controller
{
	// serializer for converting JSON to an entity
	private $serializer;

	public function __construct()
    {
        $encoders = array(new JsonEncoder());
		$normalizers = array(new ObjectNormalizer());
		$this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/api/recipes/all")
     * @Method("GET")
     */
    public function allRecipesAction() {
        $recipes = $this->getRepo()->findAll();

        $json = $this->serializer->serialize($recipes, 'json');
        return new Response($json);
    }

	/**
     * @Route("/api/recipes/my-recipes")
     * @Method("GET")
     */
    public function myRecipesAction() {
    	$recipes = $this->getRepo()->findBy(
			['userId' => $this->getUser()->getUserId()],
			['id' => 'DESC']
		);

		$json = $this->serializer->serialize($recipes, 'json');
    	return new Response($json);
    }

	/**
     * @Route("/api/recipes/all/search")
     * @Method("GET")
     */
    public function searchAllRecipesAction(Request $request) {
		$searchTerm = $request->query->get('searchTerm');
    	$recipes = $this->getRepo()->searchByTitle($searchTerm);

		$json = $this->serializer->serialize($recipes, 'json');
    	return new Response($json);
    }

    /**
     * @Route("/api/recipes/add")
     * @Method({"POST", "OPTIONS"})
     */
    public function addRecipeAction(Request $request) {
    	$db = $this->getDoctrine()->getManager();

    	$recipeJson = $request->getContent();
		$recipe = $this->serializer->deserialize($recipeJson, Recipe::class, 'json');
		$recipe->setUserId($this->getUser()->getUserId());

		$db->persist($recipe);
		$db->flush();

    	return new JsonResponse(array('status' => 'success', 'id' => $recipe->getId()));
    }

    /**
     * @Route("/api/recipes/{id}")
     * @Method({"PUT", "OPTIONS"})
     */
    public function updateRecipeAction($id, Request $request) {
	    $recipe = $this->getRepo()->find($id);

		if ($this->getUser()->getUserId() !== $recipe->getUserId()) {
			throw new AccessDeniedException('This recipe belongs to a different user');
		}

	    $recipeJson = $request->getContent();
		$updated = $this->serializer->deserialize($recipeJson, Recipe::class, 'json');
		$recipe->fromArray($updated->toArray());

		$entityManager = $this->getDoctrine()->getManager();
		$entityManager->persist($recipe);
		$entityManager->flush();

    	return new JsonResponse(array('status' => 'success'));
    }

    /**
     * @Route("/api/recipes/{id}/delete")
     * @Method({"DELETE", "OPTIONS"})
     */
    public function deleteRecipeAction($id) {
    	$entityManager = $this->getDoctrine()->getManager();

    	try {
    		$recipe = $this->getRepo()->find($id);
			if ($this->getUser()->getUserId() !== $recipe->getUserId()) {
				throw new AccessDeniedException('This recipe belongs to a different user');
			}

	    	$entityManager->remove($recipe);
			$entityManager->flush();
    	} catch (Exception $e) {
    		// TODO add logging
    		return new JsonResponse(array('status' => 'failed'));
    	}

    	return new JsonResponse(array('status' => 'success'));
    }

    /**
     * @Route("/api/recipes/{id}/image")
     * @Method({"POST", "OPTIONS"})
     */
    public function uploadImageAction($id, Request $request) {
		if ($this->getUser()->getUserId() !== $id) {
			throw new AccessDeniedException('This recipe belongs to a different user');
		}

        try {
            $file = $request->files->get('file');
        } catch ( Exception $e ) {
	       	return new JsonResponse([], 400);
        }

        // name the file with a unique id
        $imageId = md5(uniqid());
        $fileName = $imageId . '.' . $file->guessExtension();

        // upload the image to an Amazon S3 bucket
        $s3 = S3Client::factory(['version' => 'latest', 'region' => 'eu-west-2', 'signature' => 'v4']);
        $bucket = getenv('S3_BUCKET');
        $upload = $s3->upload(
	    	$bucket,
	    	$fileName,
        	fopen($file, 'rb'),
        	'public-read'
        );

        return new JsonResponse(array('imageUrl' => $upload->get('ObjectURL')));
    }

	// gets an instance of the recipe repository
	private function getRepo() {
		return $this->getDoctrine()->getRepository(Recipe::class);
	}
}
