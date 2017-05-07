<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Plan extends Controller {

  
    /**
     *
     * Contruct that checks you are loged in before nothing else happens!
     */
    function __construct(Request $request, Response $response)
    {
        if (Theme::get('premium')!=1)
        {
            Alert::set(Alert::INFO,  __('Upgrade your Open Classifieds site to activate this feature.'));
            $this->redirect(Route::url('oc-panel',array('controller'=>'market')));
        }
       
        parent::__construct($request,$response);
    }

    /**
     *
     * Display pricing page
     * @throws HTTP_Exception_404
     */
    public function action_index()
    {        
        if (Core::config('general.subscriptions')==TRUE)
        {
            Controller::$full_width = TRUE;
            $this->template->title            = __('Pricing');
            $this->template->meta_description = $this->template->title;
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title));

            $plans = New Model_Plan();

            $plans = $plans->where('status','=',1)
                            ->order_by('price','asc')
                            ->cached()->find_all();
            
            $subscription = ($this->user!=FALSE AND $this->user->subscription()->loaded())?$this->user->subscription():FALSE;

            $this->template->content = View::factory('pages/plan/pricing',array('plans'=>$plans,'user'=>$this->user,'subscription'=>$subscription));

        }
        else//this should never happen
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }


    /**
     * [action_buy] Pay for ad, and set new order 
     *
     */
    public function action_buy()
    {
        if (Core::config('general.subscriptions')==FALSE)
            throw HTTP_Exception::factory(404,__('Page not found'));

        //getting the user that wants to buy now
        if (!Auth::instance()->logged_in())
        {
            Alert::set(Alert::INFO, __('To buy this product you need to register first.'));
            $this->redirect(Route::url('oc-panel'));
        }
        
        //check plan exists
        $plan  = new Model_Plan();
        $plan->where('seoname','=',$this->request->param('id'))->where('status','=',1)->find();

        //loaded published and with stock if we control the stock.
        if($plan->loaded() AND $plan->status==1)
        {
            //free plan can not be renewed
            if ($plan->price==0 AND $this->user->subscription()->id_plan == $plan->id_plan)
            {
                Alert::set(Alert::WARNING, __('Free plan can not be renewed, before expired'));
                HTTP::redirect(Route::url('pricing'));
            }


            $order = Model_Order::new_order(NULL, $this->user, $plan->id_plan, $plan->price, core::config('payment.paypal_currency'), __('Subscription to ').$plan->name);

            //free plan no checkout
            if ($plan->price==0)
            {
                $order->confirm_payment('cash');
                $this->redirect(Route::url('oc-panel', array('controller'=>'profile','action'=>'orders')));
            }
            else
                $this->redirect(Route::url('default', array('controller' =>'plan','action'=>'checkout' ,'id' => $order->id_order)));
        }
        else
            throw HTTP_Exception::factory(404,__('Page not found'));
        
    }


    /**
     * pay an invoice, renders the paymenthods button, anyone with an ID of an order can pay it, we do not have control
     * @return [type] [description]
     */
    public function action_checkout()
    {
        $order = new Model_Order($this->request->param('id'));

        if ($order->loaded())
        {
            //hack jquery paymill
            Paymill::jquery();

            //if paid...no way jose
            if ($order->status != Model_Order::STATUS_CREATED)
            {
                Alert::set(Alert::INFO, __('This order was already paid.'));
                $this->redirect(Route::url('default'));
            }

            //checks coupons or amount of featured days
            $order->check_pricing();

            //adds VAT to the amount
            $order->add_VAT();

            //template header
            $this->template->title              = __('Checkout').' '.Model_Order::product_desc($order->id_product);
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Home'))->set_url(Route::url('default')));
            Breadcrumbs::add(Breadcrumb::factory()->set_title(__('Pricing'))->set_url(Route::url('pricing')));
            Breadcrumbs::add(Breadcrumb::factory()->set_title($this->template->title ));

            Controller::$full_width = TRUE;

            $this->template->bind('content', $content);

            $this->template->content = View::factory('pages/ad/checkout',array('order' => $order)); 
        }
        else
        {
            //throw 404
            throw HTTP_Exception::factory(404,__('Page not found'));
        }
    }


} // End Page controller
