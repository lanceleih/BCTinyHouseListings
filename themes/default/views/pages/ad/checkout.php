<?php defined('SYSPATH') or die('No direct script access.');?>

<div class="well col-xs-12 col-sm-12 col-md-12">
    <div class="row">
        <div class="col-xs-6 col-sm-6 col-md-6">
            <address>
                <strong><?=Core::config('general.site_name')?></strong>
                <br>
                <?=Core::config('general.base_url')?>
            </address>
        </div>
        <div class="col-xs-6 col-sm-6 col-md-6 text-right">
            <p>
                <?if(isset($order->VAT) AND $order->VAT > 0):?>
                    <em><?=_e('VAT Number')?>: <?=$order->VAT_country?> <?=$order->VAT_number?></em>
                    <br>
                <?endif?>
                <em><?=_e('Date')?>: <?= Date::format($order->created, core::config('general.date_format'))?></em>
                <br>
                <em><?=_e('Checkout')?> :# <?=$order->id_order?></em>
            </p>
        </div>
    </div>
    <div class="row">
        <div class="text-center">
            <h1><?=_e('Checkout')?></h1>
        </div>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="text-align: center">#</th>
                    <th><?=_e('Product')?></th>
                    <th class="text-center"><?=_e('Price')?></th>
                </tr>
            </thead>
            <tbody>
                <?if($order->id_product == Model_Order::PRODUCT_AD_SELL AND $order->ad->shipping_price()):?>
                    <tr>
                        <td class="col-md-1" style="text-align: center"><?=$order->id_product?></td>
                        <td class="col-md-9"><?=$order->description?> <em>(<?=Model_Order::product_desc($order->id_product)?>)</em></td>
                        <td class="col-md-2 text-center">
                            <?if ($order->ad->shipping_pickup() AND core::get('shipping_pickup')):?>
                                <?=i18n::money_format($order->amount, $order->currency)?>
                            <?else:?>
                                <?=i18n::money_format($order->amount - $order->ad->cf_shipping, $order->currency)?>
                            <?endif?>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-md-1" style="text-align: center"></td>
                        <td class="col-md-9">
                            <?if ($order->ad->shipping_pickup() AND core::get('shipping_pickup')):?>
                                <?=_e('Customer Pickup')?>
                            <?else:?>
                                <?=_e('Shipping')?>
                            <?endif?>
                            <?if ($order->ad->shipping_pickup()):?>
                                <div class="dropdown" style="display:inline-block;">
                                    <button class="btn btn-xs btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                                        <?=_e('Change')?>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-header"><?=_e('Shipping method')?></li>
                                        <li><a href="<?=Route::url('default',array('controller'=>'ad', 'action'=>'checkout','id'=>$order->id_order))?>?shipping_pickup=1"><?=_e('Customer Pickup - Free')?></a></li>
                                        <li><a href="<?=Route::url('default',array('controller'=>'ad', 'action'=>'checkout','id'=>$order->id_order))?>"><?=_e('Shipping')?> – <?=i18n::money_format($order->ad->shipping_price())?></a></li>
                                    </ul>
                                </div>
                            <?endif?>
                        </td>
                        <td class="col-md-2 text-center">
                            <?if ($order->ad->shipping_pickup() AND core::get('shipping_pickup')):?>
                                <?=i18n::money_format(0, $order->currency)?>
                            <?else:?>
                                <?=i18n::money_format($order->ad->cf_shipping, $order->currency)?>
                            <?endif?>
                        </td>
                    </tr>
                <?else:?>
                    <tr>
                        <td class="col-md-1" style="text-align: center"><?=$order->id_product?></td>
                        <?if (Theme::get('premium')==1):?>
                            <td class="col-md-9">
                                <?=$order->description?> 
                                <em>(<?=Model_Order::product_desc($order->id_product)?> 
                                    <?if ($order->id_product == Model_Order::PRODUCT_TO_FEATURED):?>
                                        <?=$order->featured_days?> <?=_e('Days')?>
                                    <?endif?>
                                    )
                                </em>
                                <div class="dropdown" style="display:inline-block;">
                                <?if ($order->id_product == Model_Order::PRODUCT_TO_FEATURED AND is_array($featured_plans=Model_Order::get_featured_plans()) AND count($featured_plans) > 1):?>
                                    <button class="btn btn-xs btn-info dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                                        <?=_e('Change plan')?> 
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?foreach ($featured_plans as $days => $price):?>
                                            <?if ($order->featured_days != $days):?>
                                                <li>
                                                    <a href="<?=Route::url('default',array('controller'=>'ad', 'action'=>'checkout','id'=>$order->id_order))?>?featured_days=<?=$days?>">
                                                        <small><?=$days?> <?=_e('Days')?> - <?=core::config('payment.paypal_currency')?> <?=$price?></small>
                                                    </a>
                                                </li>
                                            <?endif?>
                                        <?endforeach?>
                                    </ul>
                                <?endif?>
                                </div>
                            </td>
                        <?else :?>
                            <td class="col-md-9"><?=$order->description?> <em>(<?=Model_Order::product_desc($order->id_product)?>)</em></td>
                        <?endif?>
                        <td class="col-md-2 text-center"><?=($order->id_product == Model_Order::PRODUCT_AD_SELL)?i18n::money_format(($order->coupon->loaded())?$order->original_price():$order->original_price(), $order->currency):i18n::format_currency(($order->coupon->loaded())?$order->original_price():$order->original_price(), $order->currency)?></td>
                    </tr>
                    <?if (Theme::get('premium')==1 AND $order->coupon->loaded()):?>
                        <?$discount = ($order->coupon->discount_amount==0)?($order->original_price() * $order->coupon->discount_percentage/100):$order->coupon->discount_amount;?>
                        <tr>
                            <td class="col-md-1" style="text-align: center">
                                <?=$order->id_coupon?>
                            </td>
                            <td class="col-md-9">
                                <?=_e('Coupon')?> '<?=$order->coupon->name?>'
                                <?=sprintf(__('valid until %s'), Date::format($order->coupon->valid_date, core::config('general.date_format')))?>.
                            </td>
                            <td class="col-md-2 text-center text-danger">
                                -<?=i18n::format_currency($discount, $order->currency)?>
                            </td>
                        </tr>  
                    <?endif?>
                <?endif?>
                <tr>
                    <td class="col-md-1" style="text-align: center"><?=$order->ad->id_ad?></td>
                    <td colspan=2 class="col-md-12">
                        <em><?=$order->ad->title?></em>
                    </td>
                </tr>

                <?if(isset($order->VAT) AND $order->VAT > 0):?>
                    <td class="col-md-1" style="text-align: center"></td>
                    <td class="col-md-9">
                        <em><?=_e('VAT')?> <?=number_format($order->VAT,2)?>%</em>
                    </td>
                    <td class="col-md-2 text-center text-danger">
                        <?if($order->id_product == Model_Order::PRODUCT_AD_SELL):?>
                            <?=i18n::money_format($order->original_price()*$order->VAT/100, $order->currency)?>
                        <?else:?>
                            <?if(isset($discount)):?>
                                <?=i18n::format_currency(($order->original_price()-$discount)*$order->VAT/100, $order->currency)?>
                            <?else:?>
                                <?=i18n::format_currency($order->original_price()*$order->VAT/100, $order->currency)?>
                            <?endif?>
                        <?endif?>
                    </td>
                <?endif?>

                <tr>
                    <td>   </td>
                    <td class="text-right"><h4><strong><?=_e('Total')?>: </strong></h4></td>
                    <td class="text-center text-danger">
                        <h4>
                            <strong>
                                <?if($order->id_product == Model_Order::PRODUCT_AD_SELL):?>
                                    <?=i18n::money_format($order->amount, $order->currency)?>
                                <?else:?>
                                    <?=i18n::format_currency($order->amount, $order->currency)?>
                                <?endif?>
                            </strong>
                        </h4>
                    </td>
                </tr>
            </tbody>
        </table>

        <?if ($order->amount>0):?>

        <?=StripeKO::button_connect($order)?>
        
        <?if (Core::config('payment.paypal_account')!=''):?>
            <p class="text-right">
                <a class="btn btn-success btn-lg" href="<?=Route::url('default', array('controller'=> 'paypal','action'=>'pay' , 'id' => $order->id_order))?>">
                    <?=_e('Pay with Paypal')?> <span class="glyphicon glyphicon-chevron-right"></span>
                </a>
            </p>
        <?endif?>

        <?if ($order->id_product!=Model_Order::PRODUCT_AD_SELL):?>
            <?if ( ($user = Auth::instance()->get_user())!=FALSE AND ($user->is_admin() OR $user->is_moderator())):?>
                <ul class="list-inline text-right">
                    <li>
                        <a title="<?=__('Mark as paid')?>" class="btn btn-warning" href="<?=Route::url('oc-panel', array('controller'=> 'order', 'action'=>'pay','id'=>$order->id_order))?>">
                            <i class="glyphicon glyphicon-usd"></i> <?=_e('Mark as paid')?>
                        </a>
                    </li>
                </ul>
            <?endif?>
            <?if (Theme::get('premium')==1) :?>
                <?=Controller_Authorize::form($order)?>
                <div class="text-right">
                    <ul class="list-inline">
                        <?if(($pm = Paymill::button($order)) != ''):?>
                            <li class="text-right"><?=$pm?></li>
                        <?endif?>
                    </ul>
                </div>
                <div class="text-right">
                    <ul class="list-inline">
                        <?if(($sk = StripeKO::button($order)) != ''):?>
                            <li class="text-right"><?=$sk?></li>
                        <?endif?>
                        <?if(($bp = Bitpay::button($order)) != ''):?>
                            <li class="text-right"><?=$bp?></li>
                        <?endif?>
                        <?if(($two = twocheckout::form($order)) != ''):?>
                            <li class="text-right"><?=$two?></li>
                        <?endif?>
                        <?if(($paysbuy = paysbuy::form($order)) != ''):?>
                            <li class="text-right"><?=$paysbuy?></li>
                        <?endif?>
                        <?if(($securepay = securepay::button($order)) != ''):?>
                            <li class="text-right"><?=$securepay?></li>
                        <?endif?>
                        <?if(($robokassa = robokassa::button($order)) != ''):?>
                            <li class="text-right"><?=$robokassa?></li>
                        <?endif?>
                        <?if(($paguelofacil = paguelofacil::button($order)) != ''):?>
                            <li class="text-right"><?=$paguelofacil?></li>
                        <?endif?>
                        <?if(($paytabs = paytabs::button($order)) != ''):?>
                            <li class="text-right"><?=$paytabs?></li>
                        <?endif?>
                        <?if(($payfast = payfast::form($order)) != ''):?>
                            <li class="text-right"><?=$payfast?></li>
                        <?endif?>
                        <?if(($mp = MercadoPago::button($order)) != ''):?>
                            <li class="text-right"><?=$mp?></li>
                        <?endif?>
                        <?if( ($alt = $order->alternative_pay_button()) != ''):?>
                            <li class="text-right"><?=$alt?></li>
                        <?endif?>
                    </ul>
                    <?=View::factory('coupon')?>
                </div>
            <?elseif ( ($alt = $order->alternative_pay_button()) != '') :?>
                <div class="text-right">
                    <ul class="list-inline">
                        <li class="text-right"><?=$alt?></li>
                    </ul>
                </div>
            <?endif?>
        <?endif?>

        <?else:?>
            <ul class="list-inline text-right">
                <li>
                    <a title="<?=__('Click to proceed')?>" class="btn btn-success" href="<?=Route::url('default', array('controller'=> 'ad', 'action'=>'checkoutfree','id'=>$order->id_order))?>">
                        <?=_e('Click to proceed')?>
                    </a>
                </li>
                <?=View::factory('coupon')?>
            </ul>
        <?endif?>

    </div>
</div>

<?if (core::config('payment.fraudlabspro')!=''): ?>
<script>
    (function(){
        function s() {
            var e = document.createElement('script');
            e.type = 'text/javascript';
            e.async = true;
            e.src = ('https:' === document.location.protocol ? 'https://' : 'http://') + 'cdn.fraudlabspro.com/s.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(e, s);
        }             
        (window.attachEvent) ? window.attachEvent('onload', s) : window.addEventListener('load', s, false);
    })();
</script>
<?endif?>