<?php

namespace AsLong\Cart\Drivers;

use AsLong\Cart\Models\Cart;

class Database
{

    public function get($user)
    {
        return Cart::where('user_id', $user)->pluck('content', 'row_id');
    }

    public function put($rowId, $user, $content)
    {
        return Cart::updateOrCreate([
            'row_id'  => $rowId,
            'user_id' => $user,
        ], [
            'content' => $content,
        ]);
    }

    function remove($rowId, $user)
    {
        return Cart::where('row_id', $rowId)->where('user_id', $user)->delete();
    }

    function destroy($user)
    {
        return Cart::where('user_id', $user)->delete();
    }

}
