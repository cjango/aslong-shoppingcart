<?php

namespace AsLong\Cart;

use AsLong\Cart\Contracts\Buyable;
use AsLong\Cart\Exceptions\CartException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Item implements Arrayable, Jsonable
{

    public $rowId;

    public $id;

    public $price;

    public $qty;

    public $seller;

    public $options;

    public function __construct($id, $price, $seller, array $options = [])
    {
        $this->id      = $id;
        $this->price   = $price;
        $this->seller  = $seller;
        $this->options = $options;
        $this->rowId   = $this->generateRowId($id, $options);
    }

    /**
     * 生成每个用户唯一的行号
     * @param string $id
     * @param array $options
     * @return string
     */
    protected function generateRowId($id, array $options)
    {
        ksort($options);

        return md5($id . serialize($options));
    }

    static function fromBuyable(Buyable $item, array $options = [])
    {
        return new self($item->getBuyableIdentifier($options), $item->getBuyablePrice($options), $item->getSellerIdentifier(), $options);
    }

    public static function fromAttributes($id, $price, $seller, array $options = [])
    {
        return new self($id, $price, $seller, $options);
    }

    /**
     * Set the quantity for this cart item.
     * @param int|float $qty
     */
    public function setQuantity($qty)
    {
        if (empty($qty) || !is_numeric($qty)) {
            throw new CartException('不正确的数量.');
        }
        $this->qty = $qty;
    }

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
     * @return float|int
     */
    public function total($decimals = null, $decimalPoint = null, $thousandSeperator = null)
    {
        return $this->numberFormat($this->qty * $this->price, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Convert the object to its JSON representation.
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Get the instance as an array.
     * @return array
     */
    public function toArray()
    {
        return [
            'id'      => $this->id,
            'qty'     => $this->qty,
            'price'   => $this->numberFormat($this->price),
            'seller'  => $this->seller,
            'options' => $this->options,
            'total'   => $this->total(),
        ];
    }

    /**
     * Notes: 格式化价格结果
     * @Author: <C.Jason>
     * @Date: 2019/11/18 5:33 下午
     * @param $value
     * @param $decimals
     * @param $decimalPoint
     * @param $thousandSeperator
     * @return string
     */
    private function numberFormat($value, $decimals = null, $decimalPoint = null, $thousandSeperator = null)
    {
        if (is_null($decimals)) {
            $decimals = is_null(config('cart.format.decimals')) ? 2 : config('cart.format.decimals');
        }
        if (is_null($decimalPoint)) {
            $decimalPoint = is_null(config('cart.format.decimal_point')) ? '.' : config('cart.format.decimal_point');
        }
        if (is_null($thousandSeperator)) {
            $thousandSeperator = is_null(config('cart.format.thousand_seperator')) ? ',' : config('cart.format.thousand_seperator');
        }

        return number_format($value, $decimals, $decimalPoint, $thousandSeperator);
    }

}
