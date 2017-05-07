<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Import tools for admin
 *
 * @package    OC
 * @category   Tools
 * @author     Oliver <oliver@open-classifieds.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */
class Controller_Panel_Algolia extends Controller_Panel_Tools {

    public function action_index()
    {
        $this->template->title = __('Algolia');
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title));

        //force clean cache
        if (Core::get('reindex')==1)
        {
            Algolia::reindex();
            Alert::set(Alert::SUCCESS,__('All indeces re-indexed'));

        }


        $this->template->content = View::factory('oc-panel/pages/tools/algolia');

        return;
    }
}
