<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rainer
 * Date: 4/12/13
 * Time: 1:46 PM
 * To change this template use File | Settings | File Templates.
 */

class ParseXML2ANZLICCommand extends ParseXMLCommand {

    public function parseDocument(DOMDocument $doc) {
        $numberOfRecordsMatched = 0;
        $numberOfRecordsReturned = 0;
        $nextRecord = 0;
        $mdArray = array();

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace("gmd", "http://www.isotc211.org/2005/gmd");
        $xpath->registerNamespace("gco", "http://www.isotc211.org/2005/gco");
        $xpath->registerNamespace("csw", "http://www.opengis.net/cat/csw/2.0.2");

        $metadataList = $xpath->query('gmd:MD_Metadata');

	    // In case Geonetwork document does not provide a XML envelope, check if the metadata node is a root node.
	    // Used when the website visitor uploads a XML file
	    if ($metadataList->length == 0) {
		    $metadataList = $xpath->query('/gmd:MD_Metadata');
	    }
        foreach($metadataList as $metadata) {
            $mdItem = array();
            if ($xpath->query('gmd:fileIdentifier/gco:CharacterString',$metadata)->length > 0) {
                $mdItem['fileIdentifier'] = $xpath->query('gmd:fileIdentifier/gco:CharacterString',$metadata)->item(0)->nodeValue;
            }

            $element = $metadata->getElementsByTagNameNS('http://www.isotc211.org/2005/gmd', "dateStamp");
            if ($element->length > 0) {

                $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'DateTime');
	            if ($node->length > 0) {
		            $mdItem['dateTimeStamp'] = $node->item(0)->nodeValue;
	            }
	            $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'Date');
		        if ($node->length > 0) {
                    $mdItem['dateStamp'] = $node->item(0)->nodeValue;
                }
            }

            $mdItem['metadataStandardName'] = $xpath->query('gmd:metadataStandardName/gco:CharacterString',$metadata)->item(0)->nodeValue;
            $mdItem['metadataStandardVersion'] = $xpath->query('gmd:metadataStandardVersion/gco:CharacterString',$metadata)->item(0)->nodeValue;

	        $nodeList = $xpath->query('gmd:parentIdentifier/gco:CharacterString',$metadata);
	        if ($nodeList->length > 0) {
		        $mdItem['parentIdentifier'] = $nodeList->item(0)->nodeValue;
	        }

	        $mdHierarchyLevel = array();
	        $nodeList = $xpath->query('gmd:hierarchyLevel/gmd:MD_ScopeCode',$metadata);
			foreach ($nodeList as $node) {
				$mdHierarchyLevelItem = array();
				$mdHierarchyLevelItem['Value'] = $node->attributes->getNamedItem('codeListValue')->nodeValue;
				$mdHierarchyLevel[] = $mdHierarchyLevelItem;
			}
	        $mdItem['MDHierarchyLevel:MDHierarchyLevel'] = $mdHierarchyLevel;

			$mdHierarchyLevelName = array();
	        $nodeList = $xpath->query('gmd:hierarchyLevelName',$metadata);
	        foreach ($nodeList as $node) {
				$mdHierarchyLevelNameItem = array();

		        $item = $xpath->query('gco:CharacterString',$node);
		        if ($item->length > 0) {
					$mdHierarchyLevelNameItem['Value'] = $item->item(0)->nodeValue;
					$mdHierarchyLevelName[] = $mdHierarchyLevelNameItem;
		        }
	        }
			$mdItem['MDHierarchyLevelName:MDHierarchyLevelName'] = $mdHierarchyLevelName;

