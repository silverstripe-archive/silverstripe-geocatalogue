<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rainer
 * Date: 4/12/13
 * Time: 9:43 AM
 * To change this template use File | Settings | File Templates.
 */

class Catalogue_QueryClass {

    public $query = array('searchTerm' => '',
                          'startPosition' => 1,
                          'sortBy' => 'title',
                          'sortOrder' => 'ASC',
                          'bboxUpper' => false,
                          'bboxLower' => false);

    public function __construct($params) {
        if (isset($params['searchTerm'])) $this->query['searchTerm'] = $params['searchTerm'];
        if (isset($params['startPosition'])) $this->query['startPosition'] = $params['startPosition'];
        if (isset($params['sortBy'])) $this->query['sortBy'] = $params['sortBy'];
        if (isset($params['sortOrder'])) $this->query['sortOrder'] = $params['sortOrder'];
        if (isset($params['bboxUpper'])) $this->query['bboxUpper'] = $params['bboxUpper'];
        if (isset($params['bboxLower'])) $this->query['bboxLower'] = $params['bboxLower'];
    }

    public function get($field) {
        return $this->query[$field];
    }

    public function validate() {
        if (!$this->query['searchTerm']) {
            if (!($this->query['bboxUpper'] && $this->query['bboxLower'])) {
                throw new CataloguePage_Exception('Search term is missing. Please enter a query.');
            }
        }
    }
}