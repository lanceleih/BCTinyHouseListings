<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Import tools for admin
 *
 * @package    OC
 * @category   Tools
 * @author     Chema <chema@open-classifieds.com>
 * @copyright  (c) 2009-2013 Open Classifieds Team
 * @license    GPL v3
 */
class Controller_Panel_Import extends Controller_Panel_Tools {



        /*
        1- uploads CSV limit size, done
        2- verify correct format, done
        3- insert in table migration (create if doesnt exists), done
        
        Once this is done we display to the user:
        You have X ads to migrate. Click here to start

        That action will:
        1- Read X ads from the DB
        2- Row by row of the ads convert:
            - email to id_user
            - category name to id_category
            - location name to id_location
            - generate seoname
            - fields, title, description, date published
            - Ad images ** read below
        3- Mark Ad as migrated

        


        How many rows can we process each time? 5? add a redirect every 5? how they cancel the transformation? ajax call?
         */
        
    public function action_index()
    {
        $this->template->title = __('Import tool for ads');
        Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title));
        $this->template->scripts['footer'][] = 'js/oc-panel/import.js';

        $this->template->content = View::factory('oc-panel/pages/tools/import_ads',array('ads_import'=>$this->amount_ads_import()));

    }


    /**
     * action for form CSV import
     * @return redirect 
     */
    public function action_csv()
    {
        //sending a CSV
        if($_POST AND isset($_FILES['csv_file_ads']))
        {
            if ($_FILES['csv_file_ads']['size'] > 1048576)
            {
                Alert::set(Alert::ERROR, __('1 MB file'));
                $this->redirect(Route::url('oc-panel',array('controller'=>'import','action'=>'index')));
            }

            //header that I expect
            $expected_header = array('user_name','user_email','title','description','date','category','location',
                                        'price','address','phone','website','image_1','image_2','image_3','image_4');

            $csv = $_FILES['csv_file_ads']["tmp_name"];

            $ads_array = Core::csv_to_array($csv,$expected_header);

            if (count($ads_array) > 10000)
            {
                Alert::set(Alert::ERROR, __('limited to 10.000 at a time'));
                $this->redirect(Route::url('oc-panel',array('controller'=>'import','action'=>'index')));
            }

            if ( $ads_array===FALSE OR count($ads_array)<=0 OR ($ads_imported = $this->insert_into_import($ads_array,$expected_header))===FALSE )
            {
                Alert::set(Alert::ERROR, __('Something went wrong, please check format of the file! Remove single quotes or strange characters, in case you have any.'));
            }
            else
            {
                Alert::set(Alert::SUCCESS, sprintf(__('%d Ads imported, click process to start migration'),$ads_imported));
            }
            
        } 

        $this->redirect(Route::url('oc-panel',array('controller'=>'import','action'=>'index')));
    }

    public function action_process()
    {
        $this->auto_render = FALSE;
        $this->template = View::factory('js');

        //finished?
        if ($this->amount_ads_import()===0)
        {
            /*Alert::set(Alert::SUCCESS,__('All ads are processed! Congrats!'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'import','action'=>'index')));*/
            $this->template->content = json_encode('OK');
        }
        //keep going!
        else
        {
            //how many ads we process in each request? pass get/post
            $limit_process = Core::request('limit',5);

            $ads_import = DB::select()
                            ->from('adsimport')
                            ->where('processed','=','0')
                            ->limit($limit_process)
                            ->as_object()
                            ->execute();

            $i=0;
            foreach ($ads_import as $adi) 
            {
                if ($this->create_ad($adi)===TRUE)
                    $i++;
            }

            $todo  = $this->amount_ads_import();
            $done  = $this->amount_ads_import(1); 
            $total = $todo + $done;

            $this->template->content = json_encode(round(100-($todo*100/$total)));
            //$this->redirect(Route::url('oc-panel',array('controller'=>'import','action'=>'process')));

        }
        
    }

    public function action_deletequeue()
    {
        //finished?
        if ($this->amount_ads_import()>0)
        {
           
            //TRUNCATE oc2_adsimport

            //check if in the table other users with same email set the id_user, then gets faster ;)
            try {
                DB::delete('adsimport')->execute()  ;
            } catch (Exception $e) {
                
            }

            Alert::set(Alert::SUCCESS, __('Queue cleaned'));
            

        }

        $this->redirect(Route::url('oc-panel',array('controller'=>'import','action'=>'index')));
        
    }


    /**
     * creates an ad from a row of import
     * @param  class adsimport $adi 
     * @return boolean      
     */
    private function create_ad($adi)
    {
        //new advertisement
        $ad = new Model_Ad();

        //create user?
        if ($adi->id_user==NULL OR !is_numeric($adi->id_user))
        {
            //create the user
            $user = Model_User::create_user($adi->user_email,$adi->user_name);

            //check if in the table other users with same email set the id_user, then gets faster ;)
            try {
                DB::update('adsimport')->set(array('id_user' => $user->id_user))
                ->where('user_email', '=', $adi->user_email)->execute();;
            } catch (Exception $e) {
                
            }

            //set id user to the new ad
            $ad->id_user = $user->id_user;
            
        }
        //user was already in the import DB
        else
        {
            $ad->id_user = $adi->id_user;
        }

        //create category?
        if ($adi->id_category==NULL OR !is_numeric($adi->id_category))
        {
            //create the category
            $cat = Model_Category::create_name($adi->category);

            //check if in the table other cats with same name set the id_category, then gets faster ;)
            try {
                DB::update('adsimport')->set(array('id_category' => $cat->id_category))
                ->where('category', '=', $adi->category)->execute();
            } catch (Exception $e) {}

            //set id user to the new ad
            $ad->id_category = $cat->id_category;
            
        }
        //user was already in the import DB
        else
        {
            $ad->id_category = $adi->id_category;
        }

        //create location?
        if (isset($adi->location) AND !empty($adi->location) AND ($adi->id_location==NULL OR !is_numeric($adi->id_location)))
        {
            //create the location
            $loc = Model_Location::create_name($adi->location);

            //check if in the table other cats with same name set the id_location, then gets faster ;)
            try {
                DB::update('adsimport')->set(array('id_location' => $loc->id_location))
                ->where('location', '=', $adi->location)->execute();
            } catch (Exception $e) {}

            //set id user to the new ad
            $ad->id_location = $loc->id_location;
            
        }
        //user was already in the import DB
        elseif(is_numeric($adi->id_location))
        {
            $ad->id_location = $adi->id_location;
        }
        

        $ad->title      = $adi->title;
        $ad->seotitle   = $ad->gen_seo_title($adi->title);
        $ad->description= Text::html2bb($adi->description);
        $ad->published  = $adi->date;
        $ad->created    = $adi->date;
        $ad->price      = $adi->price;
        $ad->address    = $adi->address;
        $ad->phone      = $adi->phone;
        $ad->website    = $adi->website;
        $ad->status     = Model_Ad::STATUS_PUBLISHED;
        
        try {
            $ad->save();
        } catch (Exception $e) {
            return FALSE;
        }

        //save images
        if (($has_images = $this->process_images($ad,$adi))>0)
        {
            $ad->has_images = $has_images;
            try {
                $ad->save();
            } catch (Exception $e) {
                return FALSE;
            }
        }

        //mark it as done
        try {
            DB::update('adsimport')->set(array('processed' => 1))
            ->where('id_import', '=', $adi->id_import)->execute();
            
            return TRUE;

        } catch (Exception $e) {
            return FALSE;
        }
    }


    /***Adding images
        image_1
        image_2
        image_3
        image_4
        - image_X needs to be a varchar. 
        - If has protocol will download image,
        - if its a route will read formt he path
        - once we have the image, we change size and create thumb
        - if was downloaded we delete it*/

    private function process_images($ad,$adi)
    {
        //pattern in row CSV
        $image_pattern = 'image_';
        //amount images in CSV
        $num_images = core::config('advertisement.num_images');

        if ($num_images>4)
            $num_images = 4;

        //how many images has the ad, return
        $ad_images  = 0;

        for ($i=1; $i <=$num_images ; $i++) 
        { 
            $image = $adi->{$image_pattern.$i};
            //trying save image
            if ($this->process_image($ad,$image,$ad_images+1)===TRUE)
                $ad_images++;

        }

        return $ad_images;
    }


    private function process_image($ad,$image,$num)
    {
        //from URL
        if (Valid::URL($image))
        {
            //download it, if takes more than 10 seconds...bad deal!
            $image_content = Core::curl_get_contents($image);

            //store if retrieved
            if ($image_content!==FALSE)
            {
                $file = DOCROOT.'images/import_'.$ad->id_ad.'_'.$num.'.jpg';

                if (!File::write($file, $image_content))
                    return FALSE;
            }
            else
                return FALSE;
        }
        //already in server
        elseif(file_exists(DOCROOT.$image))
        {
            $file = DOCROOT.$image;
        }

        try {
            $is_image = getimagesize($file);
        } catch (Exception $e) {
            $is_image = FALSE;
        }

        //only if its image will be returned
        return ($is_image!==FALSE)? $ad->save_image_file($file,$num):FALSE;
    }

    /**
     * inserts into table adsimport
     * @param  array $ads_array from csv
     * @param array $expected_header
     * @return bool/integer      
     */
    private function insert_into_import($ads_array,$expected_header)
    {
        $prefix = Database::instance()->table_prefix();

        //create table import if doesnt exists
        $query = DB::query(Database::INSERT,"CREATE TABLE IF NOT EXISTS `".$prefix."adsimport` (
                                              `id_import` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                              `id_user` int(10) unsigned  NULL,
                                              `user_name` varchar(145) NOT NULL,
                                              `user_email` varchar(145) NOT NULL,
                                              `id_category` int(10) unsigned  NULL,
                                              `category` varchar(145) NOT NULL,
                                              `id_location` int(10) unsigned  NULL,
                                              `location` varchar(145) NOT NULL,
                                              `title` varchar(145) NOT NULL,
                                              `description` text NOT NULL,
                                              `address` varchar(145) DEFAULT '0',
                                              `price` decimal(14,3) NOT NULL DEFAULT '0.000',
                                              `phone` varchar(30) DEFAULT NULL,
                                              `date` datetime DEFAULT NULL,
                                              `website` varchar(200) DEFAULT NULL,
                                              `image_1` varchar(200) DEFAULT NULL,
                                              `image_2` varchar(200) DEFAULT NULL,
                                              `image_3` varchar(200) DEFAULT NULL,
                                              `image_4` varchar(200) DEFAULT NULL,
                                              `processed` tinyint(1) NOT NULL DEFAULT '0',
                                              PRIMARY KEY (`id_import`)
                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"); 
        try 
        {
           $query->execute();
        } 
        catch (Exception $e) 
        {
            return FALSE;
        }

        //insert into table import
        foreach ($ads_array as $ad=>$values) 
        {
            $query = DB::insert('adsimport', $expected_header)->values($values);
            try 
            {
               $query->execute();
            } 
            catch (Exception $e) 
            {
                return FALSE;
            }
        }

        return count($ads_array);
    }

    /**
     * amount of ads left to iport
     * @return integer 
     */
    private function amount_ads_import($processed = 0)
    {
        //how many ads left to import?
        try {
            $ads_import =  DB::select(array(DB::expr('COUNT(`id_import`)'), 'total'))
                            ->from('adsimport')
                            ->where('processed','=',$processed)
                            ->execute()->as_array('total');
            $ads_import = key($ads_import);

        } 
        //in case table doesnt exists...
        catch (Exception $e) 
        {
            $ads_import = 0;
        }

        return $ads_import;
    }

    /*FROM OC DB to CSV
        
        SELECT u.name user_name,u.email user_email,a.title,a.description,a.published `date`,
                c.name category, l.name location,a.price,a.address,a.phone,a.website,
                '' image_1, '' image_2, '' image_3, '' image_4
        FROM `oc2_ads` as a
        INNER JOIN oc2_users u
        USING(id_user)
        INNER JOIN oc2_categories c
        USING (id_category)
        INNER JOIN oc2_locations l
        ON l.id_location=a.id_location
        WHERE a.status=1

    */
        

}