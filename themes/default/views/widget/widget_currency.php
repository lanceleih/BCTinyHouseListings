<?php defined('SYSPATH') or die('No direct script access.');?>

<?if ($widget->currency_title!=''):?>
    <div class="panel-heading">
        <h3 class="panel-title"><?=$widget->currency_title?></h3>
    </div>
<?endif?>
<div class="panel-body">
    <div class="form-group curry" data-locale="<?=Core::config('i18n.locale');?>" data-currencies="<?=($widget->currencies);?>" data-default="<?=($widget->default);?>">
        <div class="my-future-ddm"></div>
    </div>
</div>
