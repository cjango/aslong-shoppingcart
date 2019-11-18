<?php

namespace AsLong\Cart\Triats;

trait Cartable
{

    public function getBuyableIdentifier()
    {
        return $this->id;
    }

    public function getBuyablePrice()
    {
        return $this->price;
    }

    public function getSellerIdentifier()
    {
        return $this->seller_id;
    }

}
