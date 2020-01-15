<?php

namespace App\Controller;

use App\Document\Log;
use App\Document\Query1;
use App\Document\Query2;
use App\Document\Query3;
use App\Document\Query4;
use App\Document\Query5;
use App\Document\User;
use App\Document\Vote;
use Doctrine\ODM\MongoDB\Aggregation\Builder;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Exception;
use MongoDB\BSON\UTCDateTime;
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
     * and sort them in a descending order.
     * Please note that individual files may log actions of more than one type.
     *
     * @Route("/api/db/logs/pertype/timerange", name="mongo_db_total_logs_per_type_time_range")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function total_logs_per_type_time_range(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $startDate = '';
        $endDate = '';

        if (empty($payload['startDate'])) {
            return new Response(
                json_encode("Missing "),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } elseif (empty($payload['endDate'])) {
            return new Response(
                json_encode("Missing end date"),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } else {
            $startDate = \DateTime::createFromFormat("Y-m-d", $payload["startDate"]);
            if (empty($startDate)) {
                return new Response(
                    json_encode("Wrong start date format should be : Y-m-d"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

            $endDate = \DateTime::createFromFormat("Y-m-d", $payload["endDate"]);
            if (empty($endDate)) {
                return new Response(
                    json_encode("Wrong end date format should be : Y-m-d"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

            /**
             * Return empty array
             *
             */
            if ($payload["startDate"] > $payload["endDate"]) {
                return new Response(
                    json_encode(array()),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

        }

        /**
         * Use getPipeline to get NoSQL query
         */
        /** @var Builder $resultSql */
        $resultSql = $this->dm->createAggregationBuilder(Log::class)
            ->hydrate(Query1::class)
            ->match()
            ->field('insertDate')
            ->gte(new UTCDateTime(strtotime($startDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
            ->lt(new UTCDateTime(strtotime($endDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
            ->group()
            ->field('id')
            ->expression('$type')
            ->field('count')
            ->sum(1)
            ->sort(array("count" => -1))
            ->execute();

        $response = array();
        foreach ($resultSql as $result) {
            /** @var Query2 $result */
            $response[] = array(
                "type" => $result->getId(),
                "total_number" => $result->getCount()
            );
        }

        return new Response(
            json_encode($response),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
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
        $payload = json_decode($request->getContent(), true);

        $startDate = '';
        $endDate = '';

        if (empty($payload['startDate'])) {
            return new Response(
                json_encode("Missing "),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } elseif (empty($payload['endDate'])) {
            return new Response(
                json_encode("Missing end date"),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } else {
            $startDate = \DateTime::createFromFormat("Y-m-d", $payload["startDate"]);
            if (empty($startDate)) {
                return new Response(
                    json_encode("Wrong start date format should be : Y-m-d"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

            $endDate = \DateTime::createFromFormat("Y-m-d", $payload["endDate"]);
            if (empty($endDate)) {
                return new Response(
                    json_encode("Wrong end date format should be : Y-m-d"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

            /**
             * Return empty array
             *
             */
            if ($payload["startDate"] > $payload["endDate"]) {
                return new Response(
                    json_encode(array()),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

        }

        $resultSql = '';
        if (empty($payload['type'])) {
            /** @var Builder $resultSql */
            $resultSql = $this->dm->createAggregationBuilder(Log::class)
                ->hydrate(Query2::class)
                ->match()
                ->field('insertDate')
                ->gte(new UTCDateTime(strtotime($startDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
                ->lt(new UTCDateTime(strtotime($endDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
                ->field('type')
                ->exists(0)
                ->group()
                ->field('id')
                ->expression(
                    $this->dm->createAggregationBuilder(Log::class)->expr()
                        ->field('type')
                        ->expression('$type')
                        ->field('day')
                        ->dayOfMonth('$insertDate')
                        ->field('month')
                        ->month('$insertDate')
                        ->field('year')
                        ->year('$insertDate')
                )
                ->field('count')
                ->sum(1)
                ->sort(array("count" => -1))
                ->execute();
        } else {
            /** @var Builder $resultSql */
            $resultSql = $this->dm->createAggregationBuilder(Log::class)
                ->hydrate(Query2::class)
                ->match()
                ->field('insertDate')
                ->gte(new UTCDateTime(strtotime($startDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
                ->lt(new UTCDateTime(strtotime($endDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
                ->field('type')
                ->equals($payload['type'])
                ->group()
                ->field('id')
                ->expression(
                    $this->dm->createAggregationBuilder(Log::class)->expr()
                        ->field('type')
                        ->expression('$type')
                        ->field('day')
                        ->dayOfMonth('$insertDate')
                        ->field('month')
                        ->month('$insertDate')
                        ->field('year')
                        ->year('$insertDate')
                )
                ->field('count')
                ->sum(1)
                ->execute();
        }

        $response = array();
        foreach ($resultSql as $result) {
            /** @var Query2 $result */
            $response[] = array(
                "timestamp" => $result->getId()['day'] . "-" . $result->getId()['month'] . "-" . $result->getId()['year'],
                "total_number" => $result->getCount()
            );
        }

        return new Response(
            json_encode($response),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Query 3 : Find the three most common logs
     * per source IP for a specific day.
     *
     * @Route("/api/db/logs/sourceip/threemostcommon", name="mongo_db_three_most_common_logs")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function three_most_common_logs(Request $request)
    {

        $payload = json_decode($request->getContent(), true);

        $searchDate = '';

        if (empty($payload['searchDate'])) {
            return new Response(
                json_encode("Missing search date"),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } else {
            $searchDate = \DateTime::createFromFormat("Y-m-d", $payload["searchDate"]);
            if (empty($searchDate)) {
                return new Response(
                    json_encode("Wrong start date format should be : Y-m-d"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }
        }

        $endDate = \DateTime::createFromFormat('Y-m-d\T00:00:00.00\Z', $searchDate->format("Y-m-d\T00:00:00.00\Z"));
        $endDate->add(new \DateInterval('P1D'));
        /** @var Builder $aggregator */
        $aggregator = $this->dm->createAggregationBuilder(Log::class);

        /**
         * Match only this day
         */
        $aggregator = $aggregator
            ->hydrate(Query3::class)
            ->match()
            ->field('insertDate')
            ->gte(new UTCDateTime(strtotime($searchDate->format("Y-m-d\T00:00:00.00\Z")) * 1000))
            ->lt(new UTCDateTime(strtotime($endDate->format("Y-m-d\T00:00:00.00\Z")) * 1000));

        /**
         * Count all groups per source ip
         * and type to create the extra column
         * typeCount and sort it descending
         */
        $aggregator = $aggregator
            ->group()
            ->field('id')
            ->expression(
                $this->dm->createAggregationBuilder(Log::class)
                    ->expr()
                    ->field('type')
                    ->expression('$type')
                    ->field('sourceIp')
                    ->expression('$sourceIp')
            )
            ->field('typeCount')
            ->sum(1)
            ->sort(array("typeCount" => -1));

        /**
         * Group by source Ip and push for each
         * group all typeCount and type from
         * previous stage
         *
         */
        $aggregator = $aggregator
            ->group()
            ->field('id')
            ->expression(
                $this->dm->createAggregationBuilder(Log::class)
                    ->expr()
                    ->field('sourceIp')
                    ->expression('$_id.sourceIp')
            )
            ->field('total_types_per_sourceIp')
            ->push(
                array(
                    'type' => '$_id.type',
                    'count' => '$typeCount'
                )
            )
            ->field('count')
            ->sum('$typeCount');

        /**
         * Project only 3
         * As we have order descending
         * projecting only three will project top3
         */
        $aggregator = $aggregator->project()
            ->field('total_types_per_sourceIp')
            ->slice('$total_types_per_sourceIp', 3);

        /** @var Builder $resultSql */
        $resultSql = $aggregator
            ->execute();


        $response = array();

        /** @var Query3 $result */
        foreach ($resultSql as $result) {
            $responsePayload = array();
            $responsePayload['sourceIp'] = $result->getId();
            $responsePayload['top3'] = $result->getTotalTypesPerSourceIp();
            $response[] = $responsePayload;
        }

        return new Response(
            json_encode($response),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Query 4 : Find the two least common HTTP methods
     * with regards to a given time range.
     *
     * @Route("/api/db/methods/twoleastcommon", name="mongo_db_two_least_common_methods")
     * @param Request $request
     * @return Response
     */
    public function two_least_common_http_methods(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        $startDate = '';
        $endDate = '';

        if (empty($payload['startDate'])) {
            return new Response(
                json_encode("Missing start date"),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } elseif (empty($payload['endDate'])) {
            return new Response(
                json_encode("Missing end date"),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } else {
            $startDate = \DateTime::createFromFormat("Y-m-d", $payload["startDate"]);
            if (empty($startDate)) {
                return new Response(
                    json_encode("Wrong start date format should be : Y-m-d"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

            $endDate = \DateTime::createFromFormat("Y-m-d", $payload["endDate"]);
            if (empty($endDate)) {
                return new Response(
                    json_encode("Wrong end date format should be : Y-m-d"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

            /**
             * Return empty array
             *
             */
            if ($payload["startDate"] > $payload["endDate"]) {
                return new Response(
                    json_encode(array()),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

        }

        /**
         * Use getPipeline to get NoSQL query
         * Only access logs
         */
        $aggregator = $this->dm->createAggregationBuilder(Log::class)
            ->hydrate(Query4::class)
            ->match()
            ->field('insertDate')
            ->gte(new UTCDateTime(strtotime($startDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
            ->lt(new UTCDateTime(strtotime($endDate->format("Y-m-d\TH:i:s.v\Z")) * 1000))
            ->field('type')
            ->exists(0);

        /**
         * Group by HTTP methods
         * and sort ascending
         */
        $aggregator = $aggregator
            ->group()
            ->field('id')
            ->expression('$method')
            ->field('count')
            ->sum(1)
            ->sort(array("count" => 1));

        /**
         * limit results to 2
         */
        /** @var Builder $resultSql */
        $resultSql = $aggregator
            ->limit(2)
            ->execute();

        $response = array();
        /** @var Query4 $result */
        foreach ($resultSql as $result) {
            $response[] = array(
                "HttpMethod" => $result->getId(),
                "Total" => $result->getCount()
            );
        }

        return new Response(
            json_encode($response),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * Query 5 : Find the referrers (if any) that
     * have led to more than one resources.
     * DISTINCT RESOURCES
     *
     * @Route("/api/db/referrers/repeaters", name="mongo_db_referrers_repeeaters")
     */
    public function referrers_repeaters()
    {

        /**
         * Count all groups of referer
         * and requested resource
         */
        $aggregator = $this->dm->createAggregationBuilder(Log::class)
            ->hydrate(Query5::class)
            ->group()
            ->field('id')
            ->expression(
                $this->dm->createAggregationBuilder(Log::class)
                    ->expr()
                    ->field('referer')
                    ->expression('$referer')
                    ->field('requestedResource')
                    ->expression('$requestedResource')
            )
            ->field('count')
            ->sum(1);

        /**
         * Count distinct value for each group
         */
        $aggregator = $aggregator
            ->group()
            ->field('id')
            ->expression(
                $this->dm->createAggregationBuilder(Log::class)
                    ->expr()
                    ->field('referer')
                    ->expression('$_id.referer')
            )
            ->field('totalCount')
            ->sum('$count')
            ->field("distinctValues")
            ->sum(1)
            ->match()
            ->field('distinctValues')
            ->gt(1);

        /** @var Builder $resultSql */
        $resultSql = $aggregator
            ->execute();

        $response = array();
        foreach ($resultSql as $result) {
            /** @var Query5 $result */
            $response[] = array(
                "referer" => $result->getId(),
                "distinctValues" => $result->getDistinctValues()
            );
        }

        return new Response(
            json_encode($response),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
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
        $payload = json_decode($request->getContent(), true);

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
        $payload = json_decode($request->getContent(), true);

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
                $this->dm->flush();

                return new Response(
                    json_encode("Successfully inserted HDFS log"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
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
            $log->setBlockNull();
            $this->dm->persist($log);
            $this->dm->flush();

            return new Response(
                json_encode("Successfully inserted Access log"),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }

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
     * @throws MongoDBException
     */
    public function castVote(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        $payload = json_decode($request->getContent(), true);

        $logExists = $this->dm->createQueryBuilder(Log::class)
            ->hydrate(true)
            ->field("id")
            ->equals($payload["logId"])
            ->count()
            ->getQuery()
            ->execute();

        if ($logExists == 0) {
            return new Response(
                json_encode("Log with id " . $payload["logId"] . " do not exist"),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }

        $voteExists = $this->dm->createQueryBuilder(Vote::class)
            ->hydrate(true)
            ->field("log_id")
            ->equals($payload["logId"])
            ->count()
            ->getQuery()
            ->execute();

        if ($voteExists == 0) {
            $vote = new Vote();
            $vote->setLogId($payload["logId"]);
            $vote->addAdminId($user->getId());
            $this->dm->persist($vote);
            $this->dm->flush();
        } else {
            $hasVoted = $this->dm->createQueryBuilder(Vote::class)
                ->field("log_id")
                ->equals($payload["logId"])
                ->field("admin_ids")
                ->equals($user->getId())
                ->count()
                ->getQuery()
                ->execute();

            if ($hasVoted == 0) {
                $hasVoted->addAdminId($user->getId());
                $this->dm->persist($hasVoted);
                $this->dm->flush();
            } else {
                return new Response(
                    json_encode("User " . $user->getUsername() . " has casted a vote to log id " . $payload["logId"] . " again"),
                    Response::HTTP_OK,
                    ['content-type' => 'application/json']
                );
            }

        }

        return new Response(
            json_encode("Successfully casted a vote to log id " . $payload["logId"]),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
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
