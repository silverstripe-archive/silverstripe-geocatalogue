<?xml version="1.0"?>
<xsl:stylesheet version="1.0"
	xmlns:csw="http://www.opengis.net/cat/csw/2.0.2" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:dc="http://purl.org/dc/elements/1.1/" 
	xmlns:dct="http://purl.org/dc/terms/"
	xmlns:geonet="http://www.fao.org/geonetwork">
	
<xsl:template match="/">
	<xsl:for-each select="csw:GetRecordsResponse">
		<xsl:for-each select="csw:SearchResults">
			$numberOfRecordsMatched = trim('<xsl:value-of select="@numberOfRecordsMatched" />');	
			$numberOfRecordsReturned = trim('<xsl:value-of select="@numberOfRecordsReturned" />');	
			$nextRecord = trim('<xsl:value-of select="@nextRecord" />');	
			<xsl:for-each select="csw:SummaryRecord">
				$mdItem = array();
				$mdItem['fileIdentifier'] = trim('<xsl:value-of select="dc:identifier"/>');
				$mdItem['metadataStandardName'] = 'DublinCore';
				$mdItem['metadataStandardVersion'] = '';

				$mdItem['MDTitle'] = trim('<xsl:value-of select="dc:title"/>');
				$mdItem['MDAbstract'] = trim('<xsl:value-of select="dct:abstract"/>');
				$mdItem['MDTopicCategory'] = '';

				$mdItem['MDKeywords'] = array();
				<xsl:for-each select="dc:subject">
					$mdItem['MDKeywords'][] = trim("<xsl:value-of select="."/>");
				</xsl:for-each>
				$mdArray[] = $mdItem;
			</xsl:for-each>
		</xsl:for-each>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>