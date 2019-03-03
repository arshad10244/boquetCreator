<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 02-Mar-19
 * Time: 5:22 PM
 */

namespace Bloomon\models;


class Flowers implements \Bloomon\interfaces\Flowers
{

    /**
     * Hold all flower species and stocks
     * @var array
     */
    private $flowers = array();


    /**
     * Returns associative array of specie and stock
     * @param $size string
     * @return mixed array
     * @uses Flowers::$flowers to store flowers.
     * @return void
     */
    public function getSpecies($size)
    {
        return $this->flowers[$size];
    }


    /**
     * Reduces stock by number provided in stock
     * @param $size string
     * @param $specie string
     * @param $stock int
     * @uses Flowers::$flowers
     * @return void
     * @throws \InvalidArgumentException
     */
    public function minusStock($size,$specie,$stock)
    {
        if(!isset($this->flowers[$size][$specie]))
           throw new \InvalidArgumentException("Specie does not exist in size");
        else
            $this->flowers[$size][$specie] -= $stock;

        if($this->flowers[$size][$specie] <= 0)
        {
            unset($this->flowers[$size][$specie]);
        }
    }


    /**
     * Adds new flowers provided size and specie in following spec:
     *
     * Example: sL ( s = specie, L = size)
     * @param $sizeSpecie string
     * @return void
     */
    public function addFlower($sizeSpecie)
    {
        // cleanup the flower before extracting size and specie
        $flower = preg_replace( "/[^A-Za-z?!]/", "", $sizeSpecie );

        //extracting size and specie
        $size = substr($flower,-1);
        $specie = substr($flower,0,1);

        if(!isset($this->flowers[$size][$specie]))
            $this->flowers[$size][$specie] = 1;
        else
            $this->flowers[$size][$specie]  += 1;

    }


    /**
     * Returns entire Flower array
     * @uses Flowers::$flowers
     * @return array
     */
    public function getFlowers()
    {
        return $this->flowers;
    }


}