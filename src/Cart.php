<?php

namespace AsLong\Cart;

use AsLong\Cart\Contracts\Buyable;
use AsLong\Cart\Drivers\Database;
use AsLong\Cart\Exceptions\CartException;
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
        if ($user instanceOf JWTSubject) {
            $this->user = $user->getJWTIdentifier();
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
     * @param Buyable $buyable
     * @param null $qty
     * @param array $options
     * @return Item
     */
    public function add(Buyable $buyable, $qty = null, $options = [])
    {
        return $this->add($buyable, $qty, $options);
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
        if ($id instanceof Buyable) {
            $item = Item::fromBuyable($id);
        } elseif (is_numeric($id)) {
            $item = Item::fromAttributes($id, $price, $seller, $options);
        } else {
            throw new CartException('选购商品有误');
        }

        $item->setQuantity($qty ?: 1);

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
    public function total()
    {
        $content = $this->getContent();

        return $content->reduce(function ($total, Item $cartItem) {
            return $total + ($cartItem->qty * $cartItem->price);
        }, 0);
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

}
