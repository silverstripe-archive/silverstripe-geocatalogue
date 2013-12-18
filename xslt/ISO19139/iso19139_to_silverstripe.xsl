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
	
<xsl:template match="/">
	<xsl:for-each select="csw:GetRecordsResponse">
		<xsl:for-each select="csw:SearchResults">
			$numberOfRecordsMatched = '<xsl:value-of select="@numberOfRecordsMatched"/>';
			$numberOfRecordsReturned = '<xsl:value-of select="@numberOfRecordsReturned"/>';
			$nextRecord = '<xsl:value-of select="@nextRecord"/>';	
			<xsl:call-template name="MD_Metadata"> 
				<xsl:with-param name="Metadata" select="gmd:MD_Metadata"/>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:for-each>
</xsl:template>

<xsl:template name="MD_Metadata">
	<xsl:param name="Metadata" />
	<xsl:for-each select="$Metadata">
		$mdItem = array();
		$mdItem['fileIdentifier'] = '<xsl:value-of select="gmd:fileIdentifier/gco:CharacterString"/>';
		$mdItem['metadataStandardName'] = '<xsl:value-of select="gmd:metadataStandardName/gco:CharacterString"/>';
		$mdItem['metadataStandardVersion'] = '<xsl:value-of select="gmd:metadataStandardVersion/gco:CharacterString"/>';
		$mdItem['parentIdentifier'] = '<xsl:value-of select="gmd:parentIdentifier/gco:CharacterString"/>';
		$mdItem['hierarchyLevel'] = '<xsl:value-of select="gmd:hierarchyLevel/gmd:MD_ScopeCode"/>';
    $mdItem['hierarchyLevelName'] = '<xsl:value-of select="gmd:hierarchyLevelName/gco:CharacterString"/>'; 
    <xsl:for-each select="gmd:identificationInfo">
      <xsl:for-each select="gmd:MD_DataIdentification">
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
				$mdItem['MDTitle'] = '<xsl:value-of select="gmd:citation/gmd:CI_Citation/gmd:title/gco:CharacterString"/>';
				$mdItem['MDAbstract'] = '<xsl:value-of select="gmd:abstract/gco:CharacterString"/>';
				$mdItem['MDTopicCategory'] = '<xsl:value-of select="gmd:topicCategory/gco:CharacterString"/>';
			</xsl:for-each>
		</xsl:for-each>	
		$mdArray[] = $mdItem;
	</xsl:for-each>	
</xsl:template>
</xsl:stylesheet>
