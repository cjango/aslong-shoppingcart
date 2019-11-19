<?php

namespace AsLong\Cart\Triats;

trait Cartable
{

    /**
     * Notes: 获取商品ID
     * @Author: <C.Jason>
     * @Date: 2019/11/19 10:45 上午
     * @param null $options
     * @return mixed
     */
    public function getBuyableIdentifier($options = null)
    {
        return $this->id;
    }

    /**
     * Notes: 获取商品价格
     * @Author: <C.Jason>
     * @Date: 2019/11/19 10:45 上午
     * @param null $options
     * @return mixed
     */
    public function getBuyablePrice($options = null)
    {
        return $this->price;
    }

    /**
     * Notes: 获取销售商户
     * @Author: <C.Jason>
     * @Date: 2019/11/19 10:45 上午
     * @return mixed
     */
    public function getSellerIdentifier()
    {
        return $this->seller_id;
    }

}
