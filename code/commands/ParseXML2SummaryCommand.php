<?php
/**
 * Created by PhpStorm.
 * User: Rainer Spittel
 * Date: 10/04/14
 * Time: 11:25 AM
 */
class ParseXML2SummaryCommand  extends ParseXMLCommand {

    /**
     * @param $doc
     * @return array
     */
    public function parseDocument(DOMDocument $doc) {
		$mdArray = array();

		$xpath = new DOMXPath($doc);
	    $xpath->registerNamespace("mcp", "http://bluenet3.antcrc.utas.edu.au/mcp");
	    $xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$xpath->registerNamespace("gmd", "http://www.isotc211.org/2005/gmd");
		$xpath->registerNamespace("gco", "http://www.isotc211.org/2005/gco");

        $responseDOM = $xpath->query('/csw:GetRecordsResponse');

	    if($responseDOM->length == 0) {
		    throw new ParseXMLCommand_Exception('Invalid response, contains no GetRecordsResponse node.');
	    }
	    $searchResultList = $xpath->query('csw:SearchResults',$responseDOM->item(0));

	    $searchResultItem = $searchResultList->item(0);
	    $numberOfRecordsMatched = $searchResultItem->attributes->getNamedItem('numberOfRecordsMatched')->nodeValue;
	    $numberOfRecordsReturned = $searchResultItem->attributes->getNamedItem('numberOfRecordsReturned')->nodeValue;
	    $nextRecord = $searchResultItem->attributes->getNamedItem('nextRecord')->nodeValue;

	    $metadataList = $searchResultItem->childNodes;
        foreach($metadataList as $metadata) {

	        if ($metadata->nodeName == 'gmd:MD_Metadata') {
		        $mdItem = $this->parseANZLIC($xpath, $metadata);
		        $mdArray[] = $mdItem;
	        } else
	        if ($metadata->nodeName == 'mcp:MD_Metadata') {
		        $mdItem = $this->parseMCP($xpath, $metadata);
		        $mdArray[] = $mdItem;
	        }

        }
//	    echo "<pre>";
//        print_r($mdArray);
//        echo "</pre>";
//		die();
        return array($numberOfRecordsMatched, $numberOfRecordsReturned, $nextRecord, $mdArray);
    }
	/**
	 * @param $xpath
	 * @param $metadata
	 *
	 * @return array
	 */
	public function parseMCP($xpath, $metadata) {
		$mdItem = array();

		$item = $xpath->query('gmd:fileIdentifier/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['fileIdentifier'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:metadataStandardName/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['metadataStandardName'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:metadataStandardVersion/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['metadataStandardVersion'] = $item->item(0)->nodeValue;
		}
		$item = $xpath->query('gmd:parentIdentifier/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['parentIdentifier'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:hierarchyLevel/gmd:MD_ScopeCode', $metadata);
		if($item->length > 0) {
			$mdItem['hierarchyLevel'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:hierarchyLevelName/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['hierarchyLevelName'] = $item->item(0)->nodeValue;
		}

		$xmlDataIdentificationList = $xpath->query('gmd:identificationInfo/mcp:MD_DataIdentification', $metadata);
		foreach($xmlDataIdentificationList as $dataIdentification) {

			$item = $xpath->query('gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString', $dataIdentification);
			if($item->length > 0) {
				$mdItem['MDTitle'] = $item->item(0)->nodeValue;
			}

			$item = $xpath->query('gmd:abstract/gco:CharacterString', $dataIdentification);
			if($item->length > 0) {
				$mdItem['MDAbstract'] = stripslashes($item->item(0)->nodeValue);
			}

			$item = $xpath->query('gmd:topicCategory/gmd:MD_TopicCategoryCode', $dataIdentification);
			if($item->length > 0) {
				$mdItem['MDTopicCategory'] = $item->item(0)->nodeValue;
			}

			$extendList = $xpath->query('gmd:extent/gmd:EX_Extent', $dataIdentification);
			foreach($extendList as $extendNode) {

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDWestBound'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDEastBound'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDSouthBound'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDNorthBound'] = $item->item(0)->nodeValue;
				}
			}
		}
		return $mdItem;
	}

	/**
	 * @param $xpath
	 * @param $metadata
	 *
	 * @return array
	 */
	public function parseANZLIC($xpath, $metadata) {
		$mdItem = array();

		$item = $xpath->query('gmd:fileIdentifier/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['fileIdentifier'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:metadataStandardName/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['metadataStandardName'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:metadataStandardVersion/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['metadataStandardVersion'] = $item->item(0)->nodeValue;
		}
		$item = $xpath->query('gmd:parentIdentifier/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['parentIdentifier'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:hierarchyLevel/gmd:MD_ScopeCode', $metadata);
		if($item->length > 0) {
			$mdItem['hierarchyLevel'] = $item->item(0)->nodeValue;
		}

		$item = $xpath->query('gmd:hierarchyLevelName/gco:CharacterString', $metadata);
		if($item->length > 0) {
			$mdItem['hierarchyLevelName'] = $item->item(0)->nodeValue;
		}

		$xmlDataIdentificationList = $xpath->query('gmd:identificationInfo/gmd:MD_DataIdentification', $metadata);
		foreach($xmlDataIdentificationList as $dataIdentification) {

			$item = $xpath->query('gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString', $dataIdentification);
			if($item->length > 0) {
				$mdItem['MDTitle'] = $item->item(0)->nodeValue;
			}

			$item = $xpath->query('gmd:abstract/gco:CharacterString', $dataIdentification);
			if($item->length > 0) {
				$mdItem['MDAbstract'] = stripslashes($item->item(0)->nodeValue);
			}

			$item = $xpath->query('gmd:topicCategory/gmd:MD_TopicCategoryCode', $dataIdentification);
			if($item->length > 0) {
				$mdItem['MDTopicCategory'] = $item->item(0)->nodeValue;
			}

			$extendList = $xpath->query('gmd:extent/gmd:EX_Extent', $dataIdentification);
			foreach($extendList as $extendNode) {

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDWestBound'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDEastBound'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDSouthBound'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal', $extendNode);
				if($item->length > 0) {
					$mdItem['MDNorthBound'] = $item->item(0)->nodeValue;
				}
			}
		}
		return $mdItem;
	}
}

class ParseXML2SummaryCommand_Exception extends Exception {}
