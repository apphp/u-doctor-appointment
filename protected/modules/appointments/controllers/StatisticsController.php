<?php
/**
 * ReportsController controller
 * This controller intended to Backend modes
 *
 * PUBLIC:                  PRIVATE
 * -----------              ------------------
 * __construct              _getLastYears
 * indexAction
 * manageAction
 *
 */

namespace Modules\Appointments\Controllers;

// Module
use \Modules\Appointments\Components\AppointmentsComponent;
use \Modules\Appointments\Models\Appointments;
use \Modules\Appointments\Models\Orders;

// Global
use \A,
    \CAuth,
    \CLocale,
    \CController;

// Application
use \Website,
    \Bootstrap,
    \Modules;

class StatisticsController extends CController
{

    /**
     * Class default constructor
     */
    public function __construct()
    {
        parent::__construct();

        // Block access if the module is not installed
        if(!Modules::model()->isInstalled('appointments')){
            if(CAuth::isLoggedInAsAdmin()){
                $this->redirect('modules/index');
            }else{
                $this->redirect(Website::getDefaultPage());
            }
        }

        if(CAuth::isLoggedInAsAdmin()){
            // Set meta tags according to active manufacturer
            Website::setMetaTags(array('title'=>A::t('appointments', 'Statistics')));

            $this->_view->actionMessage = '';
            $this->_view->errorField = '';

            $this->_view->tabs = AppointmentsComponent::prepareTab('statistics');
        }

        $this->_view->dateFormat = Bootstrap::init()->getSettings('date_format');

        $settings = Bootstrap::init()->getSettings();
        $this->_view->dateFormat     = $settings->date_format;
        $this->_view->dateTimeFormat = $settings->datetime_format;
        $this->_view->numberFormat   = $settings->number_format;
        $this->_view->currencySymbol = A::app()->getCurrency('symbol');
        $this->_view->currencyPlace  = A::app()->getCurrency('symbol_place');
    }

    /**
     * Controller default action handler
     */
    public function indexAction()
    {
        $this->redirect('reports/manage');
    }

    /**
     * Manage action handler
     * @return void
     */
    public function manageAction()
    {
        Website::setBackend();
        Website::prepareBackendAction('manage', 'reports', 'reports/manage');

        $selectedYear = A::app()->getRequest()->getQuery('year', 'int');
        $currentYear  = CLocale::date('Y');
        $validYears   = $this->_getLastYears(5, $currentYear);

        // Prepare selected year
        if(empty($selectedYear) || !in_array($selectedYear, $validYears)){
            $selectedYear = $currentYear;
        }

        $fromDate     = $selectedYear.'-01-01 00:00:00';
        $toDate       = $selectedYear.'-12-31 23:59:59';

        // Prepare orders data
        $orders       = Orders::model()->findAll("created_date >= :from_date AND created_date <= :to_date AND status > 0", array(':from_date'=>$fromDate, ':to_date'=>$toDate));

        $ordersCount  = array_fill(1, 12, 0);
        $ordersIncome = array_fill(1, 12, 0);
        if(!empty($orders)){
            foreach($orders as $order){
                $month = (int)substr($order['created_date'], 5, 2);

                $ordersCount[$month]++;
                $ordersIncome[$month] += $order['total_price'];
            }
        }

        // Prepare appointments data
        $appointments              = Appointments::model()->findAll("appointment_date >= :from_date AND appointment_date <= :to_date", array(':from_date'=>$fromDate, ':to_date'=>$toDate));
        $appointmentsCount         = array_fill(1, 12, 0);
        $appointmentsApprovedCount = array_fill(1, 12, 0);

        if(!empty($appointments)){
            foreach($appointments as $appointment){
                $month = (int)substr($appointment['date_created'], 5, 2);

                $appointmentsCount[$month]++;
                if($appointment['status'] == 1) $appointmentsApprovedCount[$month]++;
            }
        }

        $this->_view->currentYear               = $currentYear;
        $this->_view->selectedYear              = $selectedYear;
        $this->_view->ordersCount               = $ordersCount;
        $this->_view->ordersIncome              = $ordersIncome;
        $this->_view->appointmentsCount         = $appointmentsCount;
        $this->_view->appointmentsApprovedCount = $appointmentsApprovedCount;

        $this->_view->render('statistics/manage');
    }

    /* *
     * Get last year
     * @param int $countYears
     * @param int $startYear
     * @return array
     * */
    private function _getLastYears($countYears = 5, $startYear = '')
    {
        if(empty($startYear)){
            $startYear = CLocale::date('Y');
        }
        $lastYears = array();

        for($i= $startYear; $i >= $startYear - $countYears; $i--){
            $lastYears[$i] = $i;
        }

        return $lastYears;
    }

}
