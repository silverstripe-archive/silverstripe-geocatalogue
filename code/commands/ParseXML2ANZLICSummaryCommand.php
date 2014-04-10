<?php
/**
 * Created by PhpStorm.
 * User: Rainer Spittel
 * Date: 10/04/14
 * Time: 11:25 AM
 */
class ParseXML2ANZLICSummaryCommand  extends ParseXMLCommand {

    /**
     * @param $doc
     * @return array
     */
    public function parseDocument($doc) {
		$mdArray = array();

		$xpath = new DOMXPath($doc);
	    $xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");
		$xpath->registerNamespace("gmd", "http://www.isotc211.org/2005/gmd");
		$xpath->registerNamespace("gco", "http://www.isotc211.org/2005/gco");

        $responseDOM = $xpath->query('/csw:GetRecordsResponse');

	    $SearchResults = $xpath->query('csw:SearchResults',$responseDOM->item(0));

	    $searchResultItem = $SearchResults->item(0);
	    $numberOfRecordsMatched = $searchResultItem->attributes->getNamedItem('numberOfRecordsMatched')->nodeValue;
	    $numberOfRecordsReturned = $searchResultItem->attributes->getNamedItem('numberOfRecordsReturned')->nodeValue;
	    $nextRecord = $searchResultItem->attributes->getNamedItem('nextRecord')->nodeValue;

	    $metadataList = $xpath->query('gmd:MD_Metadata',$searchResultItem);
        foreach($metadataList as $metadata) {
			$mdItem = array();

	        $item = $xpath->query('gmd:fileIdentifier/gco:CharacterString',$metadata);
	        if ($item->length > 0) {
				$mdItem['fileIdentifier'] = $item->item(0)->nodeValue;
			}

	        $item = $xpath->query('gmd:metadataStandardName/gco:CharacterString',$metadata);
	        if ($item->length > 0) {
				$mdItem['metadataStandardName'] = $item->item(0)->nodeValue;
			}

	        $item = $xpath->query('gmd:metadataStandardVersion/gco:CharacterString',$metadata);
	        if ($item->length > 0) {
				$mdItem['metadataStandardVersion'] = $item->item(0)->nodeValue;
			}
	        $item = $xpath->query('gmd:parentIdentifier/gco:CharacterString',$metadata);
	        if ($item->length > 0) {
				$mdItem['parentIdentifier'] = $item->item(0)->nodeValue;
			}

	        $item = $xpath->query('gmd:hierarchyLevel/gmd:MD_ScopeCode',$metadata);
	        if ($item->length > 0) {
				$mdItem['hierarchyLevel'] = $item->item(0)->nodeValue;
			}

	        $item = $xpath->query('gmd:hierarchyLevelName/gco:CharacterString',$metadata);
	        if ($item->length > 0) {
				$mdItem['hierarchyLevelName'] = $item->item(0)->nodeValue;
			}

	        $xmlDataIdentificationList = $xpath->query('gmd:identificationInfo/gmd:MD_DataIdentification',$metadata);
            foreach ($xmlDataIdentificationList as $dataIdentification) {

	            $item = $xpath->query('gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString',$dataIdentification);
	   	        if ($item->length > 0) {
	   				$mdItem['MDTitle'] = $item->item(0)->nodeValue;
	   			}

	            $item = $xpath->query('gmd:abstract/gco:CharacterString',$dataIdentification);
	   	        if ($item->length > 0) {
	   				$mdItem['MDAbstract'] = stripslashes($item->item(0)->nodeValue);
	   			}

	            $item = $xpath->query('gmd:topicCategory/gmd:MD_TopicCategoryCode',$dataIdentification);
	   	        if ($item->length > 0) {
	   				$mdItem['MDTopicCategory'] = $item->item(0)->nodeValue;
	   			}

	            $extendList = $xpath->query('gmd:extent/gmd:EX_Extent',$dataIdentification);
	            foreach ($extendList as $extendNode) {

		            $item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal',$extendNode);
		            if ($item->length > 0) {
		   				$mdItem['MDWestBound'] = $item->item(0)->nodeValue;
		   			}

		            $item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal',$extendNode);
		            if ($item->length > 0) {
		   				$mdItem['MDEastBound'] = $item->item(0)->nodeValue;
		   			}

		            $item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal',$extendNode);
		            if ($item->length > 0) {
		   				$mdItem['MDSouthBound'] = $item->item(0)->nodeValue;
		   			}

		            $item = $xpath->query('gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal',$extendNode);
		            if ($item->length > 0) {
		   				$mdItem['MDNorthBound'] = $item->item(0)->nodeValue;
		   			}

	            }
            }
	        $mdArray[] = $mdItem;
        }
        return array($numberOfRecordsMatched, $numberOfRecordsReturned, $nextRecord, $mdArray);
    }



}