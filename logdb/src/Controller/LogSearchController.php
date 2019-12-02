<?php

namespace App\Controller;

use App\Entity\AccessLog;
use App\Entity\Actions;
use App\Entity\Block;
use App\Entity\HdfsLog;
use App\Entity\Logger;
use App\Entity\User;
use App\Form\AddLogType;
use App\Form\DateSearchAndTypeType;
use App\Form\DateSearchType;
use App\Form\MostCommonType;
use App\Form\SourceDestIpsType;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper;
use Exception;
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
                $this->addFlash('error', "Start date must be one or more days before end date. Please provide at least 1 day difference");
                return $this->render('log_search/pertype.html.twig', [
                    'controller_name' => 'LogSearchController',
                    'form' => $this->createForm(DateSearchType::class)->createView(),
                    'entity' => array()
                ]);
            }

            $entity = $entityManager->getConnection()
                ->query("SELECT total_logs,log_type FROM total_logs_per_type_time_specified('" . $task['startDate']->format('Y-m-d h:i:s') . "', '" . $task['endDate']->format('Y-m-d h:i:s') . "');")
                ->fetchAll();

            $user = $this->getUser();
            $action = new Actions();
            $action->setAction("User " . $user->getUsername() . " searched for logs per type from " . $task['startDate']->format('Y-m-d h:i:s') . " to " . $task['endDate']->format('Y-m-d h:i:s') );
            $action->setUserId($user);
            $entityManager->persist($action);
            $entityManager->flush();

            return $this->render('log_search/pertype.html.twig', [
                'controller_name' => 'LogSearchController',
                'form' => $form->createView(),
                'entity' => $entity
            ]);
        }

        return $this->render('log_search/pertype.html.twig', [
            'controller_name' => 'LogSearchController',
            'form' => $form->createView(),
            'entity' => array(),
            'error' => array()
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

            $user = $this->getUser();
            $action = new Actions();
            $action->setAction("User " . $user->getUsername() . " searched for most common logs per ip for day " . $task['entryDate']->format('Y-m-d h:i:s'));
            $action->setUserId($user);
            $entityManager->persist($action);
            $entityManager->flush();

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
                $this->addFlash('error', "Start date must be one or more days before end date. Please provide at least 1 day difference");
                return $this->render('log_search/typeperday.html.twig', [
                    'controller_name' => 'LogSearchController',
                    'form' => $this->createForm(DateSearchAndTypeType::class)->createView(),
                    'entity' => array()
                ]);
            }

            $entity = $entityManager->getConnection()
                ->query("SELECT total_logs,log_insert_date FROM total_type_logs_per_day_time_specified('" . $task['logType'] . "', '" . $task['startDate']->format('Y-m-d h:i:s') . "', '" . $task['endDate']->format('Y-m-d h:i:s') . "');")
                ->fetchAll();

            $user = $this->getUser();
            $action = new Actions();
            $action->setAction("User " . "thomas searched logs for type " . $task['logType'] . " per day starting at " . $task['startDate']->format('Y-m-d h:i:s') . " and ended at" . $task['endDate']->format('Y-m-d h:i:s'));
            $action->setUserId($user);
            $entityManager->persist($action);
            $entityManager->flush();

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
            $user = $this->getUser();
            $action = new Actions();
            $action->setAction("User " . "thomas searched logs for source ip address " . $task['sourceIps'] . " and destination ip address " . $task['destinationIps']);
            $action->setUserId($user);
            $entityManager->persist($action);
            $entityManager->flush();

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
     * @throws Exception
     */
    public function addlog(Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(AddLogType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
//            if (empty($task['insertDate']) || empty($task['size']) || empty($task['type']) || empty($task['insertDate']) || empty($task['insertDate']) ) {
//                return $this->render('log_search/addlog.html.twig', [
//                    'controller_name' => 'LogSearchController',
//                    'errorMessage' => "Start date is later or same day than end date. Please provide at least 1 day difference",
//                    'form' => $this->createForm(AddLogType::class)->createView(),
//                    'entity' => array()
//                ]);
//            }

            $em = $this->getDoctrine()->getManager();
            $now = new \DateTime();
            $user = $this->getUser();
            $actionParameters = "User " . $user->getUsername()  . " added new " . $task['logType'] . " logs per type at " . $now->format('Y-m-d h:i:s');
            if($task['logType'] == 'HDFS'){
                $hdfslog = new HdfsLog();
                $hdfslog->setSize($task['size']);
                $hdfslog->setType($task['type']);
                $actionParameters = $actionParameters . " with size " . $task['size'] . " and type " . $task['type'];
                $actionParameters = $actionParameters . " and Blocks ";
                foreach($task['blockIds'] as $block) {
                    $blk = str_replace("blk_","",$block);
                    $blockId = $em->getRepository("App\Entity\Block")->findOneBy(array("block_number" => $blk));
                    if (empty($blockId)) {
                        $blockId = new Block();
                        $blockId->setBlockNumber($blk);
                        $em->persist($blockId);
                    }
                    $hdfslog->addBlock($blockId);
                    $actionParameters = $actionParameters . " " . $blk;
                }

                $actionParameters = $actionParameters . " from " . $task['sourceIps'] . " to datanode(s) ";
                foreach($task['destinationIps'] as $destinationIp){
                    $log = new Logger();
                    $log->setDestIp($destinationIp);
                    $log->setSourceIp($task['sourceIps']);
                    $log->setInsertDate($task['insertDate']);
                    $log->setHdfsLog($hdfslog);
                    $em->persist($log);
                    $actionParameters = $actionParameters . " " . $destinationIp ;
                }
            } else {
                $actionParameters = "User "
                    . $user->getUsername()
                    . " added new "
                    . $task['logType']
                    . " logs per type at "
                    . $now->format('Y-m-d h:i:s')
                    . " with method " . $task['method']
                    . " and referer " . $task['referer']
                    . " requesting resource " . $task['requested_resource']
                    . " with response size " . $task['response_size']
                    . " and response status " . $task['response_status']
                    . " using user agent " . $task['user_agent']
                ;
                $accessLog = new AccessLog();
                $accessLog->setMethod($task['method']);
                $accessLog->setReferer($task['referer']);
                $accessLog->setRequestedResource($task['requested_resource']);
                $accessLog->setResponseSize(intval($task['response_size']));
                $accessLog->setResponseStatus($task['response_status']);
                $accessLog->setUserAgent($task['user_agent']);

                $actionParameters = $actionParameters . " asking " . $task['sourceIps'] . " to datanode(s) ";
                foreach($task['destinationIps'] as $destinationIp){
                    $log = new Logger();
                    $log->setDestIp($destinationIp);
                    $log->setSourceIp($task['sourceIps']);
                    $log->setInsertDate($task['insertDate']);
                    $log->setAccessLog($accessLog);
                    $em->persist($log);
                    $actionParameters = $actionParameters . " " . $destinationIp ;
                }
            }
            $em->flush();

            $user = $this->getUser();
            $now = new \DateTime();
            $action = new Actions();
            $action->setAction($actionParameters);
            $action->setUserId($user);
            $em->persist($action);
            $em->flush();

            $form = $this->createForm(AddLogType::class);
            $this->addFlash('success', 'Successfully created a ' . $task['logType'] . ' Log!');
            return $this->render('log_search/addlog.html.twig', [
                'controller_name' => 'LogSearchController',
                'form' => $form->createView(),
                'entity' => array(),
                'error' => array()
            ]);
        }

        return $this->render('log_search/addlog.html.twig', [
            'controller_name' => 'LogSearchController',
            'form' => $form->createView(),
            'entity' => array()
        ]);
    }
}