	        $partyList = $xpath->query('gmd:contact/gmd:CI_ResponsibleParty',$metadata);
            foreach ($partyList as $party) {
				$mdContact = array();

	            $item = $xpath->query('gmd:individualName/gco:CharacterString',$party);
				if ($item->length > 0) {
					$mdContact['MDIndividualName'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:organisationName/gco:CharacterString',$party);
				if ($item->length > 0) {
					$mdContact['MDOrganisationName'] = $item->item(0)->nodeValue;
				}

				$item = $xpath->query('gmd:positionName/gco:CharacterString',$party);
				if ($item->length > 0) {
					$mdContact['MDPositionName'] = $item->item(0)->nodeValue;
				}

				$contact = $xpath->query('gmd:contactInfo/gmd:CI_Contact',$party)->item(0);

				$mdVoice = array();
				$voiceNumberList = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:voice',$contact);
				foreach ($voiceNumberList as $voiceNumber) {
					$voiceNode = $xpath->query('gco:CharacterString',$voiceNumber);
					if ($voiceNode->length > 0) {
						$mdPhoneNumber = array();
						$mdPhoneNumber['Value'] = $xpath->query('gco:CharacterString',$voiceNumber)->item(0)->nodeValue;
						$mdVoice[] = $mdPhoneNumber;
					}
				}
				$mdContact['MDVoice:MDPhoneNumber'] = $mdVoice;

				// NOTE: allows only 1 facsimile number
				if ($xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString',$contact)->item(0)) {
					$mdContact['MDFacsimile'] = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString',$contact)->item(0)->nodeValue;
				}

				// parse gmd:address/gmd:CI_Address
				$addressList = $xpath->query('gmd:address/gmd:CI_Address',$contact);
				foreach ($addressList as $address) {
					if ($xpath->query('gmd:deliveryPoint/gco:CharacterString',$address)->item(0)) {
						$mdContact['MDDeliveryPoint'] = $xpath->query('gmd:deliveryPoint/gco:CharacterString',$address)->item(0)->nodeValue;
					}
					if ($xpath->query('gmd:city/gco:CharacterString',$address)->item(0)) {
						$mdContact['MDCity'] = $xpath->query('gmd:city/gco:CharacterString',$address)->item(0)->nodeValue;
					}
					if ($xpath->query('gmd:administrativeArea/gco:CharacterString',$address)->item(0)) {
						$mdContact['MDAdministrativeArea'] = $xpath->query('gmd:administrativeArea/gco:CharacterString',$address)->item(0)->nodeValue;
					}
					if ($xpath->query('gmd:postalCode/gco:CharacterString',$address)->item(0)) {
						$mdContact['MDPostalCode'] = $xpath->query('gmd:postalCode/gco:CharacterString',$address)->item(0)->nodeValue;
					}
					if ($xpath->query('gmd:country/gco:CharacterString',$address)->item(0)) {
						$mdContact['MDCountry'] = $xpath->query('gmd:country/gco:CharacterString',$address)->item(0)->nodeValue;
					}

					$mdVoice = array();
					$voiceNumberList = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:voice',$contact);
					foreach ($voiceNumberList as $voiceNumber) {
						$voiceNode = $xpath->query('gco:CharacterString',$voiceNumber);
						if ($voiceNode->length > 0) {
							$mdPhoneNumber = array();
							$mdPhoneNumber['Value'] = $xpath->query('gco:CharacterString',$voiceNumber)->item(0)->nodeValue;
							$mdVoice[] = $mdPhoneNumber;
						}
					}
					$mdContact['MDVoice:MDPhoneNumber'] = $mdVoice;

					if ($xpath->query('gmd:electronicMailAddress/gco:CharacterString',$address)->item(0)) {
						$mdContact['MDElectronicMailAddress'] = $xpath->query('gmd:electronicMailAddress/gco:CharacterString',$address)->item(0)->nodeValue;
					}
				}

				// add mdContact object to the contact relationship object
				$mdItem['MDContacts:MDContact'] = $mdContact;
			}

	        // OVERWRITTEN by  gmd:distributionInfo/gmd:MD_Distribution/gmd:resourceFormat/gmd:MD_Format
//	        $list = $xpath->query('gmd:distributionInfo/gmd:MD_Distribution/gmd:distributionFormat/gmd:MD_Format',$metadata);
//	        $mdResourceFormats = array();
//			foreach ($list as $item) {
//				$mdResourceFormat = array();
//				$mdResourceFormat['Name']  = $this->queryNodeValue($xpath,'gmd:name/gco:CharacterString',$item);
//				$mdResourceFormat['Version']  = $this->queryNodeValue($xpath,'gmd:version/gco:CharacterString',$item);
//
//				$mdResourceFormats[] = $mdResourceFormat;
//			}
//			$mdItem['MDResourceFormats:MDResourceFormat'] = $mdResourceFormats;

	        $list = $xpath->query('gmd:distributionInfo/gmd:MD_Distribution/gmd:transferOptions/gmd:MD_DigitalTransferOptions/gmd:onLine/gmd:CI_OnlineResource',$metadata);
			$OnlineResources = array();
			foreach ($list as $item) {
				$ciOnlineResource = array();
				$ciOnlineResource['CIOnlineLinkage']  = $this->queryNodeValue($xpath,'gmd:linkage/gmd:URL',$item);
				$ciOnlineResource['CIOnlineProtocol']  = $this->queryNodeValue($xpath,'gmd:protocol/gco:CharacterString',$item);
				$ciOnlineResource['CIOnlineName']  = $this->queryNodeValue($xpath,'gmd:name/gco:CharacterString',$item);
				$ciOnlineResource['CIOnlineDescription']  = $this->queryNodeValue($xpath,'gmd:description/gco:CharacterString',$item);

				$ciOnlineResource['CIOnlineFunction']  = $this->queryNodeValue($xpath,'gmd:linkage/gmd:function/gmd:CI_OnLineFunctionCode',$item);
				$OnlineResources[] = $ciOnlineResource;
			}
			$mdItem['CIOnlineResources:CIOnlineResource'] = $OnlineResources;

	        $xmlDataIdentificationList = $xpath->query('gmd:identificationInfo/gmd:MD_DataIdentification',$metadata);
            foreach ($xmlDataIdentificationList as $dataIdentification) {
				$mdItem['MDPurpose']  = stripslashes($this->queryNodeValue($xpath,'gmd:purpose/gco:CharacterString',$dataIdentification));
				$mdItem['MDAbstract'] = stripslashes($this->queryNodeValue($xpath,'gmd:abstract/gco:CharacterString',$dataIdentification));
				$mdItem['MDLanguage'] = $this->queryNodeValue($xpath,'gmd:language/gco:CharacterString',$dataIdentification);

				$mdTopicCategory = array();
				$xmlCategoryList = $xpath->query('gmd:topicCategory/gmd:MD_TopicCategoryCode',$dataIdentification);
				foreach ($xmlCategoryList as $category) {
					if (trim($category->nodeValue)) {
						$mdTopicCategoryItem = array();
						$mdTopicCategoryItem['Value'] = trim($category->nodeValue);
						$mdTopicCategory[] = $mdTopicCategoryItem;
					}
				}
				$mdItem['MDTopicCategory:MDTopicCategory'] = $mdTopicCategory;

				$xmlCitationList = $xpath->query('gmd:citation/gmd:CI_Citation',$dataIdentification);
				foreach ($xmlCitationList as $citation) {
					$mdItem['MDTitle'] = $this->queryNodeValue($xpath,'gmd:title/gco:CharacterString',$citation);
					$mdItem['MDEdition'] = $this->queryNodeValue($xpath,'gmd:edition/gco:CharacterString',$citation);

					$mdCitationDates=array();
					$xmlDateList = $xpath->query('gmd:date/gmd:CI_Date',$citation);
					foreach($xmlDateList as $dateItem) {
						$mdCitationDate=array();
						$mdCitationDate['MDDateTime'] = $this->queryNodeValue($xpath,'gmd:date/gco:DateTime',$dateItem);
						$mdCitationDate['MDDate'] = $this->queryNodeValue($xpath,'gmd:date/gco:Date',$dateItem);

						$parse_DateTypeList = $xpath->query('gmd:dateType/gmd:CI_DateTypeCode',$dateItem);
						if ($parse_DateTypeList->length > 0) {
							$mdCitationDate['MDDateType']  = $parse_DateTypeList->item(0)->attributes->getNamedItem('codeListValue')->nodeValue;
						}
						$mdCitationDates[]=$mdCitationDate;
					}
					$mdItem['MDCitationDates:MDCitationDate'] = $mdCitationDates;
					$mdItem['MDPresentationForm'] = $this->queryNodeValue($xpath,'gmd:presentationForm/gmd:CI_PresentationFormCode',$citation);
				}

                // Geographic Extend
                $xmlList = $xpath->query('gmd:extent/gmd:EX_Extent',$dataIdentification);
                foreach ($xmlList as $extent) {
                    $mdItem['MDGeographicDiscription']  = $this->queryNodeValue($xpath,'gmd:description/gco:CharacterString',$extent);

                    $mdItem['MDWestBound'] = $this->queryNodeValue($xpath,'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:westBoundLongitude/gco:Decimal',$extent);
                    $mdItem['MDEastBound'] = $this->queryNodeValue($xpath,'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:eastBoundLongitude/gco:Decimal',$extent);
                    $mdItem['MDSouthBound'] = $this->queryNodeValue($xpath,'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:southBoundLatitude/gco:Decimal',$extent);
                    $mdItem['MDNorthBound'] = $this->queryNodeValue($xpath,'gmd:geographicElement/gmd:EX_GeographicBoundingBox/gmd:northBoundLatitude/gco:Decimal',$extent);
                }

                // keywords
                $keywords = array();
                $xmlList = $xpath->query('gmd:descriptiveKeywords/gmd:MD_Keywords/gmd:keyword',$dataIdentification);
                foreach ($xmlList as $item) {
                    $keywords[]  = $this->queryNodeValue($xpath,'gco:CharacterString',$item);
                }
                $mdItem['MDKeywords:MDKeyword'] = $keywords;

                // iso resource contraints
                $mdResourceConstraints = array();
                $xmlList = $xpath->query('gmd:resourceConstraints/gmd:MD_LegalConstraints',$dataIdentification);
                foreach ($xmlList as $item) {
	                $mdResourceConstraint = array();

	                $node = $xpath->query('gmd:accessConstraints/gmd:MD_RestrictionCode',$item);
	                if ($node->length > 0) {
		                $mdResourceConstraint['accessConstraints']  = $node->item(0)->attributes->getNamedItem('codeListValue')->nodeValue;
	                }

	                $node = $xpath->query('gmd:useConstraints/gmd:MD_RestrictionCode',$item);
	                if ($node->length > 0) {
		                $mdResourceConstraint['useConstraints']  = $node->item(0)->attributes->getNamedItem('codeListValue')->nodeValue;
	                }
	                if ($this->queryNodeValue($xpath,'gmd:otherConstraints/gco:CharacterString',$item)) {
		                $mdResourceConstraint['otherConstraints']  = stripslashes($this->queryNodeValue($xpath,'gmd:otherConstraints/gco:CharacterString',$item));
	                }
	                if ($this->queryNodeValue($xpath,'gmd:useLimitation/gco:CharacterString',$item)) {
		                $mdResourceConstraint['useLimitation']  = stripslashes($this->queryNodeValue($xpath,'gmd:useLimitation/gco:CharacterString',$item));
	                }
	                $mdResourceConstraints[] = $mdResourceConstraint;
                }
                $mdItem['MDResourceConstraints:MDResourceConstraint'] = $mdResourceConstraints;

                // 2do: need to be tested
	            $mdResourceFormats = array();
                $xmlList = $xpath->query('gmd:resourceFormat/gmd:MD_Format',$dataIdentification);
                foreach ($xmlList as $item) {
	                $mdResourceFormat = array();
                    $mdResourceFormat['Name']  = $this->queryNodeValue($xpath,'gmd:name/gco:CharacterString',$item);
                    $mdResourceFormat['Version']  = $this->queryNodeValue($xpath,'gmd:version/gco:CharacterString',$item);
	                $mdResourceFormats[] = $mdResourceFormat;
                }
                $mdItem['MDResourceFormats:MDResourceFormat'] = $mdResourceFormats;

	            $mdItem['MDSpatialRepresentationType']  = $this->queryNodeValue($xpath,'gmd:spatialRepresentationType/gmd:MD_SpatialRepresentationTypeCode',$dataIdentification);

				$partyList = $xpath->query('gmd:pointOfContact/gmd:CI_ResponsibleParty',$dataIdentification);
				foreach ($partyList as $party) {
					$mdContact = array();

					if ($xpath->query('gmd:individualName/gco:CharacterString',$party)) {
						$mdContact['MDIndividualName']   = $xpath->query('gmd:individualName/gco:CharacterString',$party)->item(0)->nodeValue;
					}
					if ($xpath->query('gmd:organisationName/gco:CharacterString',$party)) {
						$mdContact['MDOrganisationName'] = $xpath->query('gmd:organisationName/gco:CharacterString',$party)->item(0)->nodeValue;
					}
					if ($xpath->query('gmd:positionName/gco:CharacterString',$party)) {
						$mdContact['MDPositionName']     = $xpath->query('gmd:positionName/gco:CharacterString',$party)->item(0)->nodeValue;
					}

					$contact = $xpath->query('gmd:contactInfo/gmd:CI_Contact',$party)->item(0);

					$mdVoice = array();
					$voiceNumberList = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:voice',$contact);
					foreach ($voiceNumberList as $voiceNumber) {
						$voiceNode = $xpath->query('gco:CharacterString',$voiceNumber);
						if ($voiceNode->length > 0) {
							$mdPhoneNumber = array();
							$mdPhoneNumber['Value'] = $xpath->query('gco:CharacterString',$voiceNumber)->item(0)->nodeValue;
							$mdVoice[] = $mdPhoneNumber;
						}
					}
					$mdContact['MDVoice:MDPhoneNumber'] = $mdVoice;

					// allows only 1 facsimile number
					if ($xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString',$contact)->item(0)) {
						$mdContact['MDFacsimile'] = $xpath->query('gmd:phone/gmd:CI_Telephone/gmd:facsimile/gco:CharacterString',$contact)->item(0)->nodeValue;
					}

					$addressList = $xpath->query('gmd:address/gmd:CI_Address',$contact);
					foreach ($addressList as $address) {
						if ($xpath->query('gmd:deliveryPoint/gco:CharacterString',$contact)->item(0)) {
							$mdContact['MDDeliveryPoint']         = $xpath->query('gmd:deliveryPoint/gco:CharacterString',$address)->item(0)->nodeValue;
						}
						if ($xpath->query('gmd:city/gco:CharacterString',$contact)->item(0)) {
							$mdContact['MDCity']                  = $xpath->query('gmd:city/gco:CharacterString',$address)->item(0)->nodeValue;
						}
						if ($xpath->query('gmd:administrativeArea/gco:CharacterString',$contact)->item(0)) {
							$mdContact['MDAdministrativeArea']    = $xpath->query('gmd:administrativeArea/gco:CharacterString',$address)->item(0)->nodeValue;
						}
						if ($xpath->query('gmd:postalCode/gco:CharacterString',$contact)->item(0)) {
							$mdContact['MDPostalCode']            = $xpath->query('gmd:postalCode/gco:CharacterString',$address)->item(0)->nodeValue;
						}
						if ($xpath->query('gmd:country/gco:CharacterString',$contact)->item(0)) {
							$mdContact['MDCountry']               = $xpath->query('gmd:country/gco:CharacterString',$address)->item(0)->nodeValue;
						}
						if ($xpath->query('gmd:electronicMailAddress/gco:CharacterString',$contact)->item(0)) {
							$mdContact['MDElectronicMailAddress'] = $xpath->query('gmd:electronicMailAddress/gco:CharacterString',$address)->item(0)->nodeValue;
						}
					}

					// add mdContact object to the contact relationship object
					$mdItem['PointOfContacts:MDContact'] = $mdContact;
				}

            }
            $mdArray[] = $mdItem;
//            echo "<pre>";
//            print_r($mdArray);
//            echo "</pre>";
//			die();
        }
        return array($numberOfRecordsMatched, $numberOfRecordsReturned, $nextRecord, $mdArray);

    }

    protected function queryNodeValue($xpath, $path, $field) {
        if ($xpath->query($path,$field)->length > 0) {
            return $xpath->query($path,$field)->item(0)->nodeValue;
        }
        return null;
    }

    protected function get_GCOCharacterString($item, $namespace, $field) {
        $element = $item->getElementsByTagNameNS($namespace, $field);
        if ($element->length > 0) {
            $node = $element->item(0)->getElementsByTagNameNS('http://www.isotc211.org/2005/gco', 'CharacterString');
            return $node->item(0)->nodeValue;
        }
        return null;
    }

}