<?php

namespace MD\Bundle\CMSBundle\Lib;

class Paginator {

    private $pageLimit = 9;
    private $limitStart;
    private $limitEnd;
    private $pageNumber;
    private $totalItems;

    /**
     * paginate results
     *
     * @param int $page
     * @param $limit
     * @return array
     */
    public function __construct($totalItems = 10, $page = 1, $pageLimit = 9) {
        $this->pageNumber = $page;
        $this->totalItems = $totalItems;
        $this->pageLimit = $pageLimit;
        $this->limitStart = (int) ($page - 1) * $this->pageLimit;
        $this->limitEnd = (int) $this->pageLimit;
    }

    /**
     * get limitStart
     *
     * @return int
     */
    public function getLimitStart() {
        return $this->limitStart;
    }

    /**
     * get limitEnd
     *
     * @return int
     */
    public function getLimitEnd() {
        return $this->limitEnd;
    }

    /**
     * get $pageLimit
     *
     * @return int
     */
    public function getPageLimit() {
        return (int) $this->pageLimit;
    }

    public function getPagination() {
        $radius = 2;
        $start = 1;
        $prev = NULL;
        $next = NULL;
        $return = array();
        $pageTotalNumber = ceil($this->totalItems / $this->pageLimit);

        $stop = ( $pageTotalNumber < ( ($radius * 2) + 1) ) ? $pageTotalNumber : ( ($radius * 2) + 1 );
        $pageNumber = $this->pageNumber;
        if ($pageNumber > $radius) {
            $start = $pageNumber - $radius;
            $stop = ( $pageTotalNumber <= ($pageNumber + $radius) ) ? $pageTotalNumber : $pageNumber + $radius;
        }

        if (!is_numeric($pageNumber) || ($pageNumber > $pageTotalNumber || ($pageNumber == 0))) {
            //$pageNumber = 1;
            $next = $pageNumber + 1;
            $prev = $pageNumber - 1;
        }

        if ($pageNumber != 1) {
            $return['fisrt'] = 1;
            $return['prev'] = $pageNumber - 1;
        }

        for ($i = $start; $i <= $stop; $i++) {
            $return['items'][] = $i;
        }

        if ($pageNumber < $pageTotalNumber) {
            $return['next'] = ($pageNumber + 1);
            $return['last'] = $pageTotalNumber;
        }
        $return['totalItems'] = $this->totalItems;
        $return['currentPage'] = $this->pageNumber;
        return $return;
    }

}
