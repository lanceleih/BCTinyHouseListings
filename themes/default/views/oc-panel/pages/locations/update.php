<?php defined('SYSPATH') or die('No direct script access.');?>

<h1 class="page-header page-title" id="crud-<?=$name?>">
    <?=__('Update')?> <?=Text::ucfirst(__($name))?>
</h1>

<hr>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=__('Location details')?></h3>
            </div>
            <div class="panel-body">
                <?=$form->render()?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=__('Upload location icon')?></h3>
            </div>
            <div class="panel-body">
                <?if (( $icon_src = $location->get_icon() )!==FALSE ):?>
                    <div class="row">
                        <div class="col-md-4">
                            <a class="thumbnail">
                                <img src="<?=$icon_src?>" class="img-rounded" alt="<?=__('Location icon')?>" height='200px'>
                            </a>
                        </div>
                    </div>
                <?endif?>
                <form class="form-horizontal" enctype="multipart/form-data" method="post" action="<?=Route::url('oc-panel',array('controller'=>'location','action'=>'icon','id'=>$form->object->id_location))?>">         
                    <?=Form::errors()?>  
                    
                    <div class="form-group">
                        <div class="col-sm-12">
                            <?= FORM::label('location_icon', __('Select from files'), array('for'=>'location_icon'))?>
                            <input type="file" name="location_icon" class="form-control" id="location_icon" />
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?=__('Submit')?></button> 
                    <?if (( $icon_src = $location->get_icon() )!==FALSE ):?>
                        <button type="submit"
                            class="btn btn-danger index-delete index-delete-inline"
                             onclick="return confirm('<?=__('Delete icon?')?>');" 
                             type="submit" 
                             name="icon_delete"
                             value="1" 
                             title="<?=__('Delete icon')?>">
                            <?=__('Delete icon')?>
                        </button>
                    <?endif?>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?=sprintf(__('Find %s‘s latitude & longitude'), $location->name)?></h3>
            </div>
            <div class="panel-body">
                <?= FORM::input('address', Request::current()->post('address'), array('class'=>'form-control', 'id'=>'address', 'placeholder'=>__('Type address')))?>
                <div class="popin-map-container">
                    <div class="map-inner" id="map" 
                        data-lat="<?=($location->latitude)?$location->latitude:core::config('advertisement.center_lat')?>" 
                        data-lon="<?=($location->latitude)?$location->longitude:core::config('advertisement.center_lon')?>"
                        data-zoom="<?=core::config('advertisement.map_zoom')?>" 
                        style="height:200px;width:100%">
                    </div>
                </div>
                <ul class="list-inline">
                    <li><?=__('Latitude')?>: <span id="preview_lat"><?=$location->latitude?></span></li>
                    <li><?=__('Longitude')?>: <span id="preview_lon"><?=$location->longitude?></span></li>
                </ul>
                <button type="submit" class="btn btn-primary gmap-submit"><?=__('Submit')?></button> 
            </div>
        </div>
    </div>
</div>