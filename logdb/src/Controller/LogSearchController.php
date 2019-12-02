<?php

namespace App\Controller;

use App\Entity\Actions;
use App\Form\DateSearchAndTypeType;
use App\Form\DateSearchType;
use App\Form\MostCommonType;
use App\Form\SourceDestIpsType;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManager;

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
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws DBALException
     */
    public function perTypeLogs(Request $request, EntityManagerInterface $entityManager)
    {

        $form = $this->createForm(DateSearchType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            $task = $form->getData();
            if ($task['startDate']->format('Y-m-d h:i:s') >= $task['endDate']->format('Y-m-d h:i:s')) {
                return $this->render('log_search/pertype.html.twig', [
                    'controller_name' => 'LogSearchController',
                    'errorMessage' => "Start date is later or same day than end date. Please provide at least 1 day difference",
                    'form' => $this->createForm(DateSearchType::class)->createView(),
                    'entity' => array()
                ]);
            }


            $entity = $entityManager->getConnection()
                ->query("SELECT total_logs,log_type FROM total_logs_per_type_time_specified('" . $task['startDate']->format('Y-m-d h:i:s') . "', '" . $task['endDate']->format('Y-m-d h:i:s') . "');")
                ->fetchAll();

//            $action = new Actions();
//            $action->setAction("Code 1: User " . "Thominho 999" . " searched total logs per type from " . $task['startDate']->format('Y-m-d h:i:s') . " to " . $task['endDate']->format('Y-m-d h:i:s'));
//            $action->setUserId($this->getUser());
//
//            $em->getEntityManager()->persist($action);
//            $em->getEntityManager()->flush();

            return $this->render('log_search/pertype.html.twig', [
                'controller_name' => 'LogSearchController',
                'form' => $form->createView(),
                'entity' => $entity
            ]);
        }

        return $this->render('log_search/pertype.html.twig', [
            'controller_name' => 'LogSearchController',
            'form' => $form->createView(),
            'entity' => array()
        ]);
    }

    /**
     * @Route("/dashboard/search/mostcommon", name="log_search_most_common")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws DBALException
     */
    public function mostCommon(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(MostCommonType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            $task = $form->getData();

            $entity = $entityManager->getConnection()
                ->query("SELECT source_ip,type,max_count FROM most_common_log_per_source_ip_time_specified('" . $task['entryDate']->format('Y-m-d h:i:s') . "');")
                ->fetchAll();

//            $action = new Actions();
//            $action->setAction("Code 1: User " . "Thominho 999" . " searched total logs per type from " . $task['startDate']->format('Y-m-d h:i:s') . " to " . $task['endDate']->format('Y-m-d h:i:s'));
//            $action->setUserId($this->getUser());
//
//            $em->getEntityManager()->persist($action);
//            $em->getEntityManager()->flush();

            return $this->render('log_search/mostcommon.html.twig', [
                'controller_name' => 'LogSearchController',
                'form' => $form->createView(),
                'entity' => $entity
            ]);
        }

        return $this->render('log_search/mostcommon.html.twig', [
            'controller_name' => 'LogSearchController',
            'form' => $form->createView(),
            'entity' => array()
        ]);
    }

    /**
     * @Route("/dashboard/search/typeperday", name="log_search_type_per_day")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws DBALException
     */
    public function typeperday(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(DateSearchAndTypeType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            if ($task['startDate']->format('Y-m-d h:i:s') >= $task['endDate']->format('Y-m-d h:i:s')) {
                return $this->render('log_search/typeperday.html.twig', [
                    'controller_name' => 'LogSearchController',
                    'errorMessage' => "Start date is later or same day than end date. Please provide at least 1 day difference",
                    'form' => $this->createForm(DateSearchAndTypeType::class)->createView(),
                    'entity' => array()
                ]);
            }

            $entity = $entityManager->getConnection()
                ->query("SELECT total_logs,log_insert_date FROM total_type_logs_per_day_time_specified('" . $task['logType'] . "', '" . $task['startDate']->format('Y-m-d h:i:s') . "', '" . $task['endDate']->format('Y-m-d h:i:s') . "');")
                ->fetchAll();

//            $action = new Actions();
//            $action->setAction("Code 1: User " . "Thominho 999" . " searched total logs per type from " . $task['startDate']->format('Y-m-d h:i:s') . " to " . $task['endDate']->format('Y-m-d h:i:s'));
//            $action->setUserId($this->getUser());
//
//            $em->getEntityManager()->persist($action);
//            $em->getEntityManager()->flush();

            return $this->render('log_search/typeperday.html.twig', [
                'controller_name' => 'LogSearchController',
                'form' => $form->createView(),
                'entity' => $entity
            ]);
        }

        return $this->render('log_search/typeperday.html.twig', [
            'controller_name' => 'LogSearchController',
            'form' => $form->createView(),
            'entity' => array()
        ]);
    }

    /**
     * @Route("/dashboard/search/ipslog", name="log_search_source_dest_ip")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws DBALException
     */
    public function sourceordestips(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SourceDestIpsType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            if (empty($task['sourceIps'])) {
                $task['sourceIps'] = $task['destinationIps'];
            } elseif (empty($task['destinationIps'])) {
                $task['destinationIps'] = $task['sourceIps'];
            } elseif (empty($task['destinationIps']) && empty($task['sourceIps'])) {
                return $this->render('log_search/sourcedestips.html.twig', [
                    'controller_name' => 'LogSearchController',
                    'form' => $form->createView(),
                    'error' => "You should provide at least one of two: Source or Destination Ip",
                    'entity' => array()
                ]);
            }

            $entity = $entityManager->getConnection()
                ->query("SELECT log_id ,source_ip, dest_ip , insert_date , access_id , access_logger_id_id , method , requested_resource , response_status , response_size , referrer , user_agent , hdfs_id , hdfs_logger_id , type , size , block_id , block_number FROM search_logs_by_source_or_destination_ip('" . $task['sourceIps'] . "', '" . $task['destinationIps'] . "');")
                ->fetchAll();
//            $action = new Actions();
//            $action->setAction("Code 1: User " . "Thominho 999" . " searched total logs per type from " . $task['startDate']->format('Y-m-d h:i:s') . " to " . $task['endDate']->format('Y-m-d h:i:s'));
//            $action->setUserId($this->getUser());
//
//            $em->getEntityManager()->persist($action);
//            $em->getEntityManager()->flush();

            return $this->render('log_search/sourcedestips.html.twig', [
                'controller_name' => 'LogSearchController',
                'form' => $form->createView(),
                'entity' => $entity
            ]);
        }

        return $this->render('log_search/sourcedestips.html.twig', [
            'controller_name' => 'LogSearchController',
            'form' => $form->createView(),
            'entity' => array()
        ]);
    }

    /**
     * @Route("/dashboard/addlog", name="log_add")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     * @throws DBALException
     */
    public function addlog(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(SourceDestIpsType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            if ($task['startDate']->format('Y-m-d h:i:s') >= $task['endDate']->format('Y-m-d h:i:s')) {
                return $this->render('log_search/pertype.html.twig', [
                    'controller_name' => 'LogSearchController',
                    'errorMessage' => "Start date is later or same day than end date. Please provide at least 1 day difference",
                    'form' => $this->createForm(SourceDestIpsType::class)->createView(),
                    'entity' => array()
                ]);
            }


            $entity = $entityManager->getConnection()
                ->query("SELECT total_logs,log_type FROM total_logs_per_type_time_specified(" . $task['logType'] . " '" . $task['startDate']->format('Y-m-d h:i:s') . "', '" . $task['endDate']->format('Y-m-d h:i:s') . "');")
                ->fetchAll();

//            $action = new Actions();
//            $action->setAction("Code 1: User " . "Thominho 999" . " searched total logs per type from " . $task['startDate']->format('Y-m-d h:i:s') . " to " . $task['endDate']->format('Y-m-d h:i:s'));
//            $action->setUserId($this->getUser());
//
//            $em->getEntityManager()->persist($action);
//            $em->getEntityManager()->flush();

            return $this->render('log_search/sourcedestips.html.twig', [
                'controller_name' => 'LogSearchController',
                'form' => $form->createView(),
                'entity' => $entity
            ]);
        }

        return $this->render('log_search/sourcedestips.html.twig', [
            'controller_name' => 'LogSearchController',
            'form' => $form->createView(),
            'entity' => array()
        ]);
    }
}
