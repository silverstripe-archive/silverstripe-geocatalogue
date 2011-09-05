<div  class='result_item'>

	<h2>$MDTitle</h2>

	<div class="row">
		<div class="header">Download:</div>
		<div class="item"><a href='$Top.URLSegment/dogetrecordbyid/$fileIdentifier/xml'>click here to download Metadata XML</a></div>
	</div>

	<div class="row">
		<div class="header">ID:</div>
		<div class="item">$fileIdentifier</div>
	</div>
	
	<div class="row">
		<div class="header">Created:</div>
		<div class="item">$dateStamp</div>
	</div>
	
	<div class="row">
		<div class="header">Abstract:</div>
		<div class="item">
			<% if MDAbstract %>
				$MDAbstract
			<% else %>
				(not available)
			<% end_if %>
		</div>
	</div>	

	<div class="row">
		<div class="header">Language:</div>
		<div class="item">$MDLanguage</div>
	</div>
	
	<div class="row">
		<div class="header">Date Time:</div>
		<div class="item">$MDDateTime</div>
	</div>	

	<div class="row">
		<div class="header">Date Type:</div>
		<div class="item">$DateTypeNice</div>
	</div>	

	<div class="row">
		<div class="header">Topic Category:</div>
		<div class="item">$TopicCategoryNice</div>
	</div>
	
	<div class="row">
		<div class="header">Bounding Box:</div>
		<div class="item">
			<div class="">North: $MDNorthBound</div>
			<div class="">West: $MDWestBound</div>
			<div class="">East: $MDEastBound</div>
			<div class="">South: $MDSouthBound</div>
			<div class="">Place: $PlaceName</div>
		</div>
	</div>
	
	<!-- Record result -->
	<% if MDContacts %>
		<div class='purpose'>
		<div class="header">Contact:</div>

		<% control MDContacts %>
		<div class="item">
			Name: $MDIndividualName <br />
			Organisation: $MDOrganisationName <br />
			Position: $MDPositionName <br />
		</div>
		<% end_control %>
		</div>
	<% end_if %>
	
	<% if MDResourceConstraints %>
		<div class='purpose'>
		<div class="header">Resource Constraints:</div>

		<% control MDResourceConstraints %>
		<div class="item">
			accessConstraints: $AccessConstraintsNice <br />
			useConstraints: $UseConstraintsNice <br />
			otherConstraints: $otherConstraints <br />
		</div>
		<% end_control %>
		</div>
	<% end_if %>	
</div>
