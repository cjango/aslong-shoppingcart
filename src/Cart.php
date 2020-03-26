<?php

namespace AsLong\Cart;

use AsLong\Cart\Contracts\ShouldCart;
use AsLong\Cart\Drivers\Database;
use AsLong\Cart\Exceptions\CartException;
use AsLong\Cart\Utils\Helper;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Cart
{

    /**
     * @var int
     */
    public $user;

    /**
     * 数据存储模型
     * @var Cart
     */
    private $driver;

    /**
     * 初始化
     */
    public function __construct()
    {
        $this->driver = new Database();
    }

    /**
     * Notes: 设置当前用户
     * @Author: <C.Jason>
     * @Date: 2019/11/15 4:11 下午
     * @param $user
     * @return $this
     */
    public function user($user)
    {
        if ($user instanceOf Authenticatable) {
            $this->user = $user->getAuthIdentifier();
        } elseif (is_numeric($user)) {
            $this->user = $user;
        } else {
            throw new CartException('非法用户');
        }

        return $this;
    }

    /**
     * Notes: 返回购物车全部内容
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:20 下午
     * @return Collection
     */
    public function all()
    {
        return $this->getContent();
    }

    /**
     * Notes: 获取购物车内容
     * @Author: <C.Jason>
     * @Date: 2019/11/18 11:52 上午
     */
    protected function getContent()
    {
        return $this->driver->get($this->user) ?: new Collection();
    }

    /**
     * Notes: 通过 Buyable 快速加入购物车
     * @Author: <C.Jason>
     * @Date: 2019/11/18 4:06 下午
     * @param ShouldCart $cartable
     * @param null $qty
     * @param array $options
     * @return Item
     */
    public function add(ShouldCart $cartable, $qty = null, $options = [])
    {
        $item = Item::fromBuyable($cartable);

        return $this->addToCart($item, $qty);
    }

    /**
     * Notes: 新增商品到购物车
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:20 下午
     * @param $id
     * @param null $qty
     * @param null $price
     * @param null $seller
     * @param array $options
     * @return Item
     */
    public function addById($id, $qty = null, $price = null, $seller = null, array $options = [])
    {
        if (is_numeric($id)) {
            $item = Item::fromAttributes($id, $price, $seller, $options);
        } else {
            throw new CartException('选购商品有误');
        }

        return $this->addToCart($item, $qty);
    }

    /**
     * Notes: 将Item加入到购物车
     * @Author: <C.Jason>
     * @Date: 2019/11/19 10:44 上午
     * @param Item $item
     * @param $qty
     * @return Item
     */
    protected function addToCart(Item $item, $qty)
    {
        $qty = $qty ?: 1;
        $item->setQuantity($qty);

        $content = $this->getContent();
        if ($content->has($item->rowId)) {
            $item->setQuantity($content->get($item->rowId)->qty + $qty);
        }
        $this->driver->put($item->rowId, $this->user, $item);

        return $item;
    }

    /**
     * Notes: 更新购物车商品数量
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:21 下午
     * @param $rowId
     * @param $qty
     * @return mixed
     */
    public function update($rowId, $qty)
    {
        $item = $this->get($rowId);
        $item->setQuantity($qty);
        $this->driver->put($rowId, $this->user, $item);

        return $item;
    }

    /**
     * Notes: 获取一条详情
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:21 下午
     * @param $rowId
     * @return mixed
     */
    public function get($rowId)
    {
        return $this->getContent()->get($rowId);
    }

    /**
     * Notes: 删除一条购物车数据
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:21 下午
     * @param $rowId
     * @return mixed
     */
    public function remove($rowId)
    {
        if ($this->get($rowId)) {
            return $this->driver->remove($rowId, $this->user);
        } else {
            throw new CartException('没有可移除的数据');
        }
    }

    /**
     * Notes: 清空购物车
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:21 下午
     * @return mixed
     */
    public function destroy()
    {
        return $this->driver->destroy($this->user);
    }

    /**
     * Notes: 返回全部内容
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:21 下午
     * @return mixed
     */
    public function total($decimals = null, $decimalPoint = null, $thousandSeperator = null)
    {
        $content = $this->getContent();

        $total = $content->reduce(function ($total, Item $cartItem) {
            return $total + ($cartItem->qty * $cartItem->price);
        }, 0);

        return Helper::numberFormat($total, $decimals, $decimalPoint, $thousandSeperator);
    }

    /**
     * Notes: 不进行格式化的购物车总额
     * @Author: <C.Jason>
     * @Date: 2019/11/19 11:16 上午
     * @return mixed
     */
    public function totalOrigin()
    {
        $content = $this->getContent();

        $total = $content->reduce(function ($total, Item $cartItem) {
            return bcadd($total, bcmul($cartItem->qty, $cartItem->price, 2), 2);
        }, 0);

        return floatval($total);
    }

    /**
     * Notes: 返回购物车商品数量
     * @Author: <C.Jason>
     * @Date: 2019/11/18 2:22 下午
     * @return mixed
     */
    public function count()
    {
        $content = $this->getContent();

        return $content->sum('qty');
    }

    public function __get($attribute)
    {
        switch ($attribute) {
            case  'count':
                return $this->count();
                break;
            case  'total':
                return $this->total();
                break;
            case  'totalOrigin':
                return $this->totalOrigin();
                break;
            default:
                return null;
                break;
        }
    }

}
