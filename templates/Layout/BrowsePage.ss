<div class="typography">
	$Content
</div>

<div class="typography">
	<!-- Search result -->
	<% if SearchResultItems %>
		<div class='results_searchTerm'>
			<div>
				
				
				<% if IsFirstResultPage %>
					Prev
				<% else %>
					<a href="{$Link}giveresult/$PrevStart/$searchTerm/">Prev</a>
				<% end_if %> | 
				Page $onpage of $ofpages | 
				<% if IsLastResultPage %>
					Last
				<% else %>
					<a href="{$Link}giveresult/$NextStart/$searchTerm/">Next</a>
				<% end_if %>
			
			</div>
		</div>
	
		<div class='results'>
		<!-- Search result items -->
		<% control SearchResultItems %>
			<% include RecordSummaryList %>
		<% end_control %>
	<% end_if %>

	<!-- Record result -->
	<% if SearchRecord %>
		<div class='record'>
		<% control SearchRecord %>
			<% include RecordFull %>
		<% end_control %>
	<% end_if %>


	<!-- Messages results -->
	<% if Message %>
	<div class='$MessageType'>$Message</div>
	<% end_if %>
	<a title="Go to the top of the page" href="#Top">Top</a>

</div>
