<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
	xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:gmd="http://www.isotc211.org/2005/gmd" 
	xmlns:gml="http://www.opengis.net/gml" 
	xmlns:gts="http://www.isotc211.org/2005/gts" 
	xmlns:gco="http://www.isotc211.org/2005/gco" 
	xmlns:geonet="http://www.fao.org/geonetwork">
	
<xsl:template name="MD_Metadata">
	<xsl:param name="Metadata" />
	<xsl:for-each select="$Metadata">
		$mdItem = array();
		$mdItem['fileIdentifier']          = '<xsl:value-of select="gmd:fileIdentifier/gco:CharacterString"/>';
		$mdItem['dateTimeStamp']           = '<xsl:value-of select="gmd:dateStamp/gco:DateTime"/>'; // metadata creation
		$mdItem['dateStamp']               = '<xsl:value-of select="gmd:dateStamp/gco:Date"/>'; // metadata creation
		$mdItem['metadataStandardName']    = '<xsl:value-of select="gmd:metadataStandardName/gco:CharacterString"/>';
		$mdItem['metadataStandardVersion'] = '<xsl:value-of select="gmd:metadataStandardVersion/gco:CharacterString"/>';
		$mdItem['parentIdentifier']        = '<xsl:value-of select="gmd:parentIdentifier/gco:CharacterString"/>'; 
		
		$mdHierarchyLevel = array();
		<xsl:for-each select="gmd:hierarchyLevel/gmd:MD_ScopeCode">
			$mdHierarchyLevelItem = array();
			$mdHierarchyLevelItem['Value'] = '<xsl:value-of select="@codeListValue"/>';
			$mdHierarchyLevel[] = $mdHierarchyLevelItem;
		</xsl:for-each>
		$mdItem['MDHierarchyLevel:MDHierarchyLevel'] = $mdHierarchyLevel;
		
		$mdHierarchyLevelName = array();
		<xsl:for-each select="gmd:hierarchyLevelName">
			$mdHierarchyLevelNameItem = array();
			$mdHierarchyLevelNameItem['Value'] = '<xsl:value-of select="gco:CharacterString"/>';
			$mdHierarchyLevelName[] = $mdHierarchyLevelNameItem;
		</xsl:for-each>
		$mdItem['MDHierarchyLevelName:MDHierarchyLevelName'] = $mdHierarchyLevelName;

		<xsl:for-each select="gmd:contact/gmd:CI_ResponsibleParty">
			$mdContact = array();
			$mdContact['MDIndividualName']   = '<xsl:value-of select="gmd:individualName/gco:CharacterString"/>';   // (3) Metadata Contact Role
			$mdContact['MDOrganisationName'] = '<xsl:value-of select="gmd:organisationName/gco:CharacterString"/>'; // (3) Metadata Contact Role
			$mdContact['MDPositionName']     = '<xsl:value-of select="gmd:positionName/gco:CharacterString"/>';     // (3) Metadata Contact Role

			$mdVoice = array();
			$mdElectronicMailAddress = array();
			<xsl:for-each select="gmd:contactInfo/gmd:CI_Contact">
				
				<xsl:for-each select="gmd:phone/gmd:CI_Telephone">
					<xsl:for-each select="gmd:voice">
						$mdPhoneNumber = array();
						$mdPhoneNumber['Value'] = '<xsl:value-of select="gco:CharacterString"/>'; 
						$mdVoice[] = $mdPhoneNumber;
					</xsl:for-each>
	  				$mdContact['MDFacsimile'] = '<xsl:value-of select="gmd:facsimile/gco:CharacterString"/>';
				</xsl:for-each>
				$mdContact['MDVoice:MDPhoneNumber'] = $mdVoice; 
			
				<xsl:for-each select="gmd:address/gmd:CI_Address">
					$mdContact['MDDeliveryPoint'] = '<xsl:value-of select="gmd:deliveryPoint/gco:CharacterString"/>'; 
					$mdContact['MDCity'] = '<xsl:value-of select="gmd:city/gco:CharacterString"/>';
					$mdContact['MDAdministrativeArea'] = '<xsl:value-of select="gmd:administrativeArea/gco:CharacterString"/>';
					$mdContact['MDPostalCode'] = '<xsl:value-of select="gmd:postalCode/gco:CharacterString"/>';
					$mdContact['MDCountry'] = '<xsl:value-of select="gmd:country/gco:CharacterString"/>';
					
					$mdElectronicMailAddresses = array();
					<xsl:for-each select="gmd:electronicMailAddress">
						$mdElectronicMailAddress = array();
						$mdElectronicMailAddress['Value'] = '<xsl:value-of select="gco:CharacterString"/>'; 
						$mdElectronicMailAddresses[] = $mdElectronicMailAddress;
					</xsl:for-each>
					$mdContact['MDElectronicMailAddress:MDEmail'] = $mdElectronicMailAddresses;
				</xsl:for-each>
			</xsl:for-each>
			$mdItem['MDContacts:MDContact'] = $mdContact;
		</xsl:for-each>

		$ciOnlineResources = array();
		$mdResourceFormats = array();
		<xsl:for-each select="gmd:distributionInfo">
			<xsl:for-each select="gmd:MD_Distribution">
				
				<xsl:for-each select="gmd:distributionFormat">
					$mdResourceFormat = array();
					<xsl:for-each select="gmd:MD_Format">				
						$mdResourceFormat['Name']    = '<xsl:value-of select="gmd:name/gco:CharacterString"/>';    // Resource Format
						$mdResourceFormat['Version'] = '<xsl:value-of select="gmd:version/gco:CharacterString"/>'; // Resource Version
					</xsl:for-each>
					$mdResourceFormats[] = $mdResourceFormat;
				</xsl:for-each>
				
				<xsl:for-each select="gmd:transferOptions">
					<xsl:for-each select="gmd:MD_DigitalTransferOptions">
						<xsl:for-each select="gmd:onLine">
							<xsl:for-each select="gmd:CI_OnlineResource">
								$ciOnlineResource = array();
								$ciOnlineResource['CIOnlineLinkage']    = '<xsl:value-of select="gmd:linkage/gmd:URL"/>';    // Resource Format
								$ciOnlineResource['CIOnlineProtocol'] = '<xsl:value-of select="gmd:protocol/gco:CharacterString"/>'; // Resource Version
								$ciOnlineResource['CIOnlineName'] = '<xsl:value-of select="gmd:name/gco:CharacterString"/>'; // Resource Version
								$ciOnlineResource['CIOnlineDescription'] = '<xsl:value-of select="gmd:description/gco:CharacterString"/>'; // Resource Version
								<xsl:for-each select="gmd:function/gmd:CI_OnLineFunctionCode">
									$ciOnlineResource['CIOnlineFunction'] = '<xsl:value-of select="@codeListValue"/>';        // (8) Reference Date Type
								</xsl:for-each>
								$ciOnlineResources[] = $ciOnlineResource;
							</xsl:for-each>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:for-each>
			</xsl:for-each>
		</xsl:for-each>
		$mdItem['MDResourceFormats:MDResourceFormat'] = $mdResourceFormats;
		$mdItem['CIOnlineResources:CIOnlineResource'] = $ciOnlineResources;

		<xsl:for-each select="gmd:identificationInfo">
			<xsl:for-each select="gmd:MD_DataIdentification">
				$mdItem['MDPurpose']       = '<xsl:value-of select="gmd:purpose/gco:CharacterString"/>';
				$mdItem['MDAbstract']      = '<xsl:value-of select="gmd:abstract/gco:CharacterString"/>';                 // (6) Abstract
				$mdItem['MDLanguage']      = '<xsl:value-of select="gmd:language/gco:CharacterString"/>';                 // (9) Language
				$mdTopicCategory = array();
				<xsl:for-each select="gmd:topicCategory">
					$mdTopicCategoryItem = array();
					$mdTopicCategoryItem['Value'] = '<xsl:value-of select="gmd:MD_TopicCategoryCode"/>';            // (10) Topic Category
					$mdTopicCategory[] = $mdTopicCategoryItem;
				</xsl:for-each>
				$mdItem['MDTopicCategory:MDTopicCategory'] = $mdTopicCategory;

				<xsl:for-each select="gmd:citation">
					<xsl:for-each select="gmd:CI_Citation">
						
						$mdItem['MDTitle'] = '<xsl:value-of select="gmd:title/gco:CharacterString"/>';                    // (5) Title
						$mdItem['MDEdition'] = '<xsl:value-of select="gmd:edition/gco:CharacterString"/>';

						$mdCitationDates=array();
						<xsl:for-each select="gmd:date">
							<xsl:for-each select="gmd:CI_Date">
								$mdCitationDate=array();
								$mdCitationDate['MDDateTime'] = '<xsl:value-of select="gmd:date/gco:DateTime"/>';              // (7) Reference Date Stamp
								$mdCitationDate['MDDate'] = '<xsl:value-of select="gmd:date/gco:Date"/>';                      // (7) Reference Date only (no time)

								<xsl:for-each select="gmd:dateType/gmd:CI_DateTypeCode">
									$mdCitationDate['MDDateType'] = '<xsl:value-of select="@codeListValue"/>';        // (8) Reference Date Type
								</xsl:for-each>
								$mdCitationDates[]=$mdCitationDate;
							</xsl:for-each>
						</xsl:for-each>
		        		$mdItem['MDCitationDates:MDCitationDate'] = $mdCitationDates;
						
						<xsl:for-each select="gmd:presentationForm/gmd:CI_PresentationFormCode">
							$mdItem['MDPresentationForm'] = '<xsl:value-of select="@codeListValue"/>';
						</xsl:for-each>
					</xsl:for-each>
				</xsl:for-each>
				
				<xsl:for-each select="gmd:extent">
					<xsl:for-each select="gmd:EX_Extent">
						
						$mdItem['MDGeographicDiscription']  = '<xsl:value-of select="gmd:description/gco:CharacterString"/>'; 			// core (suggestion)

						<xsl:for-each select="gmd:geographicElement">
							<xsl:for-each select="gmd:EX_GeographicBoundingBox">
							$mdItem['MDWestBound']  = '<xsl:value-of select="gmd:westBoundLongitude/gco:Decimal"/>'; // (11) Bounding Box Coordinates
							$mdItem['MDEastBound']  = '<xsl:value-of select="gmd:eastBoundLongitude/gco:Decimal"/>'; // (11) Bounding Box Coordinates
							$mdItem['MDSouthBound'] = '<xsl:value-of select="gmd:southBoundLatitude/gco:Decimal"/>'; // (11) Bounding Box Coordinates
							$mdItem['MDNorthBound'] = '<xsl:value-of select="gmd:northBoundLatitude/gco:Decimal"/>'; // (11) Bounding Box Coordinates
							</xsl:for-each>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:for-each>

				$mdItem['MDKeywords'] = array();
				<xsl:for-each select="gmd:descriptiveKeywords/gmd:MD_Keywords">
					<xsl:for-each select="gmd:keyword">
						$mdItem['MDKeywords:MDKeyword'][] = '<xsl:value-of select="gco:CharacterString"/>';
					</xsl:for-each>
				</xsl:for-each>
				
				$mdResourceConstraints = array();
				<xsl:for-each select="gmd:resourceConstraints/gmd:MD_LegalConstraints">						
					$mdResourceConstraint = array();
					<xsl:for-each select="gmd:accessConstraints/gmd:MD_RestrictionCode">
					$mdResourceConstraint['accessConstraints'] = '<xsl:value-of select="@codeListValue"/>';
					</xsl:for-each>
					<xsl:for-each select="gmd:useConstraints/gmd:MD_RestrictionCode">
					$mdResourceConstraint['useConstraints'] = '<xsl:value-of select="@codeListValue"/>';
					</xsl:for-each>
					<xsl:for-each select="gmd:otherConstraints">
					$mdResourceConstraint['otherConstraints'] = '<xsl:value-of select="gco:CharacterString"/>';
					</xsl:for-each>
					<xsl:for-each select="gmd:useLimitation">
					$mdResourceConstraint['useLimitation'] = '<xsl:value-of select="gco:CharacterString"/>';
					</xsl:for-each>
					$mdResourceConstraints[] = $mdResourceConstraint;
				</xsl:for-each>
				$mdItem['MDResourceConstraints:MDResourceConstraint'] = $mdResourceConstraints;

				<xsl:for-each select="gmd:spatialRepresentationType/gmd:MD_SpatialRepresentationTypeCode">
				$mdItem['MDSpatialRepresentationType'] = '<xsl:value-of select="@codeListValue"/>';
				</xsl:for-each>
			</xsl:for-each>
			$mdArray[] = $mdItem;
		</xsl:for-each>	
	</xsl:for-each>	
</xsl:template>

</xsl:stylesheet>
