<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title" id="page-welcome">
    <?=core::config('general.site_name')?> <?=__('panel')?>
</h1>

<hr>

<p><?=__('This is the main overview page of your website.')?></p>

<?if (core::cookie('intro_panel')!=1):?>
    <div class="row" id="intro-panel">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title"><?=__('Welcome')?> <?=Auth::instance()->get_user()->name?></div>
                    <a href="#" class="close-panel"><i class="fa fa-close"></i><span class="sr-only"><?=__('Hide')?></span></a>
                </div>
                <div class="panel-body">
                    <p>
                        <?=__('Thanks for using Yclas. If you have any questions you can you can click the help button in the upper right corner.')?>
                    </p>
                    <p>
                        <?=__('Your installation version is')?> 
                        <a class="ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'update','action'=>'index'))?>?reload=1"><span class="label label-info"><?=core::VERSION?></span></a>
                    </p>
                    <h4 class="page-header"><?=__('Lets get started')?></h4>
                    <hr>
                    <a class="start-link ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'content','action'=>'page'))?>"><i class="linecons li_note"></i><?=__('Create or edit content')?></a><a class="start-link ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'theme','action'=>'options'))?>"><i class="linecons li_photo"></i><?=__('Change the theme options')?></a><a class="start-link ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'settings','action'=>'general'))?>"><i class="linecons li_params"></i><?=__('Edit the settings of this website')?></a>
                </div>
            </div>
        </div>
    </div>
<?endif?>

<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-3 block">
        <div class="panel panel-blue">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="linecon li_eye"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?=$visits_today?></div>
                        <div><?=__('New visits')?></div>
                    </div>
                </div>
            </div>
            <a class="ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'stats','action'=>'index'))?>">
                <div class="panel-footer">
                    <span class="pull-left"><?=__('View details')?></span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 block">
        <div class="panel panel-green">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="linecon li_user"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?=$users_today?></div>
                        <div><?=__('New users')?></div>
                    </div>
                </div>
            </div>
            <a class="ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'stats','action'=>'index'))?>">
                <div class="panel-footer">
                    <span class="pull-left"><?=__('View details')?></span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 block">
        <div class="panel panel-yellow">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="linecon li_note"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?=$ads_today?></div>
                        <div><?=__('New advertisements')?></div>
                    </div>
                </div>
            </div>
            <a class="ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'ad','action'=>'index'))?>">
                <div class="panel-footer">
                    <span class="pull-left"><?=__('View details')?></span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-3 block">
        <div class="panel panel-red">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-3">
                        <i class="linecon li_banknote"></i>
                    </div>
                    <div class="col-xs-9 text-right">
                        <div class="huge"><?=$orders_today?></div>
                        <div><?=__('New orders')?></div>
                    </div>
                </div>
            </div>
            <a class="ajax-load" href="<?=Route::url('oc-panel',array('controller'=>'order','action'=>'index'))?>">
                <div class="panel-footer">
                    <span class="pull-left"><?=__('View details')?></span>
                    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                    <div class="clearfix"></div>
                </div>
            </a>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><i class="fa fa-bar-chart-o fa-fw"></i> <?=__('Site Statistics')?></div>
            </div>
            <div class="panel-body">
                <p><?=__('This panel shows how many visitors your website had the past month.')?></p>
                <?if ($ads_total == 0) :?>
                    <h4 class="empty text-center"><?=__('There are no site statistics yet ...')?></h4>
                <?endif?>
            </div>
            <?if ($ads_total > 0) :?>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="hidden-xs"></th>
                            <th><?=__('Today')?></th>
                            <th><?=__('Yesterday')?></th>
                            <th class="hidden-xs"><?=__('Last 30 days')?></th>
                            <th><?=__('Total')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="hidden-xs"><b><?=__('Ads')?></b></td>
                            <td><?=$ads_today?></td>
                            <td><?=$ads_yesterday?></td>
                            <td class="hidden-xs"><?=$ads_month?></td>
                            <td><?=$ads_total?></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><b><?=__('Visits')?></b></td>
                            <td><?=$visits_today?></td>
                            <td><?=$visits_yesterday?></td>
                            <td class="hidden-xs"><?=$visits_month?></td>
                            <td><?=$visits_total?></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><b><?=__('Sales')?></b></td>
                            <td><?=$orders_today?></td>
                            <td><?=$orders_yesterday?></td>
                            <td class="hidden-xs"><?=$orders_month?></td>
                            <td><?=$orders_total?></td>
                        </tr>
                        <tr>
                            <td class="hidden-xs"><b><?=__('Users')?></b></td>
                            <td><?=$users_today?></td>
                            <td><?=$users_yesterday?></td>
                            <td class="hidden-xs"><?=$users_month?></td>
                            <td><?=$users_total?></td>
                        </tr>
                    </tbody>
                </table>
            <?endif?>
        </div>
    </div><!-- /.col-md-6 -->
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title"><i class="fa fa-file-text-o"></i> <?=__('Latest Published Ads')?></div>
            </div>
            <div class="panel-body">
                <p><?=__('This panel shows the latest published ads on your website.')?></p>
                <?if ($ads_total == 0) :?>
                    <h4 class="empty text-center"><?=__('There are no published ads yet ...')?></h4>
                <?endif?>
            </div>
            <?if ($ads_total > 0) :?>
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?=__('Name')?></th>
                            <th class="hidden-sm hidden-xs"><?=__('Category')?></th>
                            <th class="hidden-sm hidden-xs"><?=__('Location')?></th>
                            <?if(core::config('advertisement.count_visits')==1):?>
                                <th class="hidden-xs"><?=__('Hits')?></th>
                            <?endif?>
                            <th><?=__('Date')?></th>
                        </tr>
                    </thead>
                    <?if(isset($res)):?>
                        <tbody>
                            <? $i = 0; foreach($res as $ad):?>
                                <tr>
                                    <td><?=$ad->id_ad?>
                    
                                    <td><a href="<?=Route::url('ad', array('controller'=>'ad','category'=>$ad->category->seoname,'seotitle'=>$ad->seotitle))?>"><?= wordwrap($ad->title, 15, "<br />\n"); ?></a>
                                    </td>
                    
                                    <td class="hidden-sm hidden-xs"><?= wordwrap($ad->category->name, 15, "<br />\n"); ?>
                    
                                    <td class="hidden-sm hidden-xs">
                                        <?if($ad->location->loaded()):?>
                                            <?=wordwrap($ad->location->name, 15, "<br />\n");?>
                                        <?else:?>
                                            n/a
                                        <?endif?>
                                    </td>
                    
                                    <?if(core::config('advertisement.count_visits')==1):?>
                                        <td class="hidden-xs"><?=$ad->count_ad_hit();?></td>
                                    <?endif?>
                    
                                    <td><?= Date::format($ad->published, core::config('general.date_format'))?></td>
                
                                </tr>
                            <?endforeach?>
                        </tbody>
                    <?endif?>
                </table>
            <?endif?>
        </div>
    </div><!-- /.col-md-6 -->
</div><!-- /.row -->