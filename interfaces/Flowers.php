<?php
/**
 * Created by PhpStorm.
 * User: Arshad
 * Date: 02-Mar-19
 * Time: 5:30 PM
 */

namespace Bloomon\interfaces;


interface Flowers
{
    public function addFlower($sizeSpecie);
    public function minusStock($size,$specie,$stock);
    public function getSpecies($size);

}