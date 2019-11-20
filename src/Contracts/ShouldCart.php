<?php

namespace AsLong\Cart\Contracts;

interface ShouldCart
{

    /**
     * Notes: 获取商品ID
     * @Author: <C.Jason>
     * @Date: 2019/11/18 11:14 上午
     * @param null $options
     * @return mixed
     */
    public function getCartableIdentifier($options = null);

    /**
     * Notes: 获取商品价格
     * @Author: <C.Jason>
     * @Date: 2019/11/18 11:14 上午
     * @param null $options
     * @return mixed
     */
    public function getCartablePrice($options = null);

    /**
     * Notes: 获取销售商户
     * @Author: <C.Jason>
     * @Date: 2019/11/18 11:13 上午
     * @return mixed
     */
    public function getSellerIdentifier();

}
