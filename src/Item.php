<?php

namespace AsLong\Cart;

use AsLong\Cart\Contracts\ShouldCart;
use AsLong\Cart\Exceptions\CartException;
use AsLong\Cart\Utils\Helper;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Item implements Arrayable, Jsonable
{

    /**
     * @var string
     */
    public $rowId;

    /**
     * @var string | null
     */
    public $cartable;

    /**
     * @var int
     */
    public $id;

    /**
     * @var float
     */
    public $price;

    /**
     * @var int
     */
    public $qty;

    /**
     * @var int
     */
    public $seller;

    /**
     * @var array | null
     */
    public $options;

    public function __construct($id, $price, $seller, array $options = [], $cartable = null)
    {
        $this->rowId    = $this->generateRowId($id, $options);
        $this->id       = $id;
        $this->price    = $price;
        $this->seller   = $seller;
        $this->options  = $options;
        $this->cartable = $cartable;
    }

    /**
     * 生成唯一的行号
     * @param string $id
     * @param array $options
     * @return string
     */
    protected function generateRowId($id, array $options)
    {
        ksort($options);

        return md5($id . serialize($options));
    }

    /**
     * Notes: 通过buyable创建新的实例
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:07 上午
     * @param ShouldCart $cartable
     * @param array $options
     * @return Item
     */
    static function fromBuyable(ShouldCart $cartable, array $options = [])
    {
        return new self(
            $cartable->getCartableIdentifier($options),
            $cartable->getCartablePrice($options),
            $cartable->getSellerIdentifier(),
            $options,
            get_class($cartable),
        );
    }

    /**
     * Notes: 通过属性，创建新的实例
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:07 上午
     * @param $id
     * @param $price
     * @param $seller
     * @param array $options
     * @return Item
     */
    public static function fromAttributes($id, $price, $seller, array $options = [])
    {
        return new self($id, $price, $seller, $options);
    }

    /**
     * Notes: 设置数量
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:06 上午
     * @param $qty
     */
    public function setQuantity($qty)
    {
        if (empty($qty) || !is_numeric($qty)) {
            throw new CartException('不正确的数量.');
        }
        $this->qty = $qty;
    }

    /**
     * Notes: 魔术方法
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:06 上午
     * @param $attr
     * @return float|int|null
     */
    public function __get($attr)
    {
        if (property_exists($this, $attr)) {
            return $this->{$attr};
        }

        switch ($attr) {
            case 'total':
                return $this->total();
                break;
            default:
                return null;
        }
    }

    /**
     * Notes: 获取价格
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:30 下午
     * @param null $decimals
     * @param null $decimalPoint
     * @param null $thousandSeperator
     * @return float|int
     */
    public function total($decimals = null, $decimalPoint = null, $thousandSeperator = null)
    {
        return Helper::numberFormat($this->qty * $this->price, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Notes: 不进行格式化的总价
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:15 上午
     * @return float|int
     */
    public function totalOrigin()
    {
        return floatval(bcmul($this->qty, $this->price, 2));
    }

    /**
     * Notes: 将结果转换为json
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:09 上午
     * @param int $options
     * @return false|string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Notes: 将实例转换为array
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:08 上午
     * @return array
     */
    public function toArray()
    {
        return [
            'row_id'   => $this->rowId,
            'cartable' => $this->cartable,
            'id'       => $this->id,
            'qty'      => $this->qty,
            'price'    => $this->numberFormat($this->price),
            'seller'   => $this->seller,
            'options'  => $this->options,
            'total'    => $this->total(),
        ];
    }

}
