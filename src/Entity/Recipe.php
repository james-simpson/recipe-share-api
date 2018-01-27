<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="text")
     */
    private $ingredients;

    /**
     * @ORM\Column(type="text")
     */
    private $method;

    /**
     * @ORM\Column(type="integer")
     */
    private $time;

    /**
     * @ORM\Column(type="integer")
     */
    private $difficulty;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vegetarian;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vegan;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sweet;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getIngredients()
    {
        return $this->ingredients;
    }

    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getDifficulty()
    {
        return $this->difficulty;
    }

    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
    }

    public function getVegetarian()
    {
        return $this->vegetarian;
    }

    public function setVegetarian($vegetarian)
    {
        $this->vegetarian = $vegetarian;
    }

    public function getVegan()
    {
        return $this->vegan;
    }

    public function setVegan($vegan)
    {
        $this->vegan = $vegan;
    }

    public function getSweet()
    {
        return $this->sweet;
    }

    public function setSweet($sweet)
    {
        $this->sweet = $sweet;
    }

    /**
	 * Represent as array
	 * @return array
	 */
	public function toArray(){
	    $properties = [];
	    foreach ($this as $name => $value) {
	        $properties[$name] = $value;
	    }

	    return $properties;
	}

	/**
	 * Create from array
	 * @param $array
	 */
	public function fromArray($array){
	    foreach ($array as $name => $value) {
	        if (isset($this->$name) && $name !== 'id'){
	            $this->$name = $value;
	        }
	    }
	}
}
