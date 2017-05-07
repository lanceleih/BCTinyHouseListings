<?php defined('SYSPATH') or die('No direct script access.');?>
<a class="btn btn-info pay-btn full-w" 
            href="<?=Route::url('default',array('controller'=>'bitpay','action'=>'pay','id'=>$order->id_order))?>">
            <?=_e('Pay with Bitcoin')?></a>