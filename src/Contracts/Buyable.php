<?php

namespace AsLong\Cart\Contracts;

interface Buyable
{

    /**
     * Notes:
     * @Author: <C.Jason>
     * @Date: 2019/11/18 11:14 上午
     * @param null $options
     * @return mixed
     */
    public function getBuyableIdentifier($options = null);

    /**
     * Notes:
     * @Author: <C.Jason>
     * @Date: 2019/11/18 11:14 上午
     * @param null $options
     * @return mixed
     */
    public function getBuyablePrice($options = null);

    /**
     * Notes:
     * @Author: <C.Jason>
     * @Date: 2019/11/18 11:13 上午
     * @return mixed
     */
    public function getSellerIdentifier();

}
