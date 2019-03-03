<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 02-Mar-19
 * Time: 5:25 PM
 */

namespace Bloomon\models;


class Bouquet
{

    /**
     * @var \Bloomon\interfaces\Flowers
     */
    private $Flowers;


    /**
     * @var array Holds all bouquets
     */
    private $boquet = array();



    public function __construct(\Bloomon\interfaces\Flowers $flowers)
    {
        $this->Flowers = $flowers;
    }


    /**
     * Parses bouquet specs and adds bouquet
     *
     * Sample input : AL10a15b5c30
     * @param $bouqetSpec string
     * @uses Bouquet::$boquet to set bouquet details
     * @uses Bouquet::addFlowers() to parse flowers
     * @return void
     *
     */
    public function addBouquet($bouqetSpec)
    {
        preg_match_all('/^([A-Z])([S|L])(([0-9]+[a-z])+)([0-9]+)$/',$bouqetSpec,$bouquetData);
        $this->boquet[]["name"] = $bouquetData[1][0];
        end($this->boquet);
        $index = key($this->boquet);
        $this->boquet[$index]["size"] = $bouquetData[2][0];
        $this->boquet[$index]["total"] = end($bouquetData)[0];
        $this->boquet[$index]["flowerCount"] = 0;

        // Parse flowers
        $this->addFlowers($bouquetData[3][0]);
    }


    /**
     * Parses flower string from bouquet specs and adds flowers in the bouquet
     *
     * sample input : 11a12b2c
     * @param $flowers string
     * @uses Bouquet::$boquet to get current index
     * @uses Bouquet::addSingleFlower() to add flowers to bouquet
     * @return void
     */
    private function addFlowers($flowers)
    {

        preg_match_all('/([0-9]+)+([a-z])/',$flowers,$specieQtyArr);
        $bouquetIndex = key($this->boquet);
        $this->boquet[$bouquetIndex]["flowers"]=array();
        foreach($specieQtyArr[0] as $index=>$specieQty)
        {
            preg_match('/^([0-9]+)([a-z])$/',$specieQty,$qtyArr);
            $this->addSingleFlower($bouquetIndex,$qtyArr[2],$qtyArr[1]);
        }


    }


    /**
     * @param $index  int index of Bouquet
     * @param $specie string specie of flower
     * @param $qty int quantity of specie in bouquet
     * @return void
     * @uses Bouquet::$boquet to store flowers in bouquet
     */
    private function addSingleFlower($index,$specie,$qty)
    {

        // check if mode is for updating or adding a new flower.
        $updated = false;


        if(count($this->boquet[$index]["flowers"]) > 0)
        {
            foreach($this->boquet[$index]["flowers"] as $key=>$flower)
            {
                if($flower["specie"] == $specie)
                {
                    //update the flower
                    $this->boquet[$index]["flowers"][$key]["qty"] += $qty;
                    $this->boquet[$index]["flowerCount"] += $qty;
                    $updated = true;

                }
            }
        }

        // if nothing was update, add item as new flower
        if($updated == false) {
            $this->boquet[$index]["flowers"][]["specie"] = $specie;
            end($this->boquet[$index]["flowers"]);
            $flowerIndex = key($this->boquet[$index]["flowers"]);
            $this->boquet[$index]["flowers"][$flowerIndex]["qty"] = $qty;
            $this->boquet[$index]["flowerCount"] += $qty;
        }

    }


    /**
     * Compares species between bouquet and available flowers and returns whatever is not used along available qty
     * @param $index int Bouquet index
     * @uses Bouquet::$boquet to read flowers and size from existing bouquet
     * @uses Flowers::getSpecies() to get available flowers for existing size
     * @return array of unused species
     */
    private function getUnusedFlowers($index)
    {
        // get species by size
        $availableFlowers = $this->Flowers->getSpecies($this->boquet[$index]['size']);
        $usedFlowers = array();
        foreach($this->boquet[$index]["flowers"] as $flower)
        {
           $usedFlowers[$flower['specie']] = $flower['qty'] ;
        }
        $unUsedFlowers = array_diff_key($availableFlowers,$usedFlowers);
        return $unUsedFlowers;

    }


    /**
     * Reads Bouquet and generates array of bouquet specs
     * @return array of bouquet specs which can be created
     * @uses Bouquet::$boquet
     */
    public function getResults()
    {
        $bouquets = array();
        $i = 0;
        foreach($this->boquet as $bouquet)
        {

            $bouquets[$i] = $bouquet["name"].$bouquet["size"];
            foreach($bouquet["flowers"] as $flower)
            {
                $bouquets[$i] .= $flower["qty"].$flower["specie"];
            }
            $bouquets[$i] .= $bouquet["total"];
            $i++;
        }
        return $bouquets;
    }


    /**
     * Reads flowers and qty from bouquet and compares if bouquet is possible, if possible it updates the available
     * stock and returns true else returns false
     * @param $index int Bouquet index
     * @return bool
     * @uses Bouquet::$boquet to read flowers
     * @uses Flowers::getSpecies() to read available flowers
     * @uses Flowers::minusStock() to update stock
     */
    private function isBoquetPossible($index)
    {

        $requiredFlowers = $this->boquet[$index]["flowers"];
        $availableFlowers = $this->Flowers->getSpecies($this->boquet[$index]["size"]);
        foreach($requiredFlowers as $flower)
        {
            if(!key_exists($flower['specie'],$availableFlowers) || $flower['qty'] > $availableFlowers[$flower['specie']])
            {
                // return if boquet is not possible
               return false;
            }
        }
        foreach($requiredFlowers as $flower)
        {
            // update stock for all species in bouquet
            $this->Flowers->minusStock($this->boquet[$index]["size"],$flower['specie'],$flower['qty']);
        }
        return true;

    }


    /**
     * Checks if boquet has space, if it has adds random flowers to fill
     * @param $index Bouquet index
     * @return void
     * @uses Bouquet::$boquet to read boquet size and flower size
     * @uses Bouquet::addSingleFlower() to add extra flowers
     * @uses Flowers::minusStock() to update stock
     * @todo Implement getUnusedFlowers to prioritize usage of unused flowers to make it more colorful
     */
    private function addRandomFlowers($index)
    {
        $size = $this->boquet[$index]['size'];

        while(($this->boquet[$index]['total'] - $this->boquet[$index]['flowerCount']) != 0)
        {
            // get random specie from available species
            $specie = array_rand($this->Flowers->getSpecies($size),1);
            $specie = $specie[0];

            // add the random specie to bouquet
            $this->addSingleFlower($index,$specie,1);
            // update stock for added specie
            $this->Flowers->minusStock($size,$specie,1);
        }


    }

    /**
     * Main function for processing bouquets, reads all bouquets
     * checks if bouquet is possible and adds random flowers
     * @return void
     * @uses Bouquet::$boquet to read all bouquets
     * @uses Bouquet::isBoquetPossible() to check if bouquet is possible
     * @uses Bouquet::addRandomFlowers() to add random flowers
     */
    public function processBouquets()
    {
        // check if bouquet is possible, if possible update stock, if not remove bouquet
        foreach($this->boquet as $index=>$bouquet)
        {
            if(!$this->isBoquetPossible($index))
                unset($this->boquet[$index]);
        }

        // add Random flowers to bouquet with extra space

        foreach($this->boquet as $index=>$bouquet)
        {
           $this->addRandomFlowers($index);
        }

    }


    /**
     * Returns the current bouquet
     * @uses Bouquet::$boquet to return all bouquets
     * @return array
     */
    public function getBouquets()
    {
        return $this->boquet;
    }







}