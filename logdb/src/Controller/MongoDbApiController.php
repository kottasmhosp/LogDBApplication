<?php

namespace App\Controller;

use App\Document\Log;
use Doctrine\ODM\MongoDB\DocumentManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MongoDbApiController extends AbstractController
{

    private $validHttpMethods = array(
        "GET",
        "HEAD",
        "POST",
        "PUT",
        "DELETE",
        "CONNECT",
        "OPTIONS",
        "TRACE",
        "PATCH"
    );
    private $dm;

    public function __construct(DocumentManager $documentManager)
    {
        $this->dm = $documentManager;
    }

    /**
     * @Route("/api/db/check_connection", name="mongo_db_api")
     */
    public function index()
    {
        return new Response(
            json_encode("This user is logged in and asking for MongoDB"),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Query 1 : Find the total logs per type that were created within a specified time range
     * and sort them ina descending order.
     * Please note that individual files may log actions of more than one type.
     *
     * @Route("/api/db/logs/pertyp/timerange", name="mongo_db_total_logs_per_type_time_range")
     * @param Request $request
     * @return Response
     */
    public function total_logs_per_type_time_range(Request $request)
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 2 : Find the number of total requests per day for a specific log type and time range.
     *
     * @Route("/api/db/log/perday/timetype", name="mongo_db_total_logs_per_day_time_type")
     * @param Request $request
     * @return Response
     */
    public function total_logs_per_day_time_type(Request $request)
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 3 : Find the three most common logs
     * per source IP for a specific day.
     *
     * @Route("/api/db/logs/sourceip/threemostcommon", name="mongo_db_three_most_common_logs")
     */
    public function three_most_common_logs()
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 4 : Find the two least common HTTP methods
     * with regards to a given time range.
     *
     * @Route("/api/db/methods/twoleastcommon", name="mongo_db_two_least_common_methods")
     */
    public function two_least_common_http_methods()
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 5 : Find the referrers (if any) that
     * have led to more than one resources.
     *
     * @Route("/api/db/referrers/repeaters", name="mongo_db_referrers_repeeaters")
     */
    public function referrers_repeaters()
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 6 : Find the blocks that have been replicated
     * the same day that they have also been served.
     *
     * @Route("/api/db/block/sameday/replicatedserved", name="mongo_db_sameday_replicated_served")
     */
    public function blocks_replicated_served_same_day()
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 7 : Find the fifty most upvoted logs for a specific day.
     *
     * @Route("/api/db/vote/mostupvoted/topfifty", name="mongo_db_fifty_most_upvoted")
     * @param Request $request
     * @return Response
     */
    public function fifty_most_up_voted(Request $request)
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 8 : Find the fifty most active administrators,
     * with regard to the total number of upvotes.
     *
     * @Route("/api/db/api", name="mongo_db_api")
     */
    public function total_logs_per_type_in_time_range()
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 9 : Find the top fifty administrators,
     * with regard to the total number of source IPs
     * for which they have upvoted logs.
     *
     * @Route("/api/db/admin/sourceip/topfifty", name="mongo_db_admin_admin_sourceip_top_fifty")
     */
    public function top_fifty_administrators_total_number_of_source_ips()
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 10 : Find all logs for which the same e-mail has been used
     * for more than one usernames when casting an upvote.
     *
     * @Route("/api/db/admin/vote/duplicates", name="mongo_db_duplicate_admin_votes")
     */
    public function votes_from_duplicate_admin()
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 11 : Find all the block ids for
     * which a given name has casted a vote for a log involving it.
     *
     * @Route("/api/db/api/block/all/vote/byname", name="mongo_db_block_byname")
     * @param Request $request
     * @return Response
     */
    public function block_ids_by_name_voted(Request $request)
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     * Query 12 : Add new log
     *
     * @Route("/api/db/addlog", name="mongo_db_addlog")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function newLog(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $errorMsg = $this->checkPayloadByType($payload);

        if (!empty($errorMsg)) {
            return new Response(
                json_encode($errorMsg),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }

        if ($payload['log'] == 'hdfs') {
            foreach ($payload['destIps'] as $destIp) {
                $log = new Log();
                $log->setSourceIp($payload['sourceIp']);
                $insertDate = '';
                if (!empty($payload['insertDate'])) {
                    $insertDate = \DateTime::createFromFormat("Y-m-d", $payload['insertDate']);
                } else {
                    $insertDate = new \DateTime();
                }
                $log->setInsertDate($insertDate);
                $log->setType($payload['type']);
                $log->setBlock($payload['blocks']);
                $log->setDestIp($destIp);
                $log->setSize(4 * count($payload['blocks']));
                $this->dm->persist($log);
            }
        } elseif ($payload['log'] == "access") {
            $log = new Log();
            $log->setSourceIp($payload['sourceIp']);
            $log->setDestIp($payload['destIp']);
            $insertDate = '';
            if (!empty($payload['insertDate'])) {
                $insertDate = \DateTime::createFromFormat("Y-m-d", $payload['insertDate']);
            } else {
                $insertDate = new \DateTime();
            }
            $log->setInsertDate($insertDate);
            $log->setReferer($payload['referrer']);
            $log->setResponseSize($payload['responseSize']);
            $log->setMethod($payload['method']);
            $log->setUserAgent($payload["userAgent"]);
            $this->dm->persist($log);
        }

        $this->dm->flush();

        return new Response(
            json_encode("Unrecognized log type"),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Query 13 : Casting an upvote
     *
     * @Route("/api/db/castvote", name="mongo_db_castvote")
     * @param Request $request
     * @return Response
     */
    public function castVote(Request $request)
    {
        return $this->render('mongo_db_api/index.html.twig', [
            'controller_name' => 'MongoDbApiController',
        ]);
    }

    /**
     *
     * Check if log is correct
     *
     * @param array $payload
     * @return string
     */
    private function checkPayloadByType($payload)
    {
        if (empty($payload)) {
            return "Empty Payload";
        }

        if (empty($payload['log'])) {
            return "Log type no defined";
        }

        if ($payload['log'] == 'hdfs') {
            if (empty($payload["sourceIp"])) {
                return "Missing Source Ip";
            } elseif (ip2long($payload["sourceIp"]) == false || ip2long($payload["sourceIp"]) == -1) {
                return "Source ip format is wrong";
            }

            if (empty($payload["destIps"])) {
                return "Missing destination ip. One or more ips are required";
            } else {
                foreach ($payload["destIps"] as $destip) {
                    if (ip2long($destip) == false || ip2long($destip) == -1) {
                        return "Destination ip '" . $destip . "' format is wrong";
                    }
                }
            }

            if (empty($payload["type"])) {
                return "Missing HDFS log type.";
            }

            if (empty($payload["blocks"])) {
                return "Missing block number";
            } else {
                foreach ($payload["blocks"] as $block) {
                    if (intval($block) == 0) {
                        return "Block number is not an integer or 0";
                    }
                }
            }

        } elseif ($payload['log'] == "access") {
            if (empty($payload["sourceIp"])) {
                return "Missing source ip";
            } elseif ((ip2long($payload["sourceIp"]) == false || ip2long($payload["sourceIp"]) == -1) && $payload["sourceIp"] != "-") {
                return "Source ip format is wrong";
            }

            if (empty($payload["destIp"])) {
                return "Missing destination ip";
            } elseif (ip2long($payload["destIp"]) == false || ip2long($payload["destIp"]) == -1) {
                return "Destination ip format is wrong";
            }

            if (empty($payload["requestedResource"])) {
                return "Missing requested resource";
            }

            if (empty($payload["userAgent"])) {
                return "Missing user agent";
            }

            if (empty($payload["referrer"])) {
                return "Missing referrer";
            }

            if (empty($payload["responseSize"])) {
                return "Missing response size";
            } elseif (intval($payload["responseSize"]) == 0) {
                return "Response size is 0 or not an integer";
            }

            if (empty($payload["responseStatus"])) {
                return "Missing response status";
            } else {
                if (intval($payload["responseStatus"]) < 100 || intval($payload["responseStatus"]) > 599) {
                    return "Wrong response status. Status format should be 3 digits integer";
                }
            }

            if (empty($payload["method"])) {
                return "Missing http method";
            } else {
                if (in_array($payload["method"], $this->validHttpMethods) == FALSE) {
                    return $payload["method"] . " method is not valid";
                }
            }
        }

        return "";
    }
}
