<?php

namespace App\Controller;

use App\Entity\Actions;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LogSearchController extends AbstractController
{

    /**
     * @Route("/log/search", name="log_search")
     */
    public function index()
    {
        return $this->render('log_search/index.html.twig', [
            'controller_name' => 'LogSearchController',
        ]);
    }

    /**
     * @Route("/log/search/pertype/rangetime", name="log_search")
     */
    public function timeRangeSearchLogs()
    {
        $startDate = '';
        $endDate = '';
        if(!empty($_GET['startDate'])){
            $startDate = $_GET['startDate'];
        }
        if(!empty($_GET['endDate'])){
            $endDate = $_GET['endDate'];
        }

        if(!empty($startDate)){
            $now = new \DateTime();
            $startDate = strtotime($now->format("Y-m-d h:i:s"));
        }
        if(!empty($endDate)){
            $now = new \DateTime();
            $endDate = strtotime($now->format("Y-m-d h:i:s"));
        }

        if($startDate > $endDate){
            return $this->render('log_search/timeRangeSearch.html.twig',[
                'controller_name' => 'LogSearchController',
                'errorMessage' => "Start date is later than end date",
                'entity' => array()
            ]);
        }

        $rsm = new ResultSetMapping();
        // build rsm here
        $rsm->addFieldResult("dest","dest","dest");
        $rsm->addFieldResult("source","source","source");
        $rsm->addFieldResult("date","date","date");
        $em = new EntityManagerHelper();
        $query = $em->getEntityManager()->createNativeQuery('SELECT sourceIp as source,destinationIp as dest, logger.timestamp as date  FROM logger JOIN  WHERE timestamp between ? AND ?', $rsm);
        $query->setParameter(1, $startDate);
        $query->setParameter(2, $endDate);
        $results = $query->getResult();

        $action = new Actions();
        $action->setAction("Code 1: User " . $this->getUser()->getUsername() . " searched total logs per type from " . $startDate . " to " . $endDate);
        $action->setUserId($this->getUser());

        $em->getEntityManager()->persist($action);
        $em->getEntityManager()->flush();

        return $this->render('log_search/timeRangeSearch.html.twig',[
            'controller_name' => 'LogSearchController',
            'errorMessage' => NULL,
            'entity' => $results
        ]);
    }
}
