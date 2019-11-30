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
     * @Route("/dashboard/search", name="log_search")
     */
    public function index()
    {
        return $this->render('log_search/index.html.twig', [
            'controller_name' => 'LogSearchController',
        ]);
    }

    /**
     * @Route("/dashboard/search/pertype", name="log_search_per_type")
     */
    public function perTypeLogs()
    {
//        $startDate = '';
//        $endDate = '';
//        if(!empty($_GET['startDate'])){
//            $startDate = $_GET['startDate'];
//        }
//        if(!empty($_GET['endDate'])){
//            $endDate = $_GET['endDate'];
//        }
//
//        if(!empty($startDate)){
//            $now = new \DateTime();
//            $startDate = strtotime($now->format("Y-m-d h:i:s"));
//        }
//        if(!empty($endDate)){
//            $now = new \DateTime();
//            $endDate = strtotime($now->format("Y-m-d h:i:s"));
//        }
//
//        if($startDate > $endDate){
//            return $this->render('log_search/timeRangeSearch.html.twig',[
//                'controller_name' => 'LogSearchController',
//                'errorMessage' => "Start date is later than end date",
//                'entity' => array()
//            ]);
//        }
//
//        $rsm = new ResultSetMapping();
//        // build rsm here
//        $rsm->addFieldResult("dest","dest","dest");
//        $rsm->addFieldResult("source","source","source");
//        $rsm->addFieldResult("date","date","date");
//        $em = new EntityManagerHelper();
//        $query = $em->getEntityManager()->createNativeQuery('SELECT sourceIp as source,destinationIp as dest, logger.timestamp as date  FROM logger JOIN  WHERE timestamp between ? AND ?', $rsm);
//        $query->setParameter(1, $startDate);
//        $query->setParameter(2, $endDate);
//        $results = $query->getResult();
//
//        $action = new Actions();
//        $action->setAction("Code 1: User " . $this->getUser()->getUsername() . " searched total logs per type from " . $startDate . " to " . $endDate);
//        $action->setUserId($this->getUser());
//
//        $em->getEntityManager()->persist($action);
//        $em->getEntityManager()->flush();

        return $this->render('log_search/pertype.html.twig', [
            'controller_name' => 'LogSearchController',
            'errorMessage' => NULL,
        ]);
    }

    /**
     * @Route("/dashboard/search/mostcommon", name="log_search_most_common")
     */
    public function mostCommon()
    {
        return $this->render('log_search/mostcommon.html.twig', [
            'controller_name' => 'LogSearchController'
        ]);
    }

    /**
     * @Route("/dashboard/search/topblocks", name="log_search_top_blocks")
     */
    public function topBlocks()
    {
        return $this->render('log_search/topblocks.html.twig', [
            'controller_name' => 'LogSearchController'
        ]);
    }

    /**
     * @Route("/dashboard/search/ipslog", name="log_search_source_dest_ip")
     */
    public function sourceordestips()
    {
        return $this->render('log_search/sourcedestips.html.twig', [
            'controller_name' => 'LogSearchController'
        ]);
    }

    /**
     * @Route("/dashboard/addlog/{type}", name="log_add")
     *
     */
    public function addlog($type = NULL)
    {
        if($type == NULL) {
            return $this->render('log_search/addlog.html.twig', [
                'controller_name' => 'LogSearchController'
            ]);
        } else {
            return $this->render('log_search/addlog.html.twig', [
                'controller_name' => 'Added Log'
            ]);
        }
    }
}
