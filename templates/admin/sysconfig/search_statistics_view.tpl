<div style="position: relative;width: 100%;height:100%">
	<div style="height: 40px;white-space: nowrap;overflow: auto">
		<select id="ss_days_number" class="cmf-skinned-select cmf-skinned-text">
			{html_options options=$ns.days_variant  selected=$ns.daysNumber}
		</select>
	</div>
		
	<div style="overflow: auto;position: absolute;top: 50px;right: 10px;bottom: 10px;left: 10px;">
	<table style="border-collapse:collapse;">
			<thead>
			<th>Search Text</th>
			<th>Count</th>
			</thead>
			<tbody>
				{foreach from=$ns.groupedSearchStatistics key=searchText item=count}
					<tr>
						<td>
							{$searchText}
						</td>
						<td>
							{$count}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>